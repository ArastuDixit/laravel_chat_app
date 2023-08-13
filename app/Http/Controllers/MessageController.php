<?php

namespace App\Http\Controllers;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class MessageController extends Controller
{
    // public function index()
    // {
    //     $messages = Message::all();
    //     return view('messages', compact('messages'));
    // }

    public function index()
{
    // Get all users except the currently logged-in user (to avoid displaying the current user in the recipient list)
    $users = User::where('id', '!=', Auth::user()->id)->get();

    return view('messages', compact('users'));
}

    public function sendMessage(Request $request)
    {
        // Validation (optional)
        $request->validate([
            'incoming_msg_id' => 'required|integer',
            'text_message' => 'required|string|max:255',
        ]);

        // Save the message to the database
        $message = new Message();
        $message->incoming_msg_id = $request->input('incoming_msg_id');
        $message->outgoing_msg_id = Auth::user()->id; // Assign the logged-in user's ID
        $message->text_message = $request->input('text_message');
        $message->save();

    return response()->json(['success' => true]);
    }

    public function getMessages()
    {
        // Fetch all messages from the database (customize this based on your needs)
        $messages = Message::all();

        return response()->json(['success' => true, 'messages' => $messages]);
    }

}
