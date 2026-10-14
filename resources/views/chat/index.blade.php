@extends('layouts.dashboard')

@section('title', 'Support Logs - Doc Tracker')

@section('content')
<div class="page-header mb-3">
    <h3 class="fw-bold mb-2">Support Logs</h3>
    <ul class="breadcrumbs mb-0">
        <li class="nav-home">
            <a href="{{ route('products.index') }}">
                <i class="icon-home"></i>
            </a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="#">Operations</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="#">Support Logs</a>
        </li>
    </ul>
</div>

@if($state === 'setup_pin' || $state === 'locked')
<!-- ========================================== -->
<!-- PIN SECURITY VERIFICATION SCREEN           -->
<!-- ========================================== -->
<div class="row justify-content-center py-4">
    <div class="col-md-6 col-lg-5 col-xl-4">
        <div class="card shadow-sm border text-center" style="border-radius: 16px;">
            <div class="card-header telegram-header text-white py-3" style="border-radius: 16px 16px 0 0;">
                <div class="card-title text-white mb-0 fs-5">
                    <i class="fas {{ $state === 'setup_pin' ? 'fa-key' : 'fa-lock' }} me-2"></i>
                    {{ $state === 'setup_pin' ? 'Security PIN Setup' : 'Security Verification' }}
                </div>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-4">
                    {{ $state === 'setup_pin' ? 'Create a 4-digit security PIN to initialize this support session.' : 'Enter your 4-digit security PIN code to access the support log.' }}
                </p>

                <!-- Alert message container -->
                <div id="pinAlert" class="alert alert-danger py-2 px-3 small d-none mb-3" style="border-radius: 10px;"></div>

                <!-- Visual PIN Dots -->
                <div class="d-flex justify-content-center gap-3 mb-4" id="pinDots">
                    <div class="pin-dot" id="dot-0"></div>
                    <div class="pin-dot" id="dot-1"></div>
                    <div class="pin-dot" id="dot-2"></div>
                    <div class="pin-dot" id="dot-3"></div>
                </div>

                <!-- Hidden Form for submission -->
                <form id="pinForm" method="POST" action="{{ $state === 'setup_pin' ? route('chat.setupPin') : route('chat.verifyPin') }}">
                    @csrf
                    <input type="password" id="pinInput" name="pin" maxlength="4" pattern="\d*" inputmode="numeric" style="display: none;" autocomplete="off">
                </form>

                <!-- Keypad -->
                <div class="keypad-grid mb-3">
                    @foreach([1, 2, 3, 4, 5, 6, 7, 8, 9] as $num)
                        <button type="button" class="btn btn-pin-key" onclick="enterDigit('{{ $num }}')">{{ $num }}</button>
                    @endforeach
                    <button type="button" class="btn btn-pin-key-sub text-danger" onclick="clearPin()" title="Clear">
                        <i class="fas fa-undo"></i>
                    </button>
                    <button type="button" class="btn btn-pin-key" onclick="enterDigit('0')">0</button>
                    <button type="button" class="btn btn-pin-key-sub text-muted" onclick="backspacePin()" title="Backspace">
                        <i class="fas fa-backspace"></i>
                    </button>
                </div>

                <div class="d-grid mt-4">
                    <button type="button" id="btnSubmitPin" class="btn btn-primary btn-round fw-bold py-2" style="background: #3390ec; border-color: #3390ec;" onclick="submitPin()">
                        <i class="fas fa-unlock-alt me-2"></i> {{ $state === 'setup_pin' ? 'Set PIN & Continue' : 'Verify & Unlock' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@else
<!-- ========================================== -->
<!-- SUPPORT LOG / INTERNAL MESSENGER ROOM      -->
<!-- ========================================== -->
<div class="chat-page-wrapper">
    <div class="card shadow-sm border chat-main-card" id="chatMainCard">
        
        <!-- CARD HEADER (TELEGRAM THEME) -->
        <div class="card-header telegram-header text-white py-2 px-2 px-sm-3 flex-shrink-0">
            <div class="d-flex align-items-center justify-content-between flex-nowrap gap-2 w-100">
                <!-- Left Title -->
                <div class="d-flex align-items-center gap-2 min-w-0">
                    <div class="telegram-header-avatar flex-shrink-0">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="text-truncate">
                        <h4 class="card-title text-white mb-0 fs-6 fw-bold text-truncate" style="letter-spacing: -0.2px;">
                            <span class="d-none d-sm-inline">Support Communication Log</span>
                            <span class="d-sm-none">Support Log</span>
                        </h4>
                        <div class="text-white-50 d-flex align-items-center gap-1" style="font-size: 0.68rem;">
                            <span class="telegram-online-dot"></span> <span>Active Session</span>
                            <span id="blurBadge" class="badge bg-warning text-dark px-1 py-0 ms-1 d-none" style="font-size: 0.6rem;">
                                <i class="fas fa-eye-slash"></i> Blur
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Right Toolbar Buttons -->
                <div class="d-flex align-items-center gap-1 flex-nowrap ms-auto flex-shrink-0">
                    <!-- White / Black Background Theme Toggle (Always visible) -->
                    <button type="button" class="btn btn-sm btn-telegram-header btn-round px-2" id="btnThemeToggle" onclick="toggleChatTheme()" title="Toggle White / Black Background">
                        <i class="fas fa-sun" id="themeIcon"></i> <span class="d-none d-md-inline ms-1" id="themeBtnText">White BG</span>
                    </button>

                    <!-- Privacy Blur Toggle Button (Always visible) -->
                    <button type="button" class="btn btn-sm btn-telegram-header btn-round px-2 px-sm-2" id="btnBlurToggle" onclick="togglePrivacyBlur()" title="Toggle Privacy Blur">
                        <i class="fas fa-eye" id="blurIcon"></i> <span class="d-none d-md-inline ms-1" id="blurBtnText">Blur</span>
                    </button>

                    <!-- Desktop Extra Buttons (Visible on tablet & desktop >= 768px) -->
                    <div class="d-none d-md-flex align-items-center gap-1">
                        <!-- Participant Settings -->
                        <button type="button" class="btn btn-sm btn-telegram-header btn-round px-2" data-bs-toggle="modal" data-bs-target="#editNamesModal" title="Edit Participant Labels">
                            <i class="fas fa-user-edit"></i> <span class="d-none d-lg-inline ms-1">Labels</span>
                        </button>

                        <!-- Notification Chime Toggle -->
                        <button type="button" class="btn btn-sm btn-telegram-header btn-round px-2" id="btnSoundToggle" onclick="toggleSound()" title="Toggle Notification Sound">
                            <i class="fas fa-volume-up" id="soundIcon"></i>
                        </button>

                        <!-- Clear Log -->
                        <button type="button" class="btn btn-sm btn-telegram-header btn-round px-2" onclick="confirmClearChat()" title="Clear Log History">
                            <i class="fas fa-trash-alt"></i>
                        </button>

                        <!-- Lock Session -->
                        <form method="POST" action="{{ route('chat.lock') }}" class="d-inline m-0">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-telegram-header btn-round text-danger px-2" title="Lock Session">
                                <i class="fas fa-lock"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Mobile 3-Dots Dropdown Menu (Visible on mobile < 768px) -->
                    <div class="dropdown d-md-none">
                        <button type="button" class="btn btn-sm btn-telegram-header btn-round px-2" data-bs-toggle="dropdown" aria-expanded="false" title="More Options">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border telegram-dropdown-menu">
                            <li>
                                <a class="dropdown-item py-2" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#editNamesModal">
                                    <i class="fas fa-user-edit me-2 text-primary"></i> Edit Labels
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="javascript:void(0)" onclick="toggleSound()">
                                    <i class="fas fa-volume-up me-2 text-info" id="mobileSoundIcon"></i> <span id="mobileSoundText">Sound Notification</span>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item py-2 text-danger" href="javascript:void(0)" onclick="confirmClearChat()">
                                    <i class="fas fa-trash-alt me-2"></i> Clear History
                                </a>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('chat.lock') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger">
                                        <i class="fas fa-lock me-2"></i> Lock Session
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD BODY / MESSAGES STREAM (LEFT & RIGHT SEPARATION) -->
        <div class="card-body p-2 p-sm-3 chat-messages-container flex-grow-1" id="chatContainer">
            
            <!-- Loading State -->
            <div id="loadingChat" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading logs...</span>
                </div>
                <p class="text-muted small mt-2">Syncing communication log...</p>
            </div>

            <!-- Empty State -->
            <div id="emptyChat" class="text-center py-5 d-none">
                <div class="mb-3">
                    <i class="fas fa-inbox fa-3x text-muted opacity-25"></i>
                </div>
                <h6 class="fw-bold text-dark">No entries found</h6>
                <p class="text-muted small">Type a message below to start communicating.</p>
            </div>

            <!-- Messages Stream -->
            <div id="messagesStream"></div>
        </div>

        <!-- SCROLL DOWN FLOATING PILL -->
        <button type="button" class="btn btn-dark btn-round shadow-sm scroll-down-btn d-none" id="btnScrollBottom" onclick="scrollToBottom(true)">
            <i class="fas fa-arrow-down me-1"></i> Latest Entries
        </button>

        <!-- FULL EMOJI PICKER POPUP (OPENS ONLY ON EMOJI BUTTON CLICK) -->
        <div id="emojiPickerPopup" class="emoji-picker-drawer shadow-lg border d-none">
            <div class="d-flex align-items-center justify-content-between p-2 border-bottom bg-light">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="fa fa-search text-muted"></i></span>
                    <input type="text" id="emojiSearchInput" class="form-control border-start-0" placeholder="Search emojis..." onkeyup="filterEmojis()">
                </div>
                <button type="button" class="btn btn-sm btn-link text-muted ms-2 p-0 text-decoration-none" onclick="toggleEmojiPicker()"><i class="fas fa-times fs-6"></i></button>
            </div>

            <!-- Category Tabs -->
            <div class="d-flex gap-1 p-2 border-bottom bg-white overflow-x-auto emoji-category-tabs">
                <button type="button" class="btn btn-sm btn-emoji-tab active" onclick="switchEmojiCategory('all', event)">All</button>
                <button type="button" class="btn btn-sm btn-emoji-tab" onclick="switchEmojiCategory('smileys', event)">😀 Faces</button>
                <button type="button" class="btn btn-sm btn-emoji-tab" onclick="switchEmojiCategory('gestures', event)">👍 Hands</button>
                <button type="button" class="btn btn-sm btn-emoji-tab" onclick="switchEmojiCategory('hearts', event)">❤️ Hearts</button>
                <button type="button" class="btn btn-sm btn-emoji-tab" onclick="switchEmojiCategory('objects', event)">🚀 Objects</button>
                <button type="button" class="btn btn-sm btn-emoji-tab" onclick="switchEmojiCategory('animals', event)">🐶 Animals</button>
                <button type="button" class="btn btn-sm btn-emoji-tab" onclick="switchEmojiCategory('food', event)">🍔 Food</button>
            </div>

            <!-- Emoji Grid -->
            <div class="emoji-grid p-2" id="emojiGrid"></div>
        </div>

        <!-- CARD FOOTER (TELEGRAM INPUT BAR) -->
        <div class="card-footer bg-white py-2 px-2 px-sm-3 border-top flex-shrink-0 position-relative telegram-footer">
            
            <!-- Top Row: Active Sender Capsule Switcher -->
            <div class="d-flex align-items-center justify-content-between mb-1 mb-sm-2 flex-wrap gap-1">
                <div class="d-flex align-items-center gap-1 gap-sm-2 active-sender-wrap">
                    <span class="text-muted small fw-semibold text-nowrap" style="font-size: 0.74rem;">
                        <i class="fas fa-user-circle me-1 text-primary"></i> Active:
                    </span>
                    <div class="telegram-persona-capsule" role="group">
                        <input type="radio" class="btn-check" name="activePersona" id="persona1" value="{{ $name1 }}" checked onchange="updateActivePersona(this.value)">
                        <label class="persona-tab sender-label-blur text-truncate" for="persona1" id="radioLabel1">{{ $name1 }}</label>

                        <input type="radio" class="btn-check" name="activePersona" id="persona2" value="{{ $name2 }}" onchange="updateActivePersona(this.value)">
                        <label class="persona-tab sender-label-blur text-truncate" for="persona2" id="radioLabel2">{{ $name2 }}</label>
                    </div>
                </div>
                <small class="text-muted d-none d-sm-inline" style="font-size: 0.72rem;">Press <strong>Enter</strong> to send</small>
            </div>

            <!-- Input Row: [Emoji Icon] [Telegram Input Box] [Circular Send Button] -->
            <div class="chat-input-bar-wrap">
                <div class="chat-input-inner">
                    <!-- Emoji Button -->
                    <button type="button" class="btn btn-emoji-trigger" onclick="toggleEmojiPicker()" title="Browse all emojis">
                        <i class="far fa-smile"></i>
                    </button>

                    <!-- Message Textarea Box -->
                    <div class="input-field-box">
                        <textarea 
                            id="messageInput" 
                            name="message"
                            class="form-control message-textarea" 
                            rows="1" 
                            placeholder="Write a message..."
                            onkeydown="handleInputKey(event)"
                            oninput="autoResizeTextarea(this)"></textarea>
                    </div>

                    <!-- Single Send Action Button (Telegram Round Plane Button) -->
                    <button type="button" class="btn btn-send-main" onclick="sendMessage(activePersona)" id="btnMainSend" title="Send message">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- ========================================== -->
<!-- EDIT PARTICIPANT LABELS MODAL              -->
<!-- ========================================== -->
<div class="modal fade" id="editNamesModal" tabindex="-1" aria-labelledby="editNamesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border shadow" style="border-radius: 14px;">
            <div class="modal-header telegram-header text-white py-3" style="border-radius: 14px 14px 0 0;">
                <h5 class="modal-title fw-bold text-white" id="editNamesModalLabel">
                    <i class="fas fa-user-edit me-2 text-white"></i> Edit Participant Labels
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-3">Customize the display labels for both participants in this support log.</p>
                
                <div class="mb-3">
                    <label for="inputName1" class="form-label fw-bold text-dark">Participant 1</label>
                    <input type="text" class="form-control" id="inputName1" value="{{ $name1 }}" placeholder="e.g. User 1" maxlength="50" required>
                </div>

                <div class="mb-3">
                    <label for="inputName2" class="form-label fw-bold text-dark">Participant 2</label>
                    <input type="text" class="form-control" id="inputName2" value="{{ $name2 }}" placeholder="e.g. User 2" maxlength="50" required>
                </div>

                <div id="namesAlert" class="alert alert-danger py-2 px-3 small d-none"></div>
            </div>
            <div class="modal-footer border-top py-2">
                <button type="button" class="btn btn-secondary btn-round" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-round fw-bold px-4" style="background: #3390ec; border-color: #3390ec;" onclick="saveParticipantNames()"><i class="fas fa-save me-1"></i> Save Changes</button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- STYLES -->
<style>
@if($state === 'unlocked')
/* Full Viewport Fit for Active Chat Session: No outer page scroll */
html, body {
    height: 100%;
    height: 100dvh;
    overflow: hidden !important;
}
.main-panel {
    height: 100vh !important;
    height: 100dvh !important;
    max-height: 100vh !important;
    max-height: 100dvh !important;
    overflow: hidden !important;
    display: flex !important;
    flex-direction: column !important;
}
.main-panel > .container {
    margin-top: 65px !important;
    margin-bottom: 0 !important;
    padding: 0 !important;
    height: calc(100vh - 65px) !important;
    height: calc(100dvh - 65px) !important;
    max-height: calc(100vh - 65px) !important;
    max-height: calc(100dvh - 65px) !important;
    overflow: hidden !important;
    display: flex !important;
    flex-direction: column !important;
    flex: 1 1 0 !important;
    min-height: 0 !important;
}
@media (max-width: 991.98px) {
    .main-panel > .container {
        margin-top: 56px !important;
        height: calc(100dvh - 56px) !important;
        max-height: calc(100dvh - 56px) !important;
    }
}
.main-panel .page-inner {
    padding: 8px 12px 8px 12px !important;
    height: 100% !important;
    max-height: 100% !important;
    min-height: 0 !important;
    overflow: hidden !important;
    display: flex !important;
    flex-direction: column !important;
    flex: 1 1 0 !important;
}
@media (max-width: 575.98px) {
    .main-panel .page-inner {
        padding: 4px 6px !important;
    }
}
@else
/* PIN Security Verification Screen: Allow scrolling so keypad & unlock button are accessible */
.main-panel {
    min-height: 100vh;
    min-height: 100dvh;
    overflow-y: auto !important;
}
.main-panel > .container {
    margin-top: 65px !important;
    min-height: calc(100vh - 65px) !important;
}
@media (max-width: 991.98px) {
    .main-panel > .container {
        margin-top: 56px !important;
        min-height: calc(100dvh - 56px) !important;
    }
}
.main-panel .page-inner {
    padding: 16px 12px !important;
    overflow-y: auto !important;
}
@endif

.main-panel .footer {
    display: none !important;
}
.page-header {
    display: none !important;
}

/* ========================================================= */
/* BLACK THEME: MAIN CARD & HEADER (MATCHING REFERENCE)      */
/* ========================================================= */
.chat-page-wrapper {
    height: 100% !important;
    max-height: 100% !important;
    min-height: 0 !important;
    flex: 1 1 0 !important;
    display: flex !important;
    flex-direction: column !important;
    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
}

.chat-main-card {
    height: 100% !important;
    max-height: 100% !important;
    min-height: 0 !important;
    flex: 1 1 0 !important;
    display: flex !important;
    flex-direction: column !important;
    border-radius: 12px !important;
    overflow: hidden !important;
    margin-bottom: 0 !important;
    border: 1px solid #262930 !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5) !important;
}
@media (max-width: 575.98px) {
    .chat-main-card {
        border-radius: 8px !important;
    }
}

.telegram-header {
    background: #16181d !important;
    border-bottom: 1px solid #262930 !important;
    min-height: 48px !important;
    flex-shrink: 0 !important;
}
.telegram-header-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #3875f6;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 0.95rem;
}
.telegram-online-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #10b981;
    display: inline-block;
}
.btn-telegram-header {
    background: rgba(255, 255, 255, 0.1) !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    color: #ffffff !important;
    font-size: 0.78rem !important;
    padding: 4px 12px !important;
    transition: all 0.15s ease;
}
.btn-telegram-header:hover {
    background: rgba(255, 255, 255, 0.2) !important;
    color: #ffffff !important;
    transform: translateY(-1px);
}

/* ========================================================= */
/* PITCH BLACK MESSAGES STREAM (MATCHING REFERENCE SCREEN)   */
/* ========================================================= */
.chat-messages-container {
    background: #0d0f12 !important;
    overflow-y: auto !important;
    scroll-behavior: smooth;
    position: relative;
    flex: 1 1 auto !important;
    min-height: 0 !important;
}
.chat-messages-container::-webkit-scrollbar {
    width: 6px;
}
.chat-messages-container::-webkit-scrollbar-thumb {
    background: #2a2d35;
    border-radius: 10px;
}

#messagesStream {
    display: flex;
    flex-direction: column;
    gap: 10px;
    width: 100%;
}

/* ========================================================= */
/* EXACT BUBBLES MATCH: ROYAL BLUE (RIGHT) & CHARCOAL (LEFT) */
/* ========================================================= */
.message-row {
    display: flex;
    width: 100%;
    margin-bottom: 2px;
    animation: bubbleSlide 0.18s ease;
}
@keyframes bubbleSlide {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}

/* SENDER 1 (User 1) = OUTGOING (RIGHT) - VIBRANT ROYAL BLUE */
.message-row.sender-1-row {
    justify-content: flex-end !important;
}
.message-row.sender-1-row .message-wrapper {
    display: flex;
    flex-direction: row-reverse;
    align-items: flex-end;
    gap: 8px;
    max-width: 72%;
}
.message-row.sender-1-row .message-bubble {
    background: #3875f6 !important;
    color: #ffffff !important;
    border-radius: 18px 18px 4px 18px !important;
    padding: 8px 16px !important;
    box-shadow: 0 2px 5px rgba(56, 117, 246, 0.25) !important;
    border: none !important;
}
.message-row.sender-1-row .bubble-sender-name {
    color: #dbeafe !important;
    font-size: 0.74rem !important;
    font-weight: 600;
}
.message-row.sender-1-row .bubble-meta {
    justify-content: flex-end;
    color: rgba(255, 255, 255, 0.8) !important;
    font-size: 0.68rem !important;
}
.avatar-sender-1 {
    background: #3875f6 !important;
}

/* SENDER 2 (User 2) = INCOMING (LEFT) - DARK CHARCOAL */
.message-row.sender-2-row {
    justify-content: flex-start !important;
}
.message-row.sender-2-row .message-wrapper {
    display: flex;
    flex-direction: row;
    align-items: flex-end;
    gap: 8px;
    max-width: 72%;
}
.message-row.sender-2-row .message-bubble {
    background: #25282e !important;
    color: #ffffff !important;
    border-radius: 18px 18px 18px 4px !important;
    padding: 8px 16px !important;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2) !important;
    border: none !important;
}
.message-row.sender-2-row .bubble-sender-name {
    color: #94a3b8 !important;
    font-size: 0.74rem !important;
    font-weight: 600;
}
.message-row.sender-2-row .bubble-meta {
    justify-content: flex-start;
    color: #94a3b8 !important;
    font-size: 0.68rem !important;
}
.avatar-sender-2 {
    background: #475569 !important;
}

.bubble-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.78rem;
    font-weight: 700;
    color: #ffffff;
    flex-shrink: 0;
}

/* FLOATING DARK DATE DIVIDERS */
.date-divider {
    text-align: center;
    position: relative;
    margin: 12px 0;
}
.date-divider span {
    background: #1c1e24 !important;
    color: #94a3b8 !important;
    border: 1px solid #2e313a !important;
    font-size: 0.72rem !important;
    font-weight: 600 !important;
    padding: 3px 12px !important;
    border-radius: 12px !important;
}

/* ========================================================= */
/* DARK INPUT BAR & CAPSULE SWITCHER                         */
/* ========================================================= */
.telegram-footer {
    background: #16181d !important;
    border-top: 1px solid #262930 !important;
}

.telegram-persona-capsule {
    display: inline-flex;
    background: #212329;
    padding: 2px;
    border-radius: 20px;
    border: 1px solid #2c2f38;
}
.telegram-persona-capsule .persona-tab {
    padding: 3px 14px;
    font-size: 0.78rem;
    font-weight: 600;
    color: #94a3b8;
    border-radius: 18px;
    cursor: pointer;
    transition: all 0.15s ease;
    user-select: none;
    margin-bottom: 0;
}
.telegram-persona-capsule input[type="radio"]:checked + .persona-tab {
    background: #3875f6 !important;
    color: #ffffff !important;
    box-shadow: 0 1px 3px rgba(56, 117, 246, 0.4);
}

.chat-input-bar-wrap {
    width: 100%;
    display: block;
}
.chat-input-inner {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
}
.btn-emoji-trigger {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    padding: 0;
    color: #94a3b8;
    border: 1px solid #2c2f38;
    background: #212329;
    transition: all 0.15s ease;
}
.btn-emoji-trigger:hover {
    color: #3875f6;
    border-color: #3875f6;
    background: #262930;
    transform: scale(1.05);
}
.input-field-box {
    flex: 1 1 auto;
    min-width: 0;
    width: 100%;
}
.message-textarea {
    width: 100% !important;
    min-height: 42px !important;
    max-height: 110px !important;
    border-radius: 20px !important;
    border: 1.5px solid #2c2f38 !important;
    background: #212329 !important;
    padding: 10px 16px !important;
    font-size: 14.5px !important;
    line-height: 1.4 !important;
    color: #ffffff !important;
    resize: none !important;
    outline: none !important;
    box-shadow: none !important;
    transition: border-color 0.15s ease;
}
.message-textarea::placeholder {
    color: #64748b !important;
    opacity: 1 !important;
}
.message-textarea:focus {
    border-color: #3875f6 !important;
    box-shadow: 0 0 0 2px rgba(56, 117, 246, 0.25) !important;
}

/* CIRCULAR ROYAL BLUE SEND BUTTON */
.btn-send-main {
    width: 42px !important;
    height: 42px !important;
    border-radius: 50% !important;
    background: #3875f6 !important;
    color: #ffffff !important;
    padding: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    box-shadow: 0 2px 8px rgba(56, 117, 246, 0.4) !important;
    flex-shrink: 0;
    border: none !important;
    font-size: 1rem !important;
    transition: all 0.15s ease;
}
.btn-send-main:hover {
    background: #2563eb !important;
    transform: scale(1.08);
}

/* ========================================================= */
/* PRIVACY BLUR FEATURE                                      */
/* ========================================================= */
.privacy-blur-active .message-text,
.privacy-blur-active .bubble-sender-name,
.privacy-blur-active .bubble-avatar,
.privacy-blur-active .sender-label-blur,
.privacy-blur-active #radioLabel1,
.privacy-blur-active #radioLabel2 {
    filter: blur(6px);
    user-select: none;
    transition: filter 0.2s ease;
}
.privacy-blur-active .message-bubble:hover .message-text,
.privacy-blur-active .message-bubble:hover .bubble-sender-name,
.privacy-blur-active .message-row:hover .message-text,
.privacy-blur-active .message-row:hover .bubble-sender-name,
.privacy-blur-active .message-row:hover .bubble-avatar,
.privacy-blur-active .active-sender-wrap:hover .sender-label-blur,
.privacy-blur-active .active-sender-wrap:hover #radioLabel1,
.privacy-blur-active .active-sender-wrap:hover #radioLabel2,
.privacy-blur-active .sender-label-blur:hover {
    filter: blur(0px) !important;
    user-select: text;
}

/* ========================================================= */
/* PIN SCREEN & MODAL STYLES                                 */
/* ========================================================= */
.pin-dot {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #cbd5e1;
    background: transparent;
    transition: all 0.2s ease;
}
.pin-dot.filled {
    background: #3390ec;
    border-color: #3390ec;
    transform: scale(1.15);
}
.pin-dot.shake {
    animation: shakeAnim 0.35s ease-in-out;
}
@keyframes shakeAnim {
    0%, 100% { transform: translateX(0); }
    20%, 60% { transform: translateX(-6px); }
    40%, 80% { transform: translateX(6px); }
}
.keypad-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    max-width: 260px;
    margin: 0 auto;
}
.btn-pin-key {
    height: 52px;
    border-radius: 12px;
    font-size: 1.3rem;
    font-weight: 700;
    color: #2a2f5b;
    background: #f4f6f9;
    border: 1px solid #ebedf2;
    transition: all 0.15s ease;
}
.btn-pin-key:hover, .btn-pin-key:active {
    background: #ebedf2;
    color: #3390ec;
    transform: scale(1.04);
}
.btn-pin-key-sub {
    height: 52px;
    border-radius: 12px;
    font-size: 1.1rem;
    background: transparent;
    border: none;
    transition: all 0.15s ease;
}
.btn-pin-key-sub:hover {
    background: #f4f6f9;
}

/* Scroll Bottom Button */
.scroll-down-btn {
    position: absolute;
    bottom: 95px;
    right: 25px;
    z-index: 10;
    font-size: 0.8rem;
    font-weight: 600;
    padding: 6px 14px;
    background: #ffffff !important;
    color: #707579 !important;
    border: 1px solid #dfe5ec !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15) !important;
}

/* Emoji Picker Drawer */
.emoji-picker-drawer {
    position: absolute;
    bottom: 105px;
    left: 20px;
    width: 320px;
    max-width: 90vw;
    height: 300px;
    background: #16181d;
    border: 1px solid #2c2f38 !important;
    border-radius: 12px;
    z-index: 50;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.emoji-picker-drawer .bg-light {
    background: #1e2026 !important;
    border-color: #2c2f38 !important;
}
.emoji-picker-drawer .form-control {
    background: #16181d !important;
    border-color: #2c2f38 !important;
    color: #ffffff !important;
}
.emoji-picker-drawer .input-group-text {
    background: #16181d !important;
    border-color: #2c2f38 !important;
}
.emoji-category-tabs {
    background: #16181d !important;
    border-color: #2c2f38 !important;
}
.btn-emoji-tab {
    font-size: 0.75rem;
    border: none;
    background: #212329;
    color: #94a3b8;
    padding: 3px 8px;
    border-radius: 6px;
    white-space: nowrap;
}
.btn-emoji-tab.active {
    background: #3875f6;
    color: #ffffff;
    font-weight: 600;
}
.emoji-grid {
    flex: 1;
    overflow-y: auto;
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
    background: #16181d;
}
.emoji-grid-item {
    font-size: 1.3rem;
    padding: 5px;
    text-align: center;
    border-radius: 6px;
    cursor: pointer;
    user-select: none;
    transition: all 0.12s ease;
}
.emoji-grid-item:hover {
    background: #212329;
    transform: scale(1.2);
}

/* ========================================================= */
/* WHITE THEME / LIGHT MODE STYLES                           */
/* ========================================================= */
.chat-main-card.chat-theme-white {
    background: #ffffff !important;
    border-color: #e2e8f0 !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
}

.chat-main-card.chat-theme-white .telegram-header {
    background: #ffffff !important;
    border-bottom: 1px solid #e2e8f0 !important;
}
.chat-main-card.chat-theme-white .telegram-header h4 {
    color: #0f172a !important;
}
.chat-main-card.chat-theme-white .telegram-header .text-white-50 {
    color: #64748b !important;
}
.chat-main-card.chat-theme-white .btn-telegram-header {
    background: #f1f5f9 !important;
    border: 1px solid #e2e8f0 !important;
    color: #334155 !important;
}
.chat-main-card.chat-theme-white .btn-telegram-header:hover {
    background: #e2e8f0 !important;
    color: #0f172a !important;
}

.chat-main-card.chat-theme-white .chat-messages-container {
    background: #ffffff !important;
}
.chat-main-card.chat-theme-white .chat-messages-container::-webkit-scrollbar-thumb {
    background: #cbd5e1;
}

/* SENDER 2 (INCOMING / LEFT) IN WHITE THEME */
.chat-main-card.chat-theme-white .message-row.sender-2-row .message-bubble {
    background: #f1f5f9 !important;
    color: #0f172a !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) !important;
    border: 1px solid #e2e8f0 !important;
}
.chat-main-card.chat-theme-white .message-row.sender-2-row .bubble-sender-name {
    color: #475569 !important;
}
.chat-main-card.chat-theme-white .message-row.sender-2-row .bubble-meta {
    color: #64748b !important;
}
.chat-main-card.chat-theme-white .avatar-sender-2 {
    background: #94a3b8 !important;
}

/* SENDER 1 (OUTGOING / RIGHT) IN WHITE THEME */
.chat-main-card.chat-theme-white .message-row.sender-1-row .message-bubble {
    background: #3875f6 !important;
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(56, 117, 246, 0.25) !important;
}

/* DATE DIVIDERS IN WHITE THEME */
.chat-main-card.chat-theme-white .date-divider span {
    background: #f8fafc !important;
    color: #64748b !important;
    border-color: #e2e8f0 !important;
}

/* INPUT FOOTER & CAPSULE IN WHITE THEME */
.chat-main-card.chat-theme-white .telegram-footer {
    background: #ffffff !important;
    border-top: 1px solid #e2e8f0 !important;
}
.chat-main-card.chat-theme-white .telegram-persona-capsule {
    background: #f1f5f9 !important;
    border-color: #e2e8f0 !important;
}
.chat-main-card.chat-theme-white .telegram-persona-capsule .persona-tab {
    color: #64748b !important;
}
.chat-main-card.chat-theme-white .telegram-persona-capsule input[type="radio"]:checked + .persona-tab {
    background: #3875f6 !important;
    color: #ffffff !important;
}

.chat-main-card.chat-theme-white .btn-emoji-trigger {
    background: #f8fafc !important;
    border-color: #e2e8f0 !important;
    color: #64748b !important;
}
.chat-main-card.chat-theme-white .btn-emoji-trigger:hover {
    background: #f1f5f9 !important;
    color: #3875f6 !important;
    border-color: #3875f6 !important;
}

.chat-main-card.chat-theme-white .message-textarea {
    background: #f8fafc !important;
    border-color: #e2e8f0 !important;
    color: #0f172a !important;
}
.chat-main-card.chat-theme-white .message-textarea::placeholder {
    color: #94a3b8 !important;
}
.chat-main-card.chat-theme-white .message-textarea:focus {
    border-color: #3875f6 !important;
    box-shadow: 0 0 0 2px rgba(56, 117, 246, 0.2) !important;
}

/* EMOJI DRAWER IN WHITE THEME */
.chat-main-card.chat-theme-white .emoji-picker-drawer {
    background: #ffffff !important;
    border-color: #e2e8f0 !important;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
}
.chat-main-card.chat-theme-white .emoji-picker-drawer .bg-light {
    background: #f8fafc !important;
    border-color: #e2e8f0 !important;
}
.chat-main-card.chat-theme-white .emoji-picker-drawer .form-control {
    background: #ffffff !important;
    border-color: #e2e8f0 !important;
    color: #0f172a !important;
}
.chat-main-card.chat-theme-white .emoji-picker-drawer .input-group-text {
    background: #ffffff !important;
    border-color: #e2e8f0 !important;
}
.chat-main-card.chat-theme-white .emoji-category-tabs {
    background: #f8fafc !important;
    border-color: #e2e8f0 !important;
}
.chat-main-card.chat-theme-white .btn-emoji-tab {
    background: #e2e8f0 !important;
    color: #475569 !important;
}
.chat-main-card.chat-theme-white .btn-emoji-tab.active {
    background: #3875f6 !important;
    color: #ffffff !important;
}
.chat-main-card.chat-theme-white .emoji-grid {
    background: #ffffff !important;
}
.chat-main-card.chat-theme-white .emoji-grid-item:hover {
    background: #f1f5f9 !important;
}

/* ========================================================= */
/* TELEGRAM DROPDOWN MENU FOR MOBILE ACTIONS                 */
/* ========================================================= */
.telegram-dropdown-menu {
    background: #1e2026 !important;
    border: 1px solid #2c2f38 !important;
    border-radius: 12px !important;
    padding: 6px 0 !important;
    min-width: 200px !important;
    z-index: 1050 !important;
}
.telegram-dropdown-menu .dropdown-item {
    color: #e2e8f0 !important;
    font-size: 0.83rem !important;
    font-weight: 500 !important;
    padding: 8px 16px !important;
    display: flex !important;
    align-items: center !important;
    transition: background 0.15s ease;
}
.telegram-dropdown-menu .dropdown-item:hover,
.telegram-dropdown-menu .dropdown-item:focus {
    background: #282b34 !important;
    color: #ffffff !important;
}
.telegram-dropdown-menu .dropdown-divider {
    border-color: #2c2f38 !important;
    margin: 4px 0 !important;
}
.chat-main-card.chat-theme-white .telegram-dropdown-menu {
    background: #ffffff !important;
    border-color: #e2e8f0 !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12) !important;
}
.chat-main-card.chat-theme-white .telegram-dropdown-menu .dropdown-item {
    color: #334155 !important;
}
.chat-main-card.chat-theme-white .telegram-dropdown-menu .dropdown-item:hover,
.chat-main-card.chat-theme-white .telegram-dropdown-menu .dropdown-item:focus {
    background: #f1f5f9 !important;
    color: #0f172a !important;
}
.chat-main-card.chat-theme-white .telegram-dropdown-menu .dropdown-divider {
    border-color: #e2e8f0 !important;
}

/* ========================================================= */
/* MOBILE RESPONSIVENESS MEDIA QUERIES                       */
/* ========================================================= */
@media (max-width: 767.98px) {
    .message-row.sender-1-row .message-wrapper,
    .message-row.sender-2-row .message-wrapper {
        max-width: 85% !important;
    }
    .message-bubble {
        padding: 7px 12px !important;
        font-size: 0.88rem !important;
    }
    .btn-telegram-header {
        padding: 4px 8px !important;
        font-size: 0.75rem !important;
        height: 32px !important;
        min-width: 32px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .telegram-header-avatar {
        width: 32px !important;
        height: 32px !important;
        font-size: 0.85rem !important;
    }
    .bubble-avatar {
        width: 28px !important;
        height: 28px !important;
        font-size: 0.72rem !important;
    }
    .telegram-persona-capsule .persona-tab {
        padding: 2px 8px !important;
        font-size: 0.72rem !important;
        max-width: 95px !important;
    }
    .btn-emoji-trigger, .btn-send-main {
        width: 38px !important;
        height: 38px !important;
        font-size: 0.95rem !important;
    }
    .message-textarea {
        min-height: 38px !important;
        padding: 8px 12px !important;
        font-size: 16px !important; /* Prevents auto zoom on mobile */
    }
    .emoji-picker-drawer {
        width: calc(100vw - 20px) !important;
        left: 10px !important;
        bottom: 68px !important;
        height: 240px !important;
    }
    .emoji-grid {
        grid-template-columns: repeat(6, 1fr) !important;
    }
    .scroll-down-btn {
        bottom: 70px !important;
        right: 12px !important;
        font-size: 0.75rem !important;
        padding: 4px 10px !important;
    }
    .btn-pin-key, .btn-pin-key-sub {
        height: 48px !important;
        font-size: 1.2rem !important;
    }
    .keypad-grid {
        max-width: 230px !important;
        gap: 8px !important;
    }
    .pin-dot {
        width: 13px !important;
        height: 13px !important;
    }
}
</style>

<!-- JAVASCRIPT LOGIC -->
<script>
@if($state === 'setup_pin' || $state === 'locked')
    /* =================================================== */
    /* PIN KEYPAD & VERIFICATION SCRIPT                    */
    /* =================================================== */
    let currentPin = '';

    function enterDigit(digit) {
        if (currentPin.length < 4) {
            currentPin += digit;
            updatePinUi();
            if (currentPin.length === 4) {
                setTimeout(submitPin, 200);
            }
        }
    }

    function backspacePin() {
        if (currentPin.length > 0) {
            currentPin = currentPin.slice(0, -1);
            updatePinUi();
        }
    }

    function clearPin() {
        currentPin = '';
        updatePinUi();
    }

    function updatePinUi() {
        for (let i = 0; i < 4; i++) {
            const dot = document.getElementById('dot-' + i);
            if (dot) {
                if (i < currentPin.length) {
                    dot.classList.add('filled');
                } else {
                    dot.classList.remove('filled');
                }
            }
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key >= '0' && e.key <= '9') {
            enterDigit(e.key);
        } else if (e.key === 'Backspace') {
            backspacePin();
        } else if (e.key === 'Enter' && currentPin.length === 4) {
            submitPin();
        }
    });

    function submitPin() {
        if (currentPin.length !== 4) {
            showPinAlert('Please enter a 4-digit PIN code.');
            return;
        }

        const form = document.getElementById('pinForm');
        const alertBox = document.getElementById('pinAlert');
        const btn = document.getElementById('btnSubmitPin');

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Verifying...';

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ pin: currentPin })
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(response => {
            if (response.status === 200) {
                window.location.reload();
            } else {
                showPinAlert(response.body.message || 'Incorrect PIN code.');
                shakePinDots();
                clearPin();
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-unlock-alt me-2"></i> {{ $state === "setup_pin" ? "Set PIN & Continue" : "Verify & Unlock" }}';
            }
        })
        .catch(err => {
            showPinAlert('An error occurred. Please try again.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-unlock-alt me-2"></i> Verify & Unlock';
        });
    }

    function showPinAlert(msg) {
        const alertBox = document.getElementById('pinAlert');
        alertBox.textContent = msg;
        alertBox.classList.remove('d-none');
    }

    function shakePinDots() {
        const container = document.getElementById('pinDots');
        container.classList.add('shake');
        setTimeout(() => container.classList.remove('shake'), 400);
    }

@else
    /* =================================================== */
    /* REAL-TIME LOG ENGINE (Emoji, Blur, Single Send)     */
    /* =================================================== */
    let lastMessageId = 0;
    let isPolling = false;
    let pollInterval = null;
    let soundEnabled = localStorage.getItem('chat_sound_enabled') !== 'false';
    let blurEnabled = localStorage.getItem('chat_blur_enabled') === 'true';
    let themeMode = localStorage.getItem('chat_theme_mode') || 'black'; // 'black' or 'white'
    let participantNames = {
        name1: '{{ $name1 }}',
        name2: '{{ $name2 }}'
    };
    let savedPersona = localStorage.getItem('chat_active_persona');
    let activePersona = (savedPersona && (savedPersona === participantNames.name1 || savedPersona === participantNames.name2)) 
        ? savedPersona 
        : participantNames.name1;
    let audioCtx = null;

    // Emojis Dataset
    const emojiDatabase = {
        smileys: ['😀', '😃', '😄', '😁', '😆', '😅', '🤣', '😂', '🙂', '🙃', '😉', '😊', '😇', '🥰', '😍', '🤩', '😘', '😗', '😚', '😋', '😛', '😜', '🤪', '😝', '🤑', '🤗', '🤭', '🤫', '🤔', '🤐', '🤨', '😐', '😑', '😶', '😏', '😒', '🙄', '😬', '🤥', '😌', '😔', '😪', '🤤', '😴', '😷', '🤒', '🤕', '🤢', '🤮', '🤧', '🥵', '🥶', '🥴', '😵', '🤯', '🤠', '🥳', '😎', '🤓', '🧐', '😕', '😟', '🙁', '😮', '😯', '😲', '😳', '🥺', '😦', '😧', '😨', '😰', '😥', '😢', '😭', '😱', '😖', '😣', '😞', '😓', '😩', '😫', '🥱', '😤', '😡', '😠', '🤬', '😈', '👿', '💀', '💩', '🤡', '👻', '👽', '🤖'],
        gestures: ['👋', '🤚', '🖐️', '✋', '🖖', '👌', '🤌', '🤏', '✌️', '🤞', '🫰', '🤟', '🤘', '🤙', '👈', '👉', '👆', '🖕', '👇', '☝️', '👍', '👎', '✊', '👊', '🤛', '🤜', '👏', '🙌', '👐', '🤲', '🤝', '🙏', '✍️', '💅', '🤳', '💪', '🦾', '🦿', '🦵', '🦶', '👂', '🦻', '👃', '🧠', '🫀', '🫁', '🦷', '🦴', '👀', '👁️', '👅', '👄'],
        hearts: ['❤️', '🧡', '💛', '💚', '💙', '💜', '🖤', '🤍', '🤎', '💔', '❣️', '💕', '💞', '💓', '💗', '💖', '💘', '💝', '💟', '💌', '💋', '💯', '💢', '💥', '💫', '💦', '💨', '🕳️', '💣', '💬', '👁️‍🗨️', '🗨️', '🗯️', '💭', '💤'],
        objects: ['🚀', '🛸', '🛰️', '🪐', '⭐', '🌟', '✨', '⚡', '🔥', '💥', '🎉', '🎊', '🎈', '🎁', '🏆', '🥇', '🥈', '🥉', '⚽', '🏀', '🏈', '⚾', '🎾', '🏐', '🎮', '🕹️', '🎲', '🎯', '📱', '💻', '🖥️', '⌚', '📷', '📸', '📹', '🎥', '💡', '🔦', '🔑', '🗝️', '🔒', '🔓', '💰', '💵', '💳', '💎', '📦', '🛒', '🚗', '✈️', '⛵'],
        animals: ['🐶', '🐱', '🐭', '🐹', '🐰', '🦊', '🐻', '🐼', '🐨', '🐯', '🦁', '🐮', '🐷', '🐸', '🐵', '🐔', '🐧', '🐦', '🐤', '🦆', '🦅', '🦉', '🦇', '🐺', '🐗', '🐴', '🦄', '🐝', '🐛', '🦋', '🐌', '🐞', '🐜', '🦟', '🐢', '🐍', '🦎', '🐙', '🦑', '🦐', '🦞', '🦀', '🐡', '🐠', '🐟', '🐬', '🐳', '🐋', '🦈', '🐊'],
        food: ['🍏', '🍎', '🍐', '🍊', '🍋', '🍌', '🍉', '🍇', '🍓', '🫐', '🍈', '🍒', '🍑', '🥭', '🍍', '🥥', '🥝', '🍅', '🥑', '🥦', '🥬', '🥒', '🌽', '🥕', '🧄', '🧅', '🥔', '🍠', '🥐', '🥯', '🍞', '🥖', '🥨', '🧀', '🥚', '🍳', '🧈', '🥞', '🧇', '🥓', '🥩', '🍗', '🍖', '🌭', '🍔', '🍟', '🍕', '🥪', '🥙', '🌮', '🌯', '🥗', '🍿', '🍣', '🍦', '🍧', '🍨', '🍩', '🍪', '🎂', '🍰', '🧁', '🍫', '🍬', '🍭', '☕', '🍵', '🧃', '🥤', '🍺', '🍻']
    };

    function getAudioContext() {
        if (!audioCtx) {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        }
        if (audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
        return audioCtx;
    }

    function playNotificationChime() {
        if (!soundEnabled) return;
        try {
            const ctx = getAudioContext();
            const now = ctx.currentTime;
            
            const osc1 = ctx.createOscillator();
            const gain1 = ctx.createGain();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(587.33, now);
            osc1.frequency.exponentialRampToValueAtTime(880, now + 0.12);
            gain1.gain.setValueAtTime(0.2, now);
            gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.start(now);
            osc1.stop(now + 0.35);
        } catch (e) {
            console.log('Audio chime error:', e);
        }
    }

    function toggleSound() {
        soundEnabled = !soundEnabled;
        localStorage.setItem('chat_sound_enabled', soundEnabled);
        applySoundUi();
        if (soundEnabled) {
            playNotificationChime();
        }
    }

    function applySoundUi() {
        const icon = document.getElementById('soundIcon');
        const mobileIcon = document.getElementById('mobileSoundIcon');
        const mobileText = document.getElementById('mobileSoundText');
        if (soundEnabled) {
            if (icon) icon.className = 'fas fa-volume-up';
            if (mobileIcon) mobileIcon.className = 'fas fa-volume-up me-2 text-info';
            if (mobileText) mobileText.textContent = 'Sound: Enabled';
        } else {
            if (icon) icon.className = 'fas fa-volume-mute';
            if (mobileIcon) mobileIcon.className = 'fas fa-volume-mute me-2 text-muted';
            if (mobileText) mobileText.textContent = 'Sound: Muted';
        }
    }

    // WHITE / BLACK THEME TOGGLE
    function toggleChatTheme() {
        themeMode = (themeMode === 'black') ? 'white' : 'black';
        localStorage.setItem('chat_theme_mode', themeMode);
        applyThemeUi();
    }

    function applyThemeUi() {
        const card = document.getElementById('chatMainCard');
        const icon = document.getElementById('themeIcon');
        const text = document.getElementById('themeBtnText');
        if (!card) return;

        if (themeMode === 'white') {
            card.classList.add('chat-theme-white');
            if (icon) icon.className = 'fas fa-moon me-1 text-dark';
            if (text) text.textContent = 'Black BG';
        } else {
            card.classList.remove('chat-theme-white');
            if (icon) icon.className = 'fas fa-sun me-1 text-warning';
            if (text) text.textContent = 'White BG';
        }
    }

    // PRIVACY BLUR MODE
    function togglePrivacyBlur() {
        blurEnabled = !blurEnabled;
        localStorage.setItem('chat_blur_enabled', blurEnabled);
        applyBlurUi();
    }

    function applyBlurUi() {
        const card = document.getElementById('chatMainCard');
        const icon = document.getElementById('blurIcon');
        const badge = document.getElementById('blurBadge');
        const btnText = document.getElementById('blurBtnText');

        if (blurEnabled) {
            card.classList.add('privacy-blur-active');
            icon.className = 'fas fa-eye-slash text-warning';
            badge.classList.remove('d-none');
            if (btnText) btnText.textContent = 'Unblur';
        } else {
            card.classList.remove('privacy-blur-active');
            icon.className = 'fas fa-eye';
            badge.classList.add('d-none');
            if (btnText) btnText.textContent = 'Blur Text';
        }
    }

    function updateActivePersona(persona) {
        activePersona = persona;
        localStorage.setItem('chat_active_persona', persona);
        
        // Re-render conversation stream so sent messages are on the RIGHT and received on the LEFT
        document.getElementById('messagesStream').innerHTML = '';
        lastMessageId = 0;
        fetchMessages(true);
    }

    function autoResizeTextarea(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
    }

    // FULL EMOJI PICKER
    function toggleEmojiPicker() {
        const drawer = document.getElementById('emojiPickerPopup');
        if (drawer.classList.contains('d-none')) {
            drawer.classList.remove('d-none');
            renderEmojiCategory('all');
            document.getElementById('emojiSearchInput').focus();
        } else {
            drawer.classList.add('d-none');
        }
    }

    function switchEmojiCategory(cat, e) {
        document.querySelectorAll('.btn-emoji-tab').forEach(btn => btn.classList.remove('active'));
        if (e && e.target) e.target.classList.add('active');
        renderEmojiCategory(cat);
    }

    function renderEmojiCategory(cat) {
        const grid = document.getElementById('emojiGrid');
        grid.innerHTML = '';
        let list = [];

        if (cat === 'all') {
            Object.values(emojiDatabase).forEach(arr => list.push(...arr));
        } else if (emojiDatabase[cat]) {
            list = emojiDatabase[cat];
        }

        list.forEach(emoji => {
            const item = document.createElement('div');
            item.className = 'emoji-grid-item';
            item.textContent = emoji;
            item.onclick = function() {
                appendEmoji(emoji);
            };
            grid.appendChild(item);
        });
    }

    function filterEmojis() {
        const q = document.getElementById('emojiSearchInput').value.trim();
        const grid = document.getElementById('emojiGrid');
        if (!q) {
            renderEmojiCategory('all');
            return;
        }
        grid.innerHTML = '';
        let all = [];
        Object.values(emojiDatabase).forEach(arr => all.push(...arr));
        all.forEach(emoji => {
            const item = document.createElement('div');
            item.className = 'emoji-grid-item';
            item.textContent = emoji;
            item.onclick = function() {
                appendEmoji(emoji);
            };
            grid.appendChild(item);
        });
    }

    function appendEmoji(emoji) {
        const input = document.getElementById('messageInput');
        input.value += emoji;
        input.focus();
        autoResizeTextarea(input);
    }

    function handleInputKey(event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            sendMessage(activePersona);
        }
    }

    // UPDATE PARTICIPANT NAMES
    function saveParticipantNames() {
        const n1 = document.getElementById('inputName1').value.trim();
        const n2 = document.getElementById('inputName2').value.trim();
        const alertBox = document.getElementById('namesAlert');

        if (!n1 || !n2) {
            alertBox.textContent = 'Both participant names are required.';
            alertBox.classList.remove('d-none');
            return;
        }

        fetch('{{ route("chat.updateNames") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name1: n1, name2: n2 })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                participantNames.name1 = data.name1;
                participantNames.name2 = data.name2;
                activePersona = data.name1;

                // Update UI Labels
                document.getElementById('radioLabel1').textContent = data.name1;
                document.getElementById('persona1').value = data.name1;
                document.getElementById('radioLabel2').textContent = data.name2;
                document.getElementById('persona2').value = data.name2;

                // Close modal
                const modalEl = document.getElementById('editNamesModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();

                // Re-render messages
                document.getElementById('messagesStream').innerHTML = '';
                lastMessageId = 0;
                fetchMessages(true);
            }
        });
    }

    // INITIAL LOAD
    document.addEventListener('DOMContentLoaded', function() {
        applySoundUi();
        applyThemeUi();
        applyBlurUi();

        // Sync active persona radio button
        if (activePersona === participantNames.name2) {
            const r2 = document.getElementById('persona2');
            if (r2) r2.checked = true;
        } else {
            const r1 = document.getElementById('persona1');
            if (r1) r1.checked = true;
        }

        fetchMessages(true);

        function startPolling() {
            if (pollInterval) clearInterval(pollInterval);
            pollInterval = setInterval(() => fetchMessages(false), 1500);
        }

        function stopPolling() {
            if (pollInterval) {
                clearInterval(pollInterval);
                pollInterval = null;
            }
        }

        startPolling();

        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                stopPolling();
            } else {
                fetchMessages(false);
                startPolling();
            }
        });

        const container = document.getElementById('chatContainer');
        container.addEventListener('scroll', function() {
            const btnScroll = document.getElementById('btnScrollBottom');
            const isNearBottom = container.scrollHeight - container.scrollTop - container.clientHeight < 120;
            if (isNearBottom) {
                btnScroll.classList.add('d-none');
            } else {
                btnScroll.classList.remove('d-none');
            }
        });

        // Autofocus message input
        const msgInput = document.getElementById('messageInput');
        if (msgInput) {
            msgInput.focus();
        }

        // Close emoji picker when clicking outside
        document.addEventListener('click', function(e) {
            const drawer = document.getElementById('emojiPickerPopup');
            if (drawer && !drawer.classList.contains('d-none')) {
                if (!drawer.contains(e.target) && !e.target.closest('button[onclick="toggleEmojiPicker()"]')) {
                    drawer.classList.add('d-none');
                }
            }
        });
    });

    // FETCH MESSAGES
    function fetchMessages(isInitial = false) {
        if (isPolling) return;
        isPolling = true;

        const url = '{{ route("chat.messages") }}' + (lastMessageId > 0 ? '?after_id=' + lastMessageId : '');

        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => {
            if (res.status === 403) {
                window.location.reload();
                return null;
            }
            return res.json();
        })
        .then(data => {
            isPolling = false;
            if (!data || !data.messages) return;

            document.getElementById('loadingChat').classList.add('d-none');

            if (isInitial && data.messages.length === 0) {
                document.getElementById('emptyChat').classList.remove('d-none');
                return;
            }

            if (data.messages.length > 0) {
                document.getElementById('emptyChat').classList.add('d-none');
                let hasNewFromOther = false;

                data.messages.forEach(msg => {
                    if (msg.id > lastMessageId) {
                        lastMessageId = msg.id;
                        renderMessage(msg);
                        if (!isInitial && msg.sender_name !== activePersona) {
                            hasNewFromOther = true;
                        }
                    }
                });

                if (hasNewFromOther) {
                    playNotificationChime();
                }

                if (isInitial || isNearBottom()) {
                    scrollToBottom(false);
                }
            }
        })
        .catch(err => {
            isPolling = false;
            console.error('Polling error:', err);
        });
    }

    // RENDER SINGLE MESSAGE (STANDARD CHAT: SENT -> RIGHT, RECEIVED -> LEFT)
    function renderMessage(msg) {
        const stream = document.getElementById('messagesStream');
        
        // Standard chat logic: If message is sent by current active sender -> RIGHT (Outgoing), else -> LEFT (Incoming)
        const msgSender = (msg.sender_name || '').trim().toLowerCase();
        const currentSender = (activePersona || '').trim().toLowerCase();
        
        const isSentByMe = (msgSender === currentSender);

        const rowClass = isSentByMe ? 'sender-1-row' : 'sender-2-row';
        const avatarClass = isSentByMe ? 'avatar-sender-1' : 'avatar-sender-2';
        const initial = (msg.sender_name || 'U').charAt(0).toUpperCase();

        // Check date divider
        const lastDivider = stream.querySelector('.date-divider:last-of-type');
        const currentDateLabel = msg.date_label || 'Today';
        if (!lastDivider || lastDivider.dataset.date !== currentDateLabel) {
            const div = document.createElement('div');
            div.className = 'date-divider';
            div.dataset.date = currentDateLabel;
            div.innerHTML = `<span>${escapeHtml(currentDateLabel)}</span>`;
            stream.appendChild(div);
        }

        const msgDiv = document.createElement('div');
        msgDiv.className = `message-row ${rowClass}`;
        msgDiv.id = `msg-${msg.id}`;

        const formattedText = escapeHtml(msg.message).replace(/\n/g, '<br>');

        msgDiv.innerHTML = `
            <div class="message-wrapper">
                <div class="bubble-avatar ${avatarClass}" title="${escapeHtml(msg.sender_name)}">
                    ${initial}
                </div>
                <div class="message-bubble">
                    <div class="fw-bold mb-1 bubble-sender-name" style="font-size: 0.72rem;">
                        ${escapeHtml(msg.sender_name)}
                    </div>
                    <div class="message-text" style="font-size: 0.93rem; line-height: 1.45; word-break: break-word;">
                        ${formattedText}
                    </div>
                    <div class="bubble-meta d-flex align-items-center gap-1 mt-1" style="font-size: 0.68rem;">
                        <span>${escapeHtml(msg.formatted_time || '')}</span>
                        <i class="fas fa-check-double ms-1" style="font-size: 0.65rem;"></i>
                    </div>
                </div>
            </div>
        `;

        stream.appendChild(msgDiv);
    }

    // SEND MESSAGE
    function sendMessage(senderName) {
        const input = document.getElementById('messageInput');
        const text = input.value.trim();

        if (!text) {
            input.focus();
            return;
        }

        input.value = '';
        autoResizeTextarea(input);
        input.focus();

        fetch('{{ route("chat.send") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                message: text,
                sender_name: senderName
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success' && data.message) {
                if (data.message.id > lastMessageId) {
                    lastMessageId = data.message.id;
                    document.getElementById('emptyChat').classList.add('d-none');
                    renderMessage(data.message);
                    scrollToBottom(true);
                }
            }
        })
        .catch(err => {
            console.error('Send message error:', err);
            input.value = text;
        });
    }

    function isNearBottom() {
        const container = document.getElementById('chatContainer');
        return container.scrollHeight - container.scrollTop - container.clientHeight < 150;
    }

    function scrollToBottom(smooth = true) {
        const container = document.getElementById('chatContainer');
        container.scrollTo({
            top: container.scrollHeight,
            behavior: smooth ? 'smooth' : 'auto'
        });
    }

    function confirmClearChat() {
        if (!confirm('Are you sure you want to clear the entire communication log?')) {
            return;
        }

        fetch('{{ route("chat.clear") }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById('messagesStream').innerHTML = '';
                document.getElementById('emptyChat').classList.remove('d-none');
                lastMessageId = 0;
            }
        });
    }

    function escapeHtml(string) {
        if (!string) return '';
        return String(string)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
@endif
</script>
@endsection
