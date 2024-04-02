<?php
/*
** Master Controller
*
*/

declare(strict_types=1);

namespace app\core;

use app\models\User;

class Controller
{

    protected $requestData;
    protected $isApiRequest;

    public function __construct()
    {
        //? handle cors
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        //? Parse JSON data from request body
        $this->requestData = json_decode(file_get_contents("php://input"), true);

        //? Check if it's an API request
        $this->isApiRequest = isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;;
    }


    // Check if the request contains a valid token
    protected function isAuthenticated()
    {
        //? Get token from request headers
        $token = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : null;

        //? if token is empty
        if (is_null($token))
            return sendJsonResponse(
                'Unathorized',
                'Authentication is required to access this resource.',
                401
            );


        //? turn to string to array and get token
        $token = explode(' ', $token)[1];

        //? Validate the token (implement this function)
        return $this->validateToken($token); //? Returns true if token is valid
    }


    //? validate user token return ture or false
    private function validateToken(string $token): bool
    {

        $userM = new User();
        $verifyToken = $userM->where('token', $token);

        //? check if token found and current time is less than the token expiry time.
        if (count($verifyToken) > 0 && time() < strtotime($verifyToken[0]->token_expires_at)) return true;

        return false;
    }


    //? method to load view pages
    public function view(string $view, array $data = []): void
    {
        extract($data);

        if (file_exists("../app/views/" . $view . ".php")) {
            require("../app/views/" . $view . ".php");
        } else {
            require("../app/views/404.php");
        }
    }

    //? method to load a contronller
    public function load_model($model)
    {
        if (file_exists("../app/models/" . ucwords($model) . ".php")) {
            require("../app/models/" . ucwords($model) . ".php");
            return $model = new $model();
        }
        return false;
    }


    //? method to redirect to a specific route
    public function redirect($link)
    {
        header('Location: ' . URLROOT . "/" . trim($link, "/"));
        die();
    }

    //? method to get a currecnt class name
    public function controller_name()
    {
        return get_class($this);
    }
}
