<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;










use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class ChatController extends Controller
{
    /**
     * Display the main chat view or PIN lock/setup screen.
     */
    /**
     * Display the main chat view or PIN lock/setup screen.
     */
    public function index(Request $request): View
    {
        // Whenever a user visits or reloads /chat, always reset the unlocked session
        // so that the lock code is strictly required on every fresh load / refresh.
        session()->forget('chat_unlocked');

        $isConfigured = ChatSetting::isPinConfigured();
        $isUnlocked = false;
        $names = ChatSetting::getParticipantNames();
        $name1 = $names['name1'];
        $name2 = $names['name2'];

        $state = $isConfigured ? 'locked' : 'setup_pin';

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
                'unlocked' => true,
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
                    'unlocked' => true,
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
     * Fetch all recent messages or delta messages after a specific ID with presence heartbeat.
     * Highly optimized for cloud MySQL to minimize latency and round-trips.
     */
    public function fetchMessages(Request $request): JsonResponse
    {
        if (!session('chat_unlocked')) {
            return response()->json(['error' => 'Unauthorized. Chat is locked.'], 403);
        }

        $afterId = $request->query('after_id');
        $currentUser = trim($request->query('current_user', ''));
        $isTyping = filter_var($request->query('is_typing', false), FILTER_VALIDATE_BOOLEAN);
        $activeStatus = filter_var($request->query('active_status', true), FILTER_VALIDATE_BOOLEAN);
        $readReceipts = filter_var($request->query('read_receipts', true), FILTER_VALIDATE_BOOLEAN);

        // Identify other participant
        $names = ChatSetting::getParticipantNames();
        $otherUser = (strtolower($currentUser) === strtolower($names['name1'])) ? $names['name2'] : $names['name1'];

        $presenceKey = 'presence_' . md5(strtolower($currentUser));
        $otherKey = 'presence_' . md5(strtolower($otherUser));

        // Fetch settings and presences in ONE single batch query
        $settings = ChatSetting::whereIn('key', [$presenceKey, $otherKey, 'chat_theme'])
            ->get()
            ->keyBy('key');

        // High-performance throttled presence update for current user
        if (!empty($currentUser)) {
            $nowTs = now()->timestamp;
            $shouldUpdate = false;
            $currRecord = $settings->get($presenceKey);

            if (!$currRecord) {
                $shouldUpdate = true;
            } else {
                $currData = json_decode($currRecord->value, true);
                if (!$currData) {
                    $shouldUpdate = true;
                } else {
                    $lastSeen = (int)($currData['last_seen'] ?? 0);
                    $storedTyping = (bool)($currData['is_typing'] ?? false);
                    $storedActive = (bool)($currData['active_status'] ?? true);

                    // Only write if typing status flipped, active status changed, or >8s heartbeat passed
                    if ($storedTyping !== $isTyping || $storedActive !== $activeStatus || ($nowTs - $lastSeen) >= 8) {
                        $shouldUpdate = true;
                    }
                }
            }

            if ($shouldUpdate) {
                ChatSetting::updateOrCreate(
                    ['key' => $presenceKey],
                    ['value' => json_encode([
                        'user' => $currentUser,
                        'last_seen' => $nowTs,
                        'is_typing' => $isTyping,
                        'active_status' => $activeStatus,
                    ])]
                );
            }
        }

        // Only update incoming messages as read if unread messages actually exist
        if ($readReceipts && !empty($currentUser)) {
            $hasUnread = ChatMessage::where('sender_name', '!=', $currentUser)
                ->where('is_read', false)
                ->exists();
            if ($hasUnread) {
                ChatMessage::where('sender_name', '!=', $currentUser)
                    ->where('is_read', false)
                    ->update(['is_read' => true]);
            }
        }

        // Fetch messages (NEVER return deleted messages)
        if ($afterId && is_numeric($afterId) && $afterId > 0) {
            $messages = ChatMessage::where('id', '>', (int)$afterId)
                ->where('is_deleted', false)
                ->orderBy('id', 'asc')
                ->get();
        } else {
            $messages = ChatMessage::where('is_deleted', false)
                ->orderBy('id', 'desc')
                ->take(100)
                ->get()
                ->reverse()
                ->values();
        }

        // Also fetch any updated messages in the last 15 seconds (for edits, unsends, reactions, pins)
        $updatedMessages = [];
        if ($afterId && is_numeric($afterId) && $afterId > 0) {
            $updatedMessages = ChatMessage::where('id', '<=', (int)$afterId)
                ->where('updated_at', '>=', now()->subSeconds(15))
                ->get();
        }

        // Calculate other presence
        $otherPresence = [
            'is_online' => false,
            'is_typing' => false,
            'status_text' => 'Offline',
        ];

        $otherRecord = $settings->get($otherKey);
        if (!$otherRecord) {
            $otherRecord = ChatSetting::where('key', 'like', 'presence_%')
                ->where('key', '!=', $presenceKey)
                ->latest('updated_at')
                ->first();
        }

        if ($otherRecord && !empty($otherRecord->value)) {
            $pData = json_decode($otherRecord->value, true);
            if ($pData && !empty($pData['active_status'])) {
                $lastSeen = (int)($pData['last_seen'] ?? 0);
                $diffSeconds = now()->timestamp - $lastSeen;
                if ($diffSeconds <= 20) {
                    $otherPresence['is_online'] = true;
                    $otherPresence['is_typing'] = !empty($pData['is_typing']) && ($diffSeconds <= 8);
                    $otherPresence['status_text'] = $otherPresence['is_typing'] ? 'typing...' : 'Active now';
                } elseif ($diffSeconds < 86400) {
                    $otherPresence['is_online'] = false;
                    $otherPresence['is_typing'] = false;
                    $otherPresence['status_text'] = 'Last seen ' . Carbon::createFromTimestamp($lastSeen)->diffForHumans();
                } else {
                    $otherPresence['is_online'] = false;
                    $otherPresence['is_typing'] = false;
                    $otherPresence['status_text'] = 'Offline';
                }
            }
        }

        // Pinned message
        $pinnedMessage = ChatMessage::where('is_pinned', true)
            ->where('is_deleted', false)
            ->latest('id')
            ->first();

        // Theme from cached settings collection
        $themeSetting = $settings->get('chat_theme');
        $theme = $themeSetting ? $themeSetting->value : 'midnight';

        return response()->json([
            'status' => 'success',
            'messages' => $messages,
            'updated_messages' => $updatedMessages,
            'other_presence' => $otherPresence,
            'pinned_message' => $pinnedMessage,
            'theme' => $theme,
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
            'reply_to_id' => ['nullable', 'integer'],
            'reply_to_text' => ['nullable', 'string', 'max:500'],
            'reply_to_sender' => ['nullable', 'string', 'max:50'],
        ]);

        $message = ChatMessage::create([
            'sender_name' => trim($request->sender_name),
            'message' => trim($request->message),
            'reply_to_id' => $request->reply_to_id,
            'reply_to_text' => $request->reply_to_text ? trim($request->reply_to_text) : null,
            'reply_to_sender' => $request->reply_to_sender ? trim($request->reply_to_sender) : null,
            'is_read' => false,
            'reactions' => [],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => $message,
        ], 201);
    }

    /**
     * Toggle reaction on a message.
     */
    public function reactMessage(Request $request): JsonResponse
    {
        if (!session('chat_unlocked')) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        $messageId = $request->input('message_id', $request->input('id'));
        $emoji = trim((string)$request->input('emoji', ''));
        $sender = trim((string)$request->input('sender_name', $request->input('user', '')));

        if (!$messageId || empty($emoji) || empty($sender)) {
            return response()->json([
                'error' => 'The message_id, emoji, and sender_name (or user) fields are required.'
            ], 422);
        }

        $message = ChatMessage::findOrFail($messageId);
        $reactions = $message->reactions ?? [];
        if (!is_array($reactions)) {
            $reactions = [];
        }

        if (!isset($reactions[$emoji])) {
            $reactions[$emoji] = [];
        }

        if (in_array($sender, $reactions[$emoji])) {
            $reactions[$emoji] = array_values(array_diff($reactions[$emoji], [$sender]));
            if (empty($reactions[$emoji])) {
                unset($reactions[$emoji]);
            }
        } else {
            $reactions[$emoji][] = $sender;
        }

        $message->reactions = $reactions;
        $message->touch();
        $message->save();

        return response()->json([
            'status' => 'success',
            'message' => $message,
        ]);
    }

    /**
     * Edit a sent message.
     */
    public function editMessage(Request $request): JsonResponse
    {
        if (!session('chat_unlocked')) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        $messageId = $request->input('message_id', $request->input('id'));
        $text = trim((string)$request->input('message', ''));

        if (!$messageId || empty($text)) {
            return response()->json(['error' => 'The message_id and message fields are required.'], 422);
        }

        $msg = ChatMessage::findOrFail($messageId);
        if ($msg->is_deleted) {
            return response()->json(['error' => 'Cannot edit a deleted message.'], 422);
        }

        $msg->message = $text;
        $msg->is_edited = true;
        $msg->touch();
        $msg->save();

        return response()->json([
            'status' => 'success',
            'message' => $msg,
        ]);
    }

    /**
     * Unsend / soft-delete message for everyone (completely clears message).
     */
    public function deleteMessage($request = null, $id = null): JsonResponse
    {
        if (!session('chat_unlocked')) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        if ($request instanceof Request) {
            $msgId = $id ?? $request->route('id') ?? $request->input('id') ?? $request->input('message_id');
        } else {
            $msgId = $request ?? $id;
        }

        $msg = ChatMessage::findOrFail($msgId);
        $msg->is_deleted = true;
        $msg->message = '';
        $msg->reactions = [];
        $msg->touch();
        $msg->save();

        return response()->json([
            'status' => 'success',
            'message' => $msg,
        ]);
    }

    /**
     * Toggle pinned status on a message.
     */
    public function togglePin(Request $request): JsonResponse
    {
        if (!session('chat_unlocked')) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        $messageId = $request->input('message_id', $request->input('id'));
        if (!$messageId) {
            return response()->json(['error' => 'The message_id field is required.'], 422);
        }

        $msg = ChatMessage::findOrFail($messageId);
        $msg->is_pinned = !$msg->is_pinned;
        $msg->touch();
        $msg->save();

        return response()->json([
            'status' => 'success',
            'is_pinned' => $msg->is_pinned,
            'message' => $msg,
        ]);
    }

    /**
     * Update chat preferences (theme, privacy).
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        if (!session('chat_unlocked')) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        if ($request->has('theme')) {
            ChatSetting::updateOrCreate(
                ['key' => 'chat_theme'],
                ['value' => trim($request->theme)]
            );
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Preferences updated.',
        ]);
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

    /**
     * Get unread messages count for the sidebar badge.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $currentUser = trim($request->query('user', ''));

        $query = ChatMessage::where('is_read', false)
            ->where('is_deleted', false);

        if (!empty($currentUser)) {
            $query->where('sender_name', '!=', $currentUser);
        }

        $count = $query->count();

        return response()->json([
            'unread_count' => $count,
        ]);
    }
}
