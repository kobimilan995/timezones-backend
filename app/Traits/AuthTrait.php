<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait AuthTrait {

    public function storeUser($credentials, $role_name) {
        $bcryptedPassword = bcrypt($credentials['password']);

        $role_id = DB::select("SELECT role_id FROM roles where role_name = '{$role_name}'")[0]->role_id;
        return DB::insert("INSERT INTO users (email, first_name, last_name, password, role_id) 
        values (:email, :first_name, :last_name, :password, :role_id)", [
            'email' => $credentials['email'],
            'first_name' => $credentials['first_name'],
            'last_name' => $credentials['last_name'],
            'password' => $bcryptedPassword,
            'role_id' => $role_id
        ]);
    }

    public function updateById($id, $data) {
        return DB::update("UPDATE users
        SET first_name = :first_name , last_name = :last_name, email=:email, role_id=:role_id WHERE id = :id", [
             'first_name' => $data['first_name'],
             'last_name' => $data['last_name'],
             'email' => $data['email'],
             'role_id' => $data['role_id'],
             'id' => $data['id']
        ]);
    }

    public function findByEmail($email) {
        return DB::select("SELECT * FROM users WHERE email = :email", ['email' => $email]);
    }

    public function findById($id) {
        return DB::select("SELECT id, email, first_name, last_name, users.role_id, role_name
        FROM users
        INNER JOIN roles on users.role_id = roles.role_id 
        WHERE users.id = :id", ['id' => $id]);
    }

    public function getUserRoles() {
        return DB::select("SELECT * FROM roles");
    }

    public function findByEmailWithRole($email) {
        return DB::select("SELECT * 
        FROM users 
        INNER JOIN roles ON users.role_id = roles.role_id
        WHERE users.email = :email", ['email' => $email]);
    }


    public function getAllUsers($current_page, $per_page) {
        $offset = ($current_page-1) * $per_page;
        $db_data = DB::select("SELECT SQL_CALC_FOUND_ROWS id, first_name, last_name, email, role_name 
        FROM users 
        INNER JOIN roles ON users.role_id = roles.role_id LIMIT :offset, :per_page", ['per_page' => $per_page,'offset' => $offset]);
        $count = DB::select("SELECT FOUND_ROWS() as count");

        return [
            'users' => $db_data,
            'count' => $count
        ];
    }

    public function getFilteredUsers($query, $current_page, $per_page) {
        $offset = ($current_page-1) * $per_page;
        $db_data = DB::select("SELECT SQL_CALC_FOUND_ROWS id, first_name, last_name, email, role_name 
        FROM users 
        INNER JOIN roles ON users.role_id = roles.role_id
        WHERE users.first_name LIKE :query1 OR users.last_name LIKE :query2 OR CONCAT(users.first_name, ' ', users.last_name) LIKE :query3 LIMIT :offset, :per_page", [
            'query1' => '%'.$query.'%',
            'query2' => '%'.$query.'%', 
            'query3' => '%'. $query .'%',
            'per_page' => $per_page,
            'offset' => $offset
            ]);
        $count = DB::select("SELECT FOUND_ROWS() as count");

        return [
            'users' => $db_data,
            'count' => $count
        ];
    }

    public function deleteUserWithId($user_id) {
        return DB::delete("DELETE FROM users
        WHERE id = :user_id", ['user_id' => $user_id]);
    }
}