<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
	public function store(Request $request)
	{
		$validatedData = $request->validate([
			'name' => 'min:3',
			'body' => 'required|min:3',
			'post_id' => 'required|exists:posts,id',
		]);

		$comment = Comment::create([
			'name' => $validatedData['name'],
			'body' => $validatedData['body'],
			'post_id' => $validatedData['post_id'],
		]);

		return response()->json(['comment' => $comment, 'message' => "Operation completed successfully"], 200);
	}

	public function delete(Request $request, $id)
	{
		$comment = Comment::find($id);

		if (!$comment) {
			return response()->json(['error' => 'Comment does not exist'], 404);
		}

		if (!$request->user()->isSuperUser() && $comment->post->user_id !== $request->user()->id) {
			return response()->json(['error' => 'You are not authorized to delete this comment'], 401);
		}

		$comment->delete();
		return response()->json(['comment' => $comment, 'message' => "Operation completed successfully"], 200);
	}

	public function getComments(Request $request)
	{
		$post_id = $request->query('post_id');
		if (!$post_id) {
			return response()->json(['message' => 'Post Id is required'], 400);
		}

		$comments = Comment::all()->where('post_id', $post_id);
		return response()->json(['comments' => $comments, 'message' => "Operation completed successfully"], 200);
	}
}
