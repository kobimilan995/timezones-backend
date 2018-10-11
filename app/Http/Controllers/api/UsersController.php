<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\AuthTrait;

class UsersController extends Controller
{
    use AuthTrait;
    public function index(Request $request)
    {
        $query = $request->input('query');
        $user_data = $query ? $this->getFilteredUsers($query) : $this->getAllUsers();

        return response()->json([
            'type' => 'success',
            'users' => $user_data
        ]);
    }

    public function store()
    {

    }
    
    public function show($user_id)
    {

    }

    public function update($user_id, Request $request)
    {

    }

    public function destroy($user_id)
    {

    }
}

