<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
	// function to store a post
	public function store(Request $request)
	{
		// validate the request
		$rules = [
			'title' => 'required|min:3',
			'body' => 'required|min:3',
		];
		$validatedData = Validator::make($request->all(), $rules);
		if ($validatedData->fails()) {
			return response()->json(['error' => $validatedData->errors()], 400);
		}

		// create a post
		$userId = (int) $request->user()->id;
		$post = Post::create([
			'title' => $request->title,
			'body' => $request->body,
			'user_id' => $userId,
		]);

		// return the post in response
		return response()->json(['post' => $post, 'message' => "Operation completed successfully"], 200);
	}

	// fucntion for getPosts that returns all posts if the use is superuser and query $id is not present, else return the all posts of the user who is logged in if the $id is present in query only return the post of that id
	public function getPosts(Request $request)
	{
		$post_id = $request->query('id');
		if ($post_id) {
			$post = Post::with('comments')->find($post_id);
			if (!$post) {
				return response()->json(['error' => 'Post does not exist'], 404);
			}
			return response()->json(['post' => $post, 'message' => "Operation completed successfully"], 200);
		}
		$posts = Post::with('comments')->get();
		return response()->json(['posts' => $posts, 'message' => "Operation completed successfully"], 200);
	}

	// fucntion to delete a psot
	public function delete(Request $request, $id)
	{
		// get the post
		$post = Post::find($id);

		// check if the post exists
		if (!$post) {
			return response()->json(['error' => 'Post does not exist'], 404);
		}

		// check if the user is authorized to delete the post
		if (!$request->user()->isSuperUser() && $post->user_id !== $request->user()->id) {
			return response()->json(['error' => 'You are not authorized to delete this post'], 401);
		}

		// delete the post
		$post->delete();

		// return the post in response
		return response()->json(['post' => $post, 'message' => "Operation completed successfully"], 200);
	}
}