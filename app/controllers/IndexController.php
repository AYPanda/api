<?php

namespace controllers;

use BL\UserRepository;
use core\Controller;

class IndexController extends Controller
{
	private $userRepository;
    public function __construct()
    {
        parent::__construct();
        $this->userRepository = new UserRepository();
    }
    public function index()
    {
        Send(["1"]);
    }
    public function getUser() 
    {
    	Send($this->userRepository->get());
    }
}