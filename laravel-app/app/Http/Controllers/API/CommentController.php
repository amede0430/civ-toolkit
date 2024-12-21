<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Comment::with('plan')->where('user_id', Auth::id())->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|exists:plans,id',
            'comment' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $comment = Comment::create([
            'user_id' => Auth::user()->id,
            'plan_id' => $request->plan_id, 
            'comment' => $request->comment
        ]);

        return response()->json(['message' => 'Commentaire ajoute avec succes', 'comment' => $comment], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $comment = Comment::with('plan')->find($id);
        if (!$comment) {
            return response()->json(['message' => 'Commentaire non trouve'], 404);
        }
        return response()->json($comment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $comment = Comment::find($id);        
        if (!$comment) {
            return response()->json(['message' => 'Commentaire non trouve'], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'comment' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $comment = $comment->update([
            'user_id' => Auth::user()->id,
            'plan_id' => $request->plan_id,
            'comment' => $request->comment
        ]);

        return response()->json(['message' => 'Commentaire ajoute avec succes', 'comment' => $comment], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $comment = Comment::find($id);        
        if (!$comment) {
            return response()->json(['message' => 'Commentaire non trouve'], 404);
        }

        $comment->delete();
        
        return response()->json(['message' => 'Commentaire supprime avec succes']);
    }
}
