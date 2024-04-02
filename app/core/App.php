<?php
/*
** Routing Loader or class Loader
*
*/

declare(strict_types=1);

namespace app\core;

class App
{

    private $controller = '';
    private $method = '';
    private $params = [];

    public function __construct()
    {
        $url = $this->getUrl();
        show($url);
        die;

        //? check if url is empty
        if (empty($url)) {
            sendJsonResponse('error', 'API endpoint not found', 404);
        }

        //? Handle Api controller
        if (isset($url) && $url != []) {
            if (file_exists("../app/controllers/" . ucwords($url[0]) . ".php")) {
                $this->controller = ucwords($url[0]);
                unset($url[0]);
            } else {
                sendJsonResponse('error', 'API endpoint not found', 404);
            }
        }


        require "../app/controllers/" . $this->controller . ".php";
        $this->controller = new $this->controller();


        //? Handle Api Method
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = strtolower($url[1]);
                unset($url[1]);
            } else {
                sendJsonResponse('error', 'Invalid Api method', 404);
            }
        } else {
            sendJsonResponse('error', 'API endpoint not found', 404);
        }


        $url = $url ? array_values($url) : [];
        $this->params = $url;

        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    private function getUrl()
    {
        $url = isset($_GET['url']) ? clean_url($_GET['url']) : [];
        return $url;
    }
}