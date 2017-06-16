<?php
/**
 * @file    : /app/core/controllers/register.php
 * @author  : Leonid Vinikov <czf.leo123@gmail.com>
 * @todo    :
 */

namespace Controllers;

use Symfony\Component\HttpFoundation\Request;

use Core;
use Models;
use Library;

class Register extends Controller
{
    /**
     * The instance of auth library
     *
     * @var \Library\Auth
     */
    protected $auth;

    /**
     * The instance of user model
     *
     * @var \Models\User
     */
    protected $user;

    /**
     * The instance of Attempt model
     *
     * @var \Models\Attempt
     */
    protected $attempt;

    /**
     * The instance of Config model
     *
     * @var \Models\Config
     */
    protected $config;

    /**
     * The instance of logger
     *
     * @var \Monolog\Logger
     */
    protected $logger;

    /**
     * Initialize the controller and prepare the dependencies
     *
     * @param Logger $logger
     * @param User $user
     * @param Attempt $attempt
     * @param Config $config
     * @param Auth $auth
     */
    public function __construct(Logger $logger, User $user, Attempt $attempt, Config $config, Auth $auth)
    {
        $this->logger = $logger;

        $this->user = $user;
        $this->attempt = $attempt;
        $this->config = $config;

        $this->auth = $auth;
    }

    /**
     * Attempts to register an user
     *
     * @return string|array
     */
    public function register()
    {
        $request = $request->get();
        
        $email = $request->get('email');
        $password = $request->get('password');
        $captcha = $request->get('captcha');

        $this->logger->debug("email: `$email`, password: `$password`");

        $ip = $this->auth->getIp();
        $block_status = $this->attempt->getBlockStatus($ip);

        $this->logger->debug("ip: `$ip`, block_status: `$block_status`");

        if ($block_status == 'block') {
            return "Your ip have been blocked for a while";
        }

        $validEmail = $this->auth->validateEmail($email);

        if ($validEmail->error) {
            $this->attempt->add($ip);
            return $validEmail->message;
        }

        if ($this->user->isEmailTaken($email)) {
            $this->attempt->add($ip);
            return 'The email is already taken';
        }

        $validPassword = $this->auth->validatePassword($password);

        if ($validPassword->error) {
            return $validPassword->message;
        }

        if (! $this->auth->checkCaptcha($captcha)) {
            return ['code' => 'verify'];
        }

        $user = new User;

        $user->email = $email;
        $user->password = password_hash($password, PASSWORD_BCRYPT, ['cost' => $this->config->get('bcrypt_cost')]);
        $user->isactive = true;

        if ($user->save()) {
            return ['code' => 'success'];
        }

        return "system error";
    }
} // EOF register.php