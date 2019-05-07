<?php

namespace BL;

use core\BaseRepository;

class UserRepository extends BaseRepository
{
    public function get()
    {
        return [ "user" => "userName" ];
    }
}