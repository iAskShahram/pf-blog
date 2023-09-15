<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;

// public routes
Route::group([], function () {
	Route::post('/signup', [UserController::class, 'signup']);
	Route::post('/login', [UserController::class, 'login']);
	Route::post('/comment', [CommentController::class, 'store']);
	// any guest user can see all posts, or pass query 'id' for single post
	Route::get('/post', [PostController::class, 'getPosts']);
});

// routes for all verified users
Route::group(['middleware' => ['auth:sanctum', 'is_verified']], function () {
	Route::post('/post', [PostController::class, 'store']);
	Route::delete('/post/{id}', [PostController::class, 'delete']);
	Route::patch('/post/{id}', [PostController::class, 'update']);

	Route::delete('/comment/{id}', [CommentController::class, 'delete']);
	// get all comments of a post by query 'post_id'
	Route::get('/comment', [CommentController::class, 'getComments']);

	// leave blanck (for super admin) to get all users, or pass 'id' to get a specific user or one's own profile
	Route::get('/user', [UserController::class, 'getUser']);
	Route::post('/logout', [UserController::class, 'logout']);
});

// routes for superuser only
Route::group(['middleware' => ['auth:sanctum', 'is_superuser']], function () {
	Route::put('/verify-user/{id}', [UserController::class, 'verifyUser']);
	Route::delete('/user/{id}', [UserController::class, 'delete']);
});
