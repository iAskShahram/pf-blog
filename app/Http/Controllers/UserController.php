<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

	public function login(Request $request)
	{
		$validatedData = $request->validate([
			'email' => 'required|email',
			'password' => 'required|min:8',
		]);

		$user = User::where(['email' => $validatedData['email']])->first();
		if (!$user) {
			return response()->json(['error' => 'User does not exist'], 401);
		}

		if (!$user->email_verified_at) {
			return response()->json(['error' => 'Email is not verified'], 401);
		}
		if (!Hash::check($validatedData['password'], $user->password)) {
			return response()->json(['error' => 'Password is incorrect'], 401);
		}

		$token = $user->createToken('auth_token')->plainTextToken;
		return response()->json(['user' => $user, 'token' => $token, 'message' => "Operation completed successfully"], 200);
	}

	public function logout(Request $request)
	{
		// delete the token
		$request->user()->currentAccessToken()->delete();
		return response()->json(['message' => 'Logged out successfully'], 200);
	}

	public function signup(Request $request)
	{
		$validatedData = $request->validate([
			'name' => 'required',
			'email' => 'required|email|unique:users',
			'password' => 'required|min:8',
		]);

		$user = User::create([
			'name' => $validatedData['name'],
			'email' => $validatedData['email'],
			'password' => Hash::make($validatedData['password']),
			'email_verified_at' => null,
		]);

		return response()->json(['user' => $user, 'message' => "Operation completed successfully"], 201);
	}

	public function verifyUser(Request $request, $id)
	{
		$id = (int) $id;
		$user = User::find($id);
		if (!$user) {
			return response()->json(['error' => 'User does not exist'], 404);
		}

		if ($user->email_verified_at) {
			return response()->json(['error' => 'User is already verified'], 400);
		}

		$user->email_verified_at = now();
		$user->save();
		return response()->json(['user' => $user, 'message' => "Operation completed successfully"], 200);
	}


	public function getUser(Request $request)
	{
		if ($request->id) {
			// add a check if the user is superuser, or the user is requesting his own data
			if (!$request->user()->is_superuser && $request->user()->id != $request->id) {
				return response()->json(['error' => 'You are not authorized to access this route'], 403);
			}
			$user = User::find($request->id);
			return response()->json($user, 200);
		}

		if ($request->user()->is_superuser) {
			$users = User::all();
			return response()->json($users, 200);
		}
		$user = $request->user();
		return response()->json($user, 200);
	}


	public function delete(Request $request, $id)
	{
		$id = (int) $id;
		if (!$id) {
			return response()->json(['error' => 'User id is required'], 400);
		}

		$user = User::find($id);
		if (!$user) {
			return response()->json(['error' => 'User does not exist'], 404);
		}

		// if $user is superuser then it cannot delete the super user
		if ($user->is_superuser) {
			return response()->json(['error' => 'Superuser cannot be deleted'], 400);
		}

		$user->delete();
		return response()->json(['message' => 'User deleted successfully'], 200);
	}
}
