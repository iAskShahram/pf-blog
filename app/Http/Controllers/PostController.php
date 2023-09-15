<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
	public function store(Request $request)
	{
		$rules = [
			'title' => 'required|min:3',
			'body' => 'required|min:3',
		];
		$validatedData = Validator::make($request->all(), $rules);
		if ($validatedData->fails()) {
			return response()->json(['error' => $validatedData->errors()], 400);
		}

		$userId = (int) $request->user()->id;
		$post = Post::create([
			'title' => $request->title,
			'body' => $request->body,
			'user_id' => $userId,
		]);

		return response()->json(['post' => $post, 'message' => "Operation completed successfully"], 200);
	}

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

	public function delete(Request $request, $id)
	{
		$post = Post::find($id);
		if (!$post) {
			return response()->json(['error' => 'Post does not exist'], 404);
		}

		if (!$request->user()->isSuperUser() && $post->user_id !== $request->user()->id) {
			return response()->json(['error' => 'You are not authorized to delete this post'], 401);
		}

		$post->delete();
		return response()->json(['post' => $post, 'message' => "Operation completed successfully"], 200);
	}

	public function update(Request $request, $id)
	{
		$post = Post::find($id);
		if (!$post) {
			return response()->json(['error' => 'Post does not exist'], 404);
		}

		if (!$request->user()->isSuperUser() && $post->user_id !== $request->user()->id) {
			return response()->json(['error' => 'You are not authorized to update this post'], 401);
		}

		// validate the request
		$rules = [
			'title' => 'min:3',
			'body' => 'min:3',
		];
		$validatedData = Validator::make($request->all(), $rules);
		if ($validatedData->fails()) {
			return response()->json(['error' => $validatedData->errors()], 400);
		}

		$post->update($request->all());
		return response()->json(['post' => $post, 'message' => "Operation completed successfully"], 200);
	}
}
