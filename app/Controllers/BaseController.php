<?php

namespace App\Controllers;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 *
 * @package CodeIgniter
 */

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use \Firebase\JWT\JWT;
JWT::$leeway = 60;
use App\Helpers\Constant;

class BaseController extends Controller
{

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['form', 'Common'];
    
    public $session;
    protected $blank;
    protected $private;
    protected $public;
    protected $uri;
    protected $db;

    /**
     * Constructor.
     */
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        //--------------------------------------------------------------------
        // Preload any models, libraries, etc, here.
        //--------------------------------------------------------------------
        // E.g.:
        // $this->session = \Config\Services::session();
        date_default_timezone_set("Asia/Jakarta");
        $this->secret_key = 'absensi-secret-key';
        $this->constant = new Constant();
        
        $this->session  = \Config\Services::session();
        $this->blank    = env('theme.blank');
        $this->private  = env('theme.private');
        $this->public   = env('theme.public');
        $this->uri      = service('uri');
    }


    public function isValidToken()
    {
        $secret_key = $this->secret_key;

        $token = null;
        $token = $this->request->getServer('HTTP_AUTHORIZATION');

        if ($token) {

            try {

                $decoded = JWT::decode($token, $secret_key, array('HS256'));

                // Access is granted. Add code of the operation here
                if ($decoded) {
                    // response true
                    $output = [
                        'message' => 'Access granted'
                    ];
                    return true;
                }
            } catch (\Exception $e) {

                $output = [
                    'message' => 'Access denied',
                    "error" => $e->getMessage()
                ];

                return false;
            }
        }
    }

    public function getParseToken() {
        $secret_key = $this->secret_key;

        $token = null;
        $token = $this->request->getServer('HTTP_AUTHORIZATION');

        if ($token) {

            try {

                $decoded = JWT::decode($token, $secret_key, array('HS256'));

                // Access is granted. Add code of the operation here
                if ($decoded) {
                    // response true
                    $output = [
                        'message' => 'Access granted'
                    ];
                    return $decoded;
                }
            } catch (\Exception $e) {

                $output = [
                    'message' => 'Access denied',
                    "error" => $e->getMessage()
                ];

                return $output;
            }
        }
    }
}
