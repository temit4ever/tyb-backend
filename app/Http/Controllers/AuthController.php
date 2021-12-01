<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request) {
      $this->validate($request, ['email' => 'required', 'password' => 'required']);

      $user = User::where('email', $request->email)->first();

      if (!$user) {
        return response()->json(['status' => 'error', 'message' => 'This user does not exist']);
      }

      if (Hash::check($request->password, $user->password)) {
        $user->api_token = '1234';
      }

    }
}
