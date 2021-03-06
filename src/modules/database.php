<?php
/**
 * @file: modules/database.php
 * @author: Leonid Vinikov <czf.leo123@gmail.com>
 */

namespace Modules;

class Database
{
    /**
     * Instances Count\
     *
     * @var int
     */
    private static $instances = 0;

    /**
     * Connection Factory
     *
     * @var \React\Mysql\Factory
     */
    private $factory;

    /**
     * Connection String
     *
     * @var string
     */
    private $uri;

    /**
     * Loop Handler
     *
     * @var \React\EventLoop\StreamSelectLoop
     */
    private $loop;

    /**
     * Instance of Logger Module
     *
     * @var \Modules\Logger
     */
    private $logger;

    /**
     * Database Connection
     *
     * @var \React\MySQL\ConnectionInterface
     */
    private $connection;

    /**
     * Function __construct() : Construct Database Module
     *
     * @param string                                $uri
     * @param \Modules\Logger                       $logger
     * @param \React\EventLoop\StreamSelectLoop     $loop
     * @param bool                                  $autoLoad
     */
    public function __construct(string $uri, \Modules\Logger $logger = null, \React\EventLoop\StreamSelectLoop $loop = null, $autoLoad = true)
    {
        if (!$loop) {
            $loop = \React\EventLoop\Factory::create();
        }

        $this->factory = new \React\MySQL\Factory($loop);

        if (!$logger) {
            $logger = new \Modules\Logger(self::class, \Services\Config::get('logger')->module_database);
        }

        $this->uri      = $uri;
        $this->loop     = $loop;
        $this->logger   = $logger;

        if ($autoLoad) {
            $this->initialize();
        }
    }

    /**
     * Function __destruct(): Destruct
     */
    public function __destruct()
    {
        --self::$instances;

        $this->logger->debug('destroying this instance, now count: `' . self::$instances . '`');
    }

    /**
     * Function initialize() : Initialize Database Module
     *
     * @return void
     */
    private function initialize()
    {
        $this->logger->debug("Module loaded");

        // pinging db
        $this->loop->addPeriodicTimer(60, function () {
            $this->idleRunProc();
        });

        ++self::$instances;
    }

    /**
     * Function idleRunProc() : This function is proc, it runs idlie each x time.
     * used as database connection checker. (rewrite-later)
     *
     * @return void
     */
    private function idleRunProc()
    {
        // handle connection timeout due inactive.

        $token = $this->logger->callBackSet(self::class, "ping", uniqid());
        $ping  = $this->connection->ping();

        $ping->then(function () use ($token) {
            $this->logger->callBackFire($token, 'connected');
        }, function (\Exception $e) use ($token) {
            $this->logger->callBackFire($token, $e->getMessage());

            $this->connection = null;
        });

        $this->logger->callBackDeclare($token);
    }

    /**
     * Function Connect() : Creates a new connection
     *
     * @param callable $callback
     *
     * @return void
     */
    public function connect(callable $callback)
    {
        $this->logger->debug("uri: `{$this->uri}`");

        $promise = $this->factory->createConnection($this->uri);

        $promise->then(function (\React\MySQL\ConnectionInterface $connection) use ($callback) {
            $this->connection = $connection;

            $callback(null);
        }, function (\Exception $error) use ($callback) {
            $this->connection = null;

            $callback($error);
        });
    }

    /**
     * Function close()
     *
     * @param callable $callback
     *
     * @return void
     */
    public function close(callable $callback = null)
    {
        $this->connection->close($callback);
    }

    /**
     * Function query() : Run Promise Query
     *
     * @param string $query
     *
     * @return mixed
     */
    public function query(string $query)
    {
        $deferred = new \React\Promise\Deferred();

        $token = null;

        if (\Services\Config::get('logger')->module_database == \Config\Module_Database_Logs::DEEP) {
            $token = $this->logger->callBackSet("query", $query, uniqid());
        }

        $this->connection->query($query)->then(
            function (\React\MySQL\QueryResult $command) use ($deferred, $token) {
                if ($token) {
                    $this->logger->debugJson($command->resultRows, 'result');
                    $this->logger->callBackFire($token, $command->resultRows, 'halt-on-null');
                }

                $deferred->resolve($command);
            },
            function (\Exception $error) use ($deferred) {
                $deferred->reject($error);
            }
        );

        if ($token) $this->logger->callBackDeclare($token);

        return $deferred->promise();
    }

    /**
     * Function queryAwait() : Run query and wait for data to be returned.
     *
     * @param string $query
     * 
     * @return mixed
     */
    public function queryAwait($query)
    {
        // # TODO: add debug level
        $this->logger->debug($query, ['depth' => 3]);

        $request = $this->query($query);

        // i have no time
        $response = \Clue\React\Block\await($request, $this->getLoop());

        return $response;
    }

    /**
     * Function fakeLongQuery() : Fakes a query that takes time to to proceed
     *
     * @return mixed
     */
    public function fakeLongQuery(int $timeout = 20)
    {
        return $this->queryAwait("SELECT SLEEP({$timeout});");
    }

    /**
     * Function getLoop() : Get working loop
     *
     * @return \React\EventLoop\StreamSelectLoop
     */
    public function getLoop()
    {
        return $this->loop;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isConnected()
    {
        if ($this->connection) return true;

        return false;
    }
} // EOF modules/database.php
