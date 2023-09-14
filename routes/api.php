<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// public routes for signup, login and comment only
Route::group([], function () {
	Route::post('/signup', [UserController::class, 'signup']);
	Route::post('/login', [UserController::class, 'login']);
	Route::post('/comment', [CommentController::class, 'store']);
	// any guest user can see all posts
	Route::get('/post', [PostController::class, 'getPosts']);
});

// routes for all users whose emails are verified, use middleware to check if the email is verified
Route::group(['middleware' => ['auth:sanctum', 'is_verified']], function () {
	// these routes are only for authenticated users, where authenticated users will be able to Perform CRUD on their posts that they create
	Route::post('/post', [PostController::class, 'store']);
	// delete a post
	Route::delete('/post/{id}', [PostController::class, 'delete']);

	// routes to perform CRUD on comments
	// delete route for comment
	Route::delete('/comment/{id}', [CommentController::class, 'delete']);
	// get all comments of a post
	Route::get('/comment', [CommentController::class, 'getComments']);

	// routes for users
	// route to get user details
	Route::get('/user', [UserController::class, 'getUser']);
	Route::post('/logout', [UserController::class, 'logout']);
});

// routes for superuser only
Route::group(['middleware' => ['auth:sanctum', 'is_superuser']], function () {
	Route::put('/verify-user/{id}', [UserController::class, 'verifyUser']);
	Route::delete('/user/{id}', [UserController::class, 'delete']);
});