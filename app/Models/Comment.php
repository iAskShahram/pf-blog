<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
	use HasFactory;

	// fillable attributes
	protected $fillable = [
		'name',
		'body',
		'post_id',
	];

	// casted attributes
	protected $casts = [
		'created_at' => 'datetime',
		'updated_at' => 'datetime',
		'post_id' => 'integer',
	];

	// add function to get post of comment
	public function post()
	{
		return $this->belongsTo(Post::class);
	}
}
