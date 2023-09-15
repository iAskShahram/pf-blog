<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
	use HasFactory;

	protected $fillable = [
		'name',
		'body',
		'post_id',
	];

	protected $casts = [
		'created_at' => 'datetime',
		'updated_at' => 'datetime',
		'post_id' => 'integer',
	];

	public function post()
	{
		return $this->belongsTo(Post::class);
	}
}
