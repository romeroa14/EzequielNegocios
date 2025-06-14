<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $conversations = Auth::guard('web')->user()->person->conversations()
            ->with(['lastMessage', 'buyer'])
            ->latest('updated_at')
            ->paginate(10);

        return view('seller.messages.index', compact('conversations'));
    }

    public function show(Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $conversation->load(['messages.sender', 'buyer']);
        
        // Marcar mensajes como leídos
        $conversation->messages()
            ->where('sender_id', '!=', Auth::id())
            ->where('read', false)
            ->update(['read' => true]);

        return view('seller.messages.show', compact('conversation'));
    }

    public function reply(Request $request, Conversation $conversation)
    {
        $this->authorize('reply', $conversation);

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => Auth::guard('web')->user()->person->id,
            'content' => $request->message,
            'read' => false,
        ]);

        $conversation->touch();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message->load('sender'),
                'success' => true,
            ]);
        }

        return back()->with('success', 'Mensaje enviado exitosamente.');
    }
} 