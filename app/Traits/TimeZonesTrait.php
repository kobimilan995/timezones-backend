<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait TimeZonesTrait {

    public function storeTimeZone($data, $user) {

        $gmt_diff = $data['gmt_differance']?:0;
        return DB::insert("INSERT INTO time_zones (tz_name, tz_city, tz_gmt_diff, tz_user_id) values ('{$data['name']}', '{$data['city']}', {$gmt_diff}, {$user->id})");
    }

    public function getUsersTimeZones($user_id) {
        return DB::select("SELECT * 
        FROM time_zones 
        WHERE tz_user_id = {$user_id}");
    }

    public function getAllTimeZones() {
        return DB::select("SELECT tz_id, tz_name, tz_city, tz_gmt_diff, tz_user_id, first_name, last_name 
        FROM time_zones
        INNER JOIN users ON time_zones.tz_user_id = users.id");
    }

    public function getFilteredUsersTimeZones($user_id, $query) {
        return DB::select("SELECT *
        FROM time_zones
        WHERE tz_user_id = {$user_id} AND tz_name LIKE '%{$query}%'");
    }

    public function getFilteredAllTimeZones($query) {
        return DB::select("SELECT tz_id, tz_name, tz_city, tz_gmt_diff, tz_user_id, first_name, last_name 
        FROM time_zones
        INNER JOIN users ON time_zones.tz_user_id = users.id
        WHERE tz_name LIKE '%{$query}%'");
    }


    public function findById($tz_id) {
        return DB::select("SELECT *
        FROM time_zones 
        WHERE tz_id = {$tz_id}");
    }

    public function updateById($tz_id, $data) {
        return DB::update("UPDATE time_zones
        SET tz_name = '{$data['name']}' , tz_city = '{$data['city']}', tz_gmt_diff={$data['gmt_differance']} WHERE tz_id = {$tz_id}");
    }

    public function deleteById($tz_id) {
        return DB::delete("DELETE FROM time_zones WHERE tz_id={$tz_id}");
    }

}