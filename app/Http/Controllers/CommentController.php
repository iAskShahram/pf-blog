<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
	// function to store a comment, anyone can comment on a post, no authentication required
	public function store(Request $request)
	{
		// validate the request
		$validatedData = $request->validate([
			'name' => 'min:3',
			'body' => 'required|min:3',
			'post_id' => 'required|exists:posts,id',
		]);

		// use validatedData to create a comment
		$comment = Comment::create([
			'name' => $validatedData['name'],
			'body' => $validatedData['body'],
			'post_id' => $validatedData['post_id'],
		]);

		// return the comment in response
		return response()->json(['comment' => $comment, 'message' => "Operation completed successfully"], 200);
	}

	// function to delete a comment, a super user or the user whose post it is can delete a comment
	public function delete(Request $request, $id)
	{
		// get the comment
		$comment = Comment::find($id);

		// check if the comment exists
		if (!$comment) {
			return response()->json(['error' => 'Comment does not exist'], 404);
		}

		// check if the user is authorized to delete the comment
		if (!$request->user()->isSuperUser() && $comment->post->user_id !== $request->user()->id) {
			return response()->json(['error' => 'You are not authorized to delete this comment'], 401);
		}

		// delete the comment
		$comment->delete();

		// return the comment in response
		return response()->json(['comment' => $comment, 'message' => "Operation completed successfully"], 200);
	}

	// function for getComments that will return all comments of a post with post_id given inside the query parameter
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