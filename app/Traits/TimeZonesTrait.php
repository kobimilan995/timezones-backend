<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait TimeZonesTrait {

    public function storeTimeZone($data, $user) {

        $gmt_diff = $data['gmt_differance']?:0;
        return DB::insert("INSERT INTO time_zones (tz_name, tz_city, tz_gmt_diff, tz_user_id) values (:name, :city, :gmt_diff, :user_id)", [
            'name' => $data['name'],
            'city' => $data['city'],
            'gmt_diff' => $gmt_diff,
            'user_id' => $user->id
        ]);
    }

    public function getUsersTimeZones($user_id, $current_page, $per_page) {
        $offset = ($current_page-1) * $per_page;
        $db_data = DB::select("SELECT SQL_CALC_FOUND_ROWS * 
        FROM time_zones 
        WHERE tz_user_id = :user_id LIMIT :offset, :per_page", [
            'user_id' => $user_id,
            'per_page' => $per_page,
            'offset' => $offset
        ]);
        $count = DB::select("SELECT FOUND_ROWS() as count");
        return [
            'time_zones' => $db_data,
            'count' => $count,
        ];
    }

    public function getAllTimeZones($current_page, $per_page) {
        $offset = ($current_page-1) * $per_page;
        $db_data = DB::select("SELECT SQL_CALC_FOUND_ROWS tz_id, tz_name, tz_city, tz_gmt_diff, tz_user_id, first_name, last_name
        FROM time_zones
        INNER JOIN users ON time_zones.tz_user_id = users.id LIMIT :offset, :per_page", [
            'per_page' => $per_page,
            'offset' => $offset
        ]);
        $count = DB::select("SELECT FOUND_ROWS() as count");
        return [
            'time_zones' => $db_data,
            'count' => $count
        ];
    }

    public function getFilteredUsersTimeZones($user_id, $query, $current_page, $per_page) {
        $offset = ($current_page-1) * $per_page;
        $db_data = DB::select("SELECT SQL_CALC_FOUND_ROWS *
        FROM time_zones
        WHERE tz_user_id = :user_id AND tz_name LIKE :query LIMIT :offset, :per_page", [
            'user_id' => $user_id,
            'query' => '%'.$query.'%',
            'per_page' => $per_page,
            'offset' => $offset
        ]);
        $count = DB::select("SELECT FOUND_ROWS() as count");
        return [
            'time_zones' => $db_data,
            'count' => $count
        ];
    }

    public function getFilteredAllTimeZones($query, $current_page, $per_page) {
        $offset = ($current_page-1) * $per_page;
        $db_data = DB::select("SELECT SQL_CALC_FOUND_ROWS tz_id, tz_name, tz_city, tz_gmt_diff, tz_user_id, first_name, last_name
        FROM time_zones
        INNER JOIN users ON time_zones.tz_user_id = users.id
        WHERE tz_name LIKE :query LIMIT :offset, :per_page", [
            'query' => '%'.$query.'%',
            'per_page' => $per_page,
            'offset' => $offset
        ]);
        $count = DB::select("SELECT FOUND_ROWS() as count");
        return [
            'time_zones' => $db_data,
            'count' => $count
        ];
    }


    public function findById($tz_id) {
        return DB::select("SELECT *
        FROM time_zones 
        WHERE tz_id = :tz_id", [
            'tz_id' => $tz_id
        ]);
    }

    public function updateById($tz_id, $data) {
        return DB::update("UPDATE time_zones
        SET tz_name = :name , tz_city = :city, tz_gmt_diff=:gmt_differance WHERE tz_id = :tz_id", [
            'name' => $data['name'],
            'city' => $data['city'],
            'gmt_differance' => $data['gmt_differance'],
            'tz_id' => $tz_id
        ]);
    }

    public function deleteById($tz_id) {
        return DB::delete("DELETE FROM time_zones WHERE tz_id = :tz_id", [
            'tz_id' => $tz_id
        ]);
    }

}