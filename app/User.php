<?php

namespace App;
use \Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\Traits\AuthTrait;
class User
{
    use AuthTrait;

    private $id, $email, $firstName, $lastName, $password;


    public function __construct($email, $firstName, $lastName, $password) {
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->password = $password;
    }
}
