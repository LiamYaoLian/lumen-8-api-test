<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\TodoNote;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;

class TodoNoteController extends Controller
{
    public function listForLoggedInUser()
    {
        // TODO
        return TodoNote::all();
    }

    public function listForUser($userId)
    {
    }

    public function store(Request $request)
    {
        try {
            $todoNote = new TodoNote();

            // TODO style
            $todoNote->user_id = auth()->user()->id;
            $todoNote->note_content = $request->noteContent;
            $todoNote->completion_time = null;

            if ($todoNote->save()) {
                return response()->json(['status' => 'success', 'message' => 'Post created successfully']);

            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $post = Post::findOrFail($id);
            $post->title = $request->title;
            $post->body = $request->body;

            if ($post->save()) {
                return response()->json(['status' => 'success', 'message' => 'Post updated successfully']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $post = Post::findOrFail($id);

            if ($post->delete()) {
                return response()->json(['status' => 'success', 'message' => 'Post deleted successfully']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
