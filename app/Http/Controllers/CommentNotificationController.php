<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\CommentNotification;
use App\Models\Comment;

class CommentNotificationController extends Controller
{
    public function comment()
	{
    	return $this->belongsTo(Comment::class, 'comment_id', 'comment_id');
	}
}
