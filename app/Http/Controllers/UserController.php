<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

	// login user using bearer token, return the user object and the token inr esponse
	public function login(Request $request)
	{
		// validate the request
		$validatedData = $request->validate([
			'email' => 'required|email',
			'password' => 'required|min:8',
		]);

		// check if the user exists
		$user = User::where('email', $validatedData['email'])->first();
		if (!$user) {
			return response()->json(['error' => 'User does not exist'], 401);
		}

		// check if email is verified
		if (!$user->email_verified_at) {
			return response()->json(['error' => 'Email is not verified'], 401);
		}

		// check if the password is correct
		if (!Hash::check($validatedData['password'], $user->password)) {
			return response()->json(['error' => 'Password is incorrect'], 401);
		}

		// generate token for the user
		$token = $user->createToken('auth_token')->plainTextToken;

		// return the user object and the token in response
		return response()->json(['user' => $user, 'token' => $token, 'message' => "Operation completed successfully"], 200);
	}

	// logout user by deleting the auth_token generated above
	public function logout(Request $request)
	{
		// delete the token
		$request->user()->currentAccessToken()->delete();
		return response()->json(['message' => 'Logged out successfully'], 200);
	}

	// signup funtion to create new user whose email is till not varfied, we will verify the email later
	public function signup(Request $request)
	{
		// validate the request
		$validatedData = $request->validate([
			'name' => 'required',
			'email' => 'required|email|unique:users',
			'password' => 'required|min:8',
		]);

		// create the user
		$user = User::create([
			'name' => $validatedData['name'],
			'email' => $validatedData['email'],
			'password' => Hash::make($validatedData['password']),
			'email_verified_at' => null,
		]);

		// return the user object and the token in response
		return response()->json(['user' => $user, 'message' => "Operation completed successfully"], 201);
	}

	// function to verify the email of the user, only superuser can verify the new user
	public function verifyUser(Request $request, $id)
	{
		// this route is superuser specific, so we need to check if the user is a superuser
		if (!$request->user()->isSuperUser()) {
			return response()->json(['error' => 'You are not authorized to access this route'], 403);
		}

		$id = (int) $id;
		// find the user
		$user = User::find($id);
		if (!$user) {
			return response()->json(['error' => 'User does not exist'], 404);
		}

		// check if user is already verified
		if ($user->email_verified_at) {
			return response()->json(['error' => 'User is already verified'], 400);
		}

		// verify the email
		$user->email_verified_at = now();
		$user->save();

		// return the user object and the token in response
		return response()->json(['user' => $user, 'message' => "Operation completed successfully"], 200);
	}


	// function to reutrn all users
	public function getAllUsers(Request $request)
	{
		// this route is superuser specific, so we need to check if the user is a superuser
		if ($request->user()->is_superuser != true) {
			return response()->json(['error' => 'You are not authorized to access this route'], 403);
		}
		$users = User::all();
		return response()->json($users);
	}

	// function to get user by id
	public function getUserById(Request $request,  $id)
	{
		// this route is superuser specific, so we need to check if the user is a superuser
		if ($request->user()->is_superuser != true) {
			return response()->json(['error' => 'You are not authorized to access this route'], 403);
		}
		$user = User::find($id);
		return response()->json($user);
	}


	// funtion for getUser that returns the details of the user. if the id query parameter is given, then it returns the details of the user with that id, else it returns the details of the authenticated user
	// if the id is given, then the user must be a superuser
	public function getUser(Request $request)
	{
		// if id is given, then the user must be a superuser
		if ($request->id) {
			// this route is superuser specific, so we need to check if the user is a superuser
			if (!$request->user()->is_superuser) {
				return response()->json(['error' => 'You are not authorized to access this user profile'], 403);
			}
			$user = User::find($request->id);
			return response()->json($user, 200);
		}
		// if user is superuser then return all else return the details of the authenticated user
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