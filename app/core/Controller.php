<?php

namespace core;

use classes\ApiHelper;

require_once(__DIR__."/helpers.php"); // Плохое, временное решение. Лучше сделать через композер

class Controller
{
    private $api;

    public function __construct()
    {
        $this->api = new ApiHelper();
    }
    protected function request()
    {
        return (object) empty($_REQUEST) == true ? json_decode(file_get_contents('php://input')) : $_REQUEST;
    }
}