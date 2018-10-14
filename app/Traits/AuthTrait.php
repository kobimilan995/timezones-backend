<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait AuthTrait {

    public function storeUser($credentials, $role_name) {
        $bcryptedPassword = bcrypt($credentials['password']);

        $role_id = DB::select("SELECT role_id FROM roles where role_name = '{$role_name}'")[0]->role_id;
        return DB::insert("INSERT INTO users (email, first_name, last_name, password, role_id) values ('{$credentials['email']}', '{$credentials['first_name']}', '{$credentials['last_name']}', '{$bcryptedPassword}', '{$role_id}')");
    }

    public function storeTokenData($token, $user, $expire_date) {
        return DB::insert("INSERT INTO tokens (jwt_token, user_id, expire_date) values ('{$token}', {$user->id}, {$expire_date})");
    }

    public function deleteTokenData($user) {
        return DB::delete("DELETE FROM tokens WHERE user_id='{$user->id}'");
    }

    public function updateById($id, $data) {
        return DB::update("UPDATE users
        SET first_name = '{$data['first_name']}' , last_name = '{$data['last_name']}', email='{$data['email']}', role_id={$data['role_id']} WHERE id = {$data['id']}");
    }

    public function findByEmail($email) {
        return DB::select("SELECT * FROM users WHERE email = '{$email}'");
    }

    public function findById($id) {
        return DB::select("SELECT id, email, first_name, last_name, role_id
        FROM users 
        WHERE users.id = {$id}");
    }

    public function getUserRoles() {
        return DB::select("SELECT * FROM roles");
    }

    public function findByEmailWithRole($email) {
        return DB::select("SELECT * 
        FROM users 
        INNER JOIN roles ON users.role_id = roles.role_id
        WHERE users.email = '{$email}'");
    }


    public function getAllUsers() {
        return DB::select("SELECT id, first_name, last_name, email, role_name 
        FROM users 
        INNER JOIN roles ON users.role_id = roles.role_id");
    }

    public function getFilteredUsers($query) {
        return DB::select("SELECT id, first_name, last_name, email, role_name 
        FROM users 
        INNER JOIN roles ON users.role_id = roles.role_id
        WHERE users.first_name LIKE '%{$query}%' OR users.last_name LIKE '%{$query}%'");
    }

    public function deleteUserWithId($user_id) {
        return DB::delete("DELETE FROM users
        WHERE id = {$user_id}");
    }
}