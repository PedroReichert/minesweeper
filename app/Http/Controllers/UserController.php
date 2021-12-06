<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

class UserController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users,email',
            'password'=>'required'
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        return Response::json(['data'=>$user]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $user = User::findOrFail($id);
            return Response::json(['data'=>$user]);
        }catch(Exception $e){
            return Response::json(['data'=>'Not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'=>'required',
            'password'=>'required'
        ]);

        try{
            $user = User::findOrFail($id);
            $user->name = $validated['name'];
            $user->password = Hash::make($validated['password']);
            $user->save();
            return Response::json(['data'=>$user]);
        }catch(Exception $e){
            return Response::json(['data'=>'Not found'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $user = User::findOrFail($id);
            $user->delete();
            return Response::json(['data'=>$user]);
        }catch(Exception $e){
            return Response::json(['data'=>'Not found'], 404);
        }
    }
}
