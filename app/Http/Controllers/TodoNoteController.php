<?php

namespace App\Http\Controllers;

use App\Models\TodoNote;
use App\Models\User;
use Illuminate\Http\Request;

class TodoNoteController extends Controller
{
    public function getList(Request $request)
    {
        $userStr = $request->query('user');

        // if url is '/todonotes?user=me'
        if ($userStr == 'me') {
            return $this->getListOfLoggedInUser();

        // if url is '/todonotes?user={username}'
        } else {
            return $this->getListByUsername($userStr);
        }
    }

    public function create(Request $request)
    {
        try {
            $todoNote = new TodoNote();
            // TODO need to replace style
            $todoNote->user_id = auth()->user()->id;
            $todoNote->note_content = $request->noteContent;
            $todoNote->completion_time = null;

            if ($todoNote->save()) {
                return response()->json(['status' => 'success', 'message' => 'To-do note created successfully']);

            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // TODO how to mark it as completed
    public function update(Request $request, $id)
    {
        try {
            $todoNote = TodoNote::findOrFail($id);
            if ($todoNote->user_id != auth()->user()->id) {
                return response()->json(['status' => 'error', 'message' => 'This note is not yours']);
            }
            $todoNote->note_content = $request->noteContent;

            if ($todoNote->save()) {
                // TODO
                return response()->json(['status' => 'success', 'message' => 'Note content updated successfully']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            $todoNote = TodoNote::findOrFail($id);

            if ($todoNote->user_id != auth()->user()->id) {
                return response()->json(['status' => 'error', 'message' => 'This note is not yours']);
            }

            if ($todoNote->delete()) {
                return response()->json(['status' => 'success', 'message' => 'Note deleted successfully']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    private function getListOfLoggedInUser()
    {
        $currentUserId = auth()->user()->id;
        return $this->getListByUserId($currentUserId);
    }

    private function getListByUsername($username)
    {
        $users = User::where('username', '=', $username)->get();
        // TODO
        $userId = $users[0]->id;
        return $this->getListByUserId($userId);
    }

    private function getListByUserId($userId)
    {
        $todoNotes = TodoNote::where('user_id', '=', $userId)->get();
        return $todoNotes;
    }


}
