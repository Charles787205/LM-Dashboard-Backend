<?php

namespace App\Http\Controllers;
use App\Models\HubComment;
use Illuminate\Http\Request;

class HubCommentController extends Controller
{
    
    public function index(Request $request){

        $hub_ids = $request->input('hub_ids', []);
        $comments = [];
        if (empty($hub_ids)){
            $comments = HubComment::with('user')->orderBy('created_at', 'desc')->get();
        } else {
            $comments = HubComment::with('user')->whereIn('hub_id', $hub_ids)->orderBy('created_at', 'desc')->get();
         }
        return response()->json($comments);
    }

    public function getCommentByHub($hubId){
        $comments  = HubComment::with(['user' => function ($query){
            $query->select('id', 'name');
        }])->where('hub_id', $hubId)->orderBy('created_at', 'desc')->first();
        return response()->json($comments);
    }

    public function store(Request $request){
       
        $request->validate([
            'comment' => 'required|string',
            'hub_id' => 'required|integer|exists:hub,id',
        ]);

        $comment = HubComment::create([
            'user_id' => auth()->id(),
            'comment' => $request->comment,
            'hub_id' => $request->hub_id,
        ]);

        return response()->json($comment, 201);
    }
    
}
