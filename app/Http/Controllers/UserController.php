<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\User as UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return UserResource::collection(User::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|min:3|max:100',
            //'avatar' => 'file|mimes:jpeg,png|max:1024|dimensions:min_width=100,min_height=100,max_width=1024,max_height=1024',
          'email' => 'required|email|unique:users',
          'password' => 'required'

        ]);
        $user = User::create([
          'title' => $request->title,
          'email' => $request->email,
          'password' => Hash::make($request->password),
        ]);

        $user->api_token = Str::random(20);

        if ($request->exists('avatar')) {
          //dd($request);
            $user->avatar = $request->file('avatar');
            //dd($_FILES);
            $user->save();
        }
        //dd(response()->json(['data' => $user,], 201 ));

        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            'title' => 'required|min:3|max:100',
            'avatar' => 'file|mimes:jpeg,png|max:1024|dimensions:min_width=100,min_height=100,max_width=1024,max_height=1024'
        ]);

        $user->update($request->all());

        if ($request->exists('avatar')) {
            $user->avatar = $request->file('avatar');
            $user->save();
        }

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
    }
}
