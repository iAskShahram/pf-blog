<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
	use HasFactory;

	// add fillabel
	protected $fillable = [
		'user_id',
		'title',
		'body',
	];

	// casted attributes
	protected $casts = [
		'created_at' => 'datetime',
		'updated_at' => 'datetime',
		'user_id' => 'integer',
	];

	// add function to get user of post
	public function user()
	{
		return $this->belongsTo(User::class);
	}

	// add function to get comments of post
	public function comments()
	{
		return $this->hasMany(Comment::class);
	}
}
