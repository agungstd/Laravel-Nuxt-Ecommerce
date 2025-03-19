<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get users
        $users = User::when(request()->q, function($users) {
            $users = $users->where('name', 'like', '%'. request()->q . '%');
        })->latest()->paginate(5);
        
        //return with Api Resource
        return new UserResource(true, 'List Data Users', $users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6' 
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            //create user
            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => bcrypt($request->password)
            ]);

            //return success with Api Resource
            return new UserResource(true, 'Data User Berhasil Disimpan!', $user);
        } catch (\Exception $e) {
            //return failed with Api Resource
            return new UserResource(false, 'Data User Gagal Disimpan: ' . $e->getMessage(), null);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::whereId($id)->first();
        
        if($user) {
            //return success with Api Resource
            return new UserResource(true, 'Detail Data User!', $user);
        }

        //return failed with Api Resource
        return new UserResource(false, 'Detail Data User Tidak Ditemukan!', null);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|confirmed|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            if($request->password == "") {
                //update user without password
                $user->update([
                    'name'  => $request->name,
                    'email' => $request->email,
                ]);
            } else {
                //update user with new password
                $user->update([
                    'name'     => $request->name,
                    'email'    => $request->email,
                    'password' => bcrypt($request->password)
                ]);
            }

            //return success with Api Resource
            return new UserResource(true, 'Data User Berhasil Diupdate!', $user);
        } catch (\Exception $e) {
            //return failed with Api Resource
            return new UserResource(false, 'Data User Gagal Diupdate: ' . $e->getMessage(), null);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        try {
            //delete user
            $user->delete();
            
            //return success with Api Resource
            return new UserResource(true, 'Data User Berhasil Dihapus!', null);
        } catch (\Exception $e) {
            //return failed with Api Resource
            return new UserResource(false, 'Data User Gagal Dihapus: ' . $e->getMessage(), null);
        }
    }
}