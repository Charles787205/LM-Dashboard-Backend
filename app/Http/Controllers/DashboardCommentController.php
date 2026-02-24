<?php

namespace App\Http\Controllers;
use App\Models\DashboardComment;
use Illuminate\Http\Request;

class DashboardCommentController extends Controller
{
    
    public function index(Request $request){
        //get the last comment
        
        $comments  = DashboardComment::with('user')->orderBy('created_at', 'desc')->first();
        return response()->json($comments);
    }

    public function getCommentsByUser($userId){
        $comment  = DashboardComment::with('user')->where('user_id', $userId)->orderBy('created_at', 'desc')->first();

        return response()->json($comment);
    }

    public function store(Request $request){
        $request->validate([
            'comment' => 'required|string',
            'dashboard_type' => 'required|string',
        ]);

        $comment = DashboardComment::create([
            'user_id' => auth()->id(),
            'comment' => $request->comment,
            'dashboard_type' => $request->dashboard_type,
        ]);

        return response()->json($comment, 201);
    }

}
