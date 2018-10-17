<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\AuthTrait;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    use AuthTrait;
    public function index(Request $request)
    {
        $query = $request->input('query');
        $current_page = $request->input('current_page');
        $per_page = $request->input('per_page');
        $db_data = $query ? $this->getFilteredUsers($query, $current_page, $per_page) : $this->getAllUsers($current_page, $per_page);

        return response()->json([
            'type' => 'success',
            'users' => $db_data['users'],
            'count' => $db_data['count'][0]
        ]);
    }
    
    public function show($user_id)
    {
        $user_data = $this->findById($user_id);
        if(!$user_data) {
            return response()->json([
                'type' => 'error',
                'data' => ['errors' => ['User does not exist']]  
              ], 404);
        }
        $roles = $this->getUserRoles();
        return response()->json([
           'type' => 'success',
           'user' => $user_data[0] ,
           'roles' => $roles
        ]);
    }

    public function update($user_id, Request $request)
    {
        $validation = Validator::make($request->all(),[ 
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'role_id' => 'required|exists:roles,role_id'
        ]);
        if($validation->fails()) {
            return response()->json([
                'type' => 'error',
                'data' => $validation->errors()
            ], 400);
        }
        $old_email = $request->input('email');
        $id = $request->input('id');

        $old_user_data = $this->findByEmail($old_email);
        if($old_user_data && $id != $old_user_data[0]->id) {
            return response()->json([
                'type' => 'error',
                'data' => ['errors' => ['Email must be unique']]  
              ], 400);
        }
        
        $bool = $this->updateById($id, $request->all());

        return response()->json([
            'type' => 'success',
            'updated' => $bool
        ]);
    }

    public function destroy($user_id, Request $request)
    {
        if($user_id == $request['user']->id) {
            return response()->json([
                'type' => 'error',
                'data' => ['errors' => ['You cannot delete yourself!']]  
              ], 400);
        }
        $success = $this->deleteUserWithId($user_id);

        if($success) {
            return response()->json([
                'type' => 'success',
                'message' => 'User succesfully deleted'
            ]);
        }
        
        return response()->json([
          'type' => 'error',
          'data' => ['errors' => ['User does not exist']]  
        ], 400);
    }
}

