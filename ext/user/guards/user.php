<?php
/**
 * @file: ext/user/guards/User.php
 * @author: Leonid Vinikov <czf.leo123@gmail.com>
 */

namespace Guards;

class User implements \Interfaces\Guard\Run
{
    /**
     * Instance of Ip Module
     *
     * @var \Modules\Ip
     */
    private $ip;

    /**
     * Instance of Object Core
     *
     * @var \Core\OObject
     */
    private $object;

    /**
     * Instance of Logger Moudle
     *
     * @var \Modules\Logger
     */
    private $logger;

    /**
     * Current session array from database.
     *
     * @var array
     */
    private $session;

    /**
     * Instance of Auth Service
     *
     * @param \Services\Auth $auth
     */
    private $auth;

    /**
     * Function __construct() : Construct User Guard
     *
     * @param \Modules\Ip    $ip
     * @param \Core\OObject  $object
     * @param \Services\Auth $auth
     */
    public function __construct(\Modules\Ip $ip, \Core\OObject $object, \Services\Auth $auth)
    {
        $this->ip     = $ip;
        $this->object = $object;
        $this->auth   = $auth;

        $this->initialize();
    }

    /**
     * Function initialize() : Initialize
     *
     * @todo add logger swtich
     *
     * @return void
     */
    public function initialize()
    {
        $this->logger = new \Modules\Logger(self::class);
    }

    /**
     * Function run() : Executed when client request the guard.
     *
     * @throws Exception
     *
     * @return bool
     */
    public function run()
    {
        $return = false;

        $error   = null;
        $check   = null;
        $session = null;
        $hash    = $this->object->get("hash");

        // # NOTICE: $this->session reference
        if ((strlen($hash) == 40) && ($check = $this->auth->check((string)$this->ip, $hash, $error, $session))
            == \Services\Auth\Model\EnumCheckReturn::SUCCESS
        ) {
            $return = true;

            // # NOTICE: save session here
            $this->session = $session;
        }

        $debug = new \stdClass();

        $debug->ip      = (string)$this->ip;
        $debug->hash    = $hash;
        $debug->check   = (int)$check;
        $debug->error   = $error;
        $debug->session = $this->session;
        $debug->return  = $return;

        $this->logger->debugJson($debug);

        return $return;
    }

    /**
     * Function getSession() : this function return $session array that resolved from database.
     *
     * @return void
     */
    public function getSession()
    {
        return $this->session;
    }
} // EOF ext/user/guards/user.php