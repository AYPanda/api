<?php

namespace core;

use classes\DBquery;

abstract class BaseRepository
{
    protected $db;

    public function __construct()
    {
        $this->db = new DBquery();
    }
}