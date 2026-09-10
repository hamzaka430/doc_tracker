<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ChatController extends Controller
{
    /**
     * Display the main chat view or PIN lock/setup screen.
     */
    public function index(Request $request): View
    {
        $isConfigured = ChatSetting::isPinConfigured();
        $isUnlocked = (bool) session('chat_unlocked', false);
        $names = ChatSetting::getParticipantNames();
        $name1 = $names['name1'];
        $name2 = $names['name2'];

        if (!$isConfigured) {
            $state = 'setup_pin';
        } elseif (!$isUnlocked) {
            $state = 'locked';
        } else {
            $state = 'unlocked';
        }

        return view('chat.index', compact('state', 'isConfigured', 'isUnlocked', 'name1', 'name2'));
    }

    /**
     * Initial 4-digit PIN setup.
     */
    public function setupPin(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'pin' => ['required', 'digits:4', 'numeric'],
        ]);

        ChatSetting::setPin($request->pin);
        session(['chat_unlocked' => true]);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'PIN configured successfully!',
                'redirect' => route('chat.index'),
            ]);
        }

        return redirect()->route('chat.index')->with('success', 'Security PIN configured successfully!');
    }

    /**
     * Verify the 4-digit PIN to unlock the chat.
     */
    public function verifyPin(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'pin' => ['required', 'digits:4', 'numeric'],
        ]);

        if (ChatSetting::verifyPin($request->pin)) {
            session(['chat_unlocked' => true]);

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'PIN verified successfully!',
                    'redirect' => route('chat.index'),
                ]);
            }

            return redirect()->route('chat.index');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Incorrect PIN code. Please try again.',
            ], 422);
        }

        return back()->withErrors(['pin' => 'Incorrect PIN code. Please try again.']);
    }

    /**
     * Lock the chat session.
     */
    public function lock(Request $request): RedirectResponse|JsonResponse
    {
        session()->forget('chat_unlocked');

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Chat locked.',
                'redirect' => route('chat.index'),
            ]);
        }

        return redirect()->route('chat.index');
    }

    /**
     * Fetch all recent messages or delta messages after a specific ID.
     */
    public function fetchMessages(Request $request): JsonResponse
    {
        if (!session('chat_unlocked')) {
            return response()->json(['error' => 'Unauthorized. Chat is locked.'], 403);
        }

        $afterId = $request->query('after_id');

        if ($afterId && is_numeric($afterId) && $afterId > 0) {
            $messages = ChatMessage::where('id', '>', (int)$afterId)
                ->orderBy('id', 'asc')
                ->get();
        } else {
            $messages = ChatMessage::orderBy('id', 'desc')
                ->take(100)
                ->get()
                ->reverse()
                ->values();
        }

        return response()->json([
            'status' => 'success',
            'messages' => $messages,
        ]);
    }

    /**
     * Store and broadcast a newly typed message.
     */
    public function sendMessage(Request $request): JsonResponse
    {
        if (!session('chat_unlocked')) {
            return response()->json(['error' => 'Unauthorized. Chat is locked.'], 403);
        }

        $request->validate([
            'message' => ['required', 'string', 'max:5000'],
            'sender_name' => ['required', 'string', 'max:50'],
        ]);

        $message = ChatMessage::create([
            'sender_name' => trim($request->sender_name),
            'message' => trim($request->message),
            'is_read' => false,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => $message,
        ], 201);
    }

    /**
     * Update participant names.
     */
    public function updateNames(Request $request): JsonResponse|RedirectResponse
    {
        if (!session('chat_unlocked')) {
            return response()->json(['error' => 'Unauthorized. Chat is locked.'], 403);
        }

        $request->validate([
            'name1' => ['required', 'string', 'max:50'],
            'name2' => ['required', 'string', 'max:50'],
        ]);

        ChatSetting::setParticipantNames($request->name1, $request->name2);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Participant names updated successfully!',
                'name1' => trim($request->name1),
                'name2' => trim($request->name2),
            ]);
        }

        return redirect()->route('chat.index')->with('success', 'Participant names updated!');
    }

    /**
     * Clear all chat history.
     */
    public function clearChat(Request $request): JsonResponse
    {
        if (!session('chat_unlocked')) {
            return response()->json(['error' => 'Unauthorized. Chat is locked.'], 403);
        }

        ChatMessage::truncate();

        return response()->json([
            'status' => 'success',
            'message' => 'Chat conversation cleared successfully.',
        ]);
    }
}
