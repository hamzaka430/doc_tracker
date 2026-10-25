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

@if($state === 'setup_pin')
<!-- ========================================== -->
<!-- PIN SECURITY INITIAL SETUP SCREEN          -->
<!-- ========================================== -->
<div id="pinSecurityScreen" class="row justify-content-center py-4 mx-0">
    <div class="col-md-6 col-lg-5 col-xl-4">
        <div class="card shadow-sm border text-center" style="border-radius: 16px;">
            <div class="card-header telegram-header text-white py-3" style="border-radius: 16px 16px 0 0;">
                <div class="card-title text-white mb-0 fs-5">
                    <i class="fas fa-key me-2"></i> Security PIN Setup
                </div>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-4">
                    Create a 4-digit security PIN to initialize this support session.
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
                <form id="pinForm" method="POST" action="{{ route('chat.setupPin') }}">
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
                        <i class="fas fa-unlock-alt me-2"></i> Set PIN & Continue
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<!-- ========================================== -->
<!-- PIN SECURITY VERIFICATION SCREEN           -->
<!-- ========================================== -->
<div id="pinSecurityScreen" class="row justify-content-center py-4 mx-0 {{ $isUnlocked ? 'd-none' : '' }}">
    <div class="col-md-6 col-lg-5 col-xl-4">
        <div class="card shadow-sm border text-center" style="border-radius: 16px;">
            <div class="card-header telegram-header text-white py-3" style="border-radius: 16px 16px 0 0;">
                <div class="card-title text-white mb-0 fs-5">
                    <i class="fas fa-lock me-2"></i> Security Verification
                </div>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-4">
                    Enter your 4-digit security PIN code to access the support log.
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
                <form id="pinForm" method="POST" action="{{ route('chat.verifyPin') }}">
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
                        <i class="fas fa-unlock-alt me-2"></i> Verify & Unlock
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- SUPPORT LOG / INTERNAL MESSENGER ROOM      -->
<!-- ========================================== -->
<div id="chatPageWrapper" class="chat-page-wrapper" style="{{ $isUnlocked ? '' : 'display: none;' }}">
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
                        <div class="text-white-50 d-flex align-items-center gap-1.5" style="font-size: 0.7rem;">
                            <span class="online-pulse-wrap" id="presencePulse" aria-hidden="true">
                                <span class="online-pulse-ring"></span>
                                <span class="online-pulse-dot" id="presenceDot"></span>
                            </span>
                            <span class="text-emerald-400 fw-semibold" id="presenceStatusText">Active Session</span>
                            <span id="blurBadge" class="badge bg-warning text-dark px-1.5 py-0.5 ms-1 d-none" style="font-size: 0.6rem;">
                                <i class="fas fa-eye-slash me-0.5"></i> Blur
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Right Toolbar Buttons -->
                <div class="d-flex align-items-center gap-1.5 flex-nowrap ms-auto flex-shrink-0">
                    <!-- Search Conversation Toggle Button -->
                    <button type="button" class="btn btn-telegram-header" id="btnToggleSearch" onclick="toggleSearch()" title="Search conversation" aria-label="Search conversation">
                        <i class="fas fa-search" id="searchHeaderIcon"></i>
                    </button>

                    <!-- White / Black Background Theme Toggle (Visible on desktop/tablet >= 768px) -->
                    <button type="button" class="btn btn-telegram-header d-none d-md-inline-flex" id="btnThemeToggle" onclick="toggleChatTheme()" title="Switch Theme (Dark / Light)" aria-label="Toggle theme">
                        <i class="fas fa-sun" id="themeIcon"></i> <span class="d-none d-md-inline ms-1" id="themeBtnText">White BG</span>
                    </button>

                    <!-- Privacy Blur Toggle Button (Always visible) -->
                    <button type="button" class="btn btn-telegram-header" id="btnBlurToggle" onclick="togglePrivacyBlur()" title="Privacy Blur (Obscure entire chat)" aria-label="Toggle privacy blur">
                        <i class="fas fa-eye-slash" id="blurIcon"></i> <span class="d-none d-md-inline ms-1" id="blurBtnText">Blur</span>
                    </button>

                    <!-- Desktop Extra Buttons (Visible on tablet & desktop >= 768px) -->
                    <div class="d-none d-md-flex align-items-center gap-1.5">
                        <!-- Chat Settings & Customization -->
                        <button type="button" class="btn btn-telegram-header" data-bs-toggle="modal" data-bs-target="#chatSettingsModal" title="Settings, Themes & Nicknames" aria-label="Chat Settings">
                            <i class="fas fa-sliders-h"></i> <span class="d-none d-lg-inline ms-1">Settings</span>
                        </button>

                        <!-- Notification Chime Toggle -->
                        <button type="button" class="btn btn-telegram-header" id="btnSoundToggle" onclick="toggleSound()" title="Toggle Notification Sound" aria-label="Toggle notification sound">
                            <i class="fas fa-volume-up" id="soundIcon"></i>
                        </button>

                        <!-- Clear Log -->
                        <button type="button" class="btn btn-telegram-header" onclick="confirmClearChat()" title="Clear Conversation Log" aria-label="Clear chat log">
                            <i class="fas fa-trash-alt"></i>
                        </button>

                        <!-- Lock Session -->
                        <form method="POST" action="{{ route('chat.lock') }}" class="d-inline m-0">
                            @csrf
                            <button type="submit" class="btn btn-telegram-header text-danger" title="Lock Session (Require PIN)" aria-label="Lock session">
                                <i class="fas fa-lock"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Mobile 3-Dots Dropdown Menu (Visible on mobile < 768px) -->
                    <div class="dropdown d-md-none">
                        <button type="button" class="btn btn-telegram-header" data-bs-toggle="dropdown" aria-expanded="false" title="More options" aria-label="More options">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border telegram-dropdown-menu">
                            <li>
                                <a class="dropdown-item py-2" href="javascript:void(0)" onclick="toggleChatTheme()">
                                    <i class="fas fa-adjust me-2 text-warning" id="mobileThemeIcon"></i> <span id="mobileThemeText">Theme (Dark / Light)</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="javascript:void(0)" onclick="togglePrivacyBlur()">
                                    <i class="fas fa-eye-slash me-2 text-info" id="mobileBlurIcon"></i> <span id="mobileBlurText">Privacy Blur Mode</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#chatSettingsModal">
                                    <i class="fas fa-sliders-h me-2 text-primary"></i> Chat Settings & Themes
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

        <!-- SEARCH IN CONVERSATION (SLIDE-DOWN BAR) -->
        <div id="chatSearchBar" class="chat-search-bar d-none">
            <div class="d-flex align-items-center gap-2 px-3 py-1.5 w-100">
                <i class="fas fa-search text-white-50 small"></i>
                <input type="text" id="searchInput" class="form-control form-control-sm border-0 bg-transparent text-white" placeholder="Search in conversation..." oninput="handleSearch(this.value)" autocomplete="off">
                <span id="searchResultsBadge" class="badge bg-secondary text-white small d-none">0 matches</span>
                <button type="button" class="btn btn-sm text-white-50 p-0" onclick="closeSearch()" title="Close search" aria-label="Close search">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- PINNED MESSAGE STICKY BANNER -->
        <div id="pinnedMessageBanner" class="pinned-banner d-none" onclick="scrollToPinnedMessage()">
            <div class="d-flex align-items-center justify-content-between w-100 px-3 py-1.5">
                <div class="d-flex align-items-center gap-2 text-truncate">
                    <i class="fas fa-thumbtack text-amber-400 small flex-shrink-0"></i>
                    <div class="text-truncate" style="font-size: 0.78rem;">
                        <span class="text-white fw-bold" id="pinnedSender">Pinned</span>: 
                        <span class="text-white-50" id="pinnedText">...</span>
                    </div>
                </div>
                <button type="button" class="btn btn-sm text-white-50 p-0 ms-2 flex-shrink-0" onclick="event.stopPropagation(); unpinCurrentMessage()" title="Unpin message">
                    <i class="fas fa-times small"></i>
                </button>
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
            <div id="emptyChat" class="text-center py-5 d-none my-auto d-flex flex-column align-items-center justify-content-center">
                <div class="empty-chat-icon-wrap mb-3">
                    <div class="empty-chat-circle">
                        <i class="fas fa-comments text-primary"></i>
                    </div>
                </div>
                <h6 class="fw-bold text-white mb-1" style="font-size: 1.05rem;">No messages yet</h6>
                <p class="text-muted small mb-0" style="max-width: 260px; font-size: 0.82rem; line-height: 1.45;">
                    Start this internal support session by typing your first message below.
                </p>
            </div>

            <!-- Messages Stream -->
            <div id="messagesStream"></div>
        </div>

        <!-- SCROLL DOWN FLOATING PILL -->
        <button type="button" class="btn btn-dark btn-round shadow-sm scroll-down-btn d-none" id="btnScrollBottom" onclick="scrollToBottom(true)">
            <i class="fas fa-arrow-down me-1"></i> Latest Entries
        </button>

        <!-- EMOJI PICKER DRAWER -->
        <div id="emojiPickerPopup" class="emoji-picker-drawer shadow-lg border d-none">
            <!-- Header: Clean Title & Close -->
            <div class="d-flex align-items-center justify-content-between p-2 border-bottom bg-dark drawer-main-nav">
                <span class="text-white fw-bold small ps-1">
                    <i class="far fa-smile text-primary me-1.5"></i> Emojis
                </span>
                <button type="button" class="btn btn-sm text-white-50 p-0 text-decoration-none" onclick="toggleEmojiPicker()" title="Close drawer" aria-label="Close drawer">
                    <i class="fas fa-times fs-6"></i>
                </button>
            </div>

            <!-- Emojis View -->
            <div id="drawerMediaEmojis">
                <div class="p-2 border-bottom bg-dark">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-transparent border-end-0 text-white-50"><i class="fa fa-search"></i></span>
                        <input type="text" id="emojiSearchInput" class="form-control border-start-0 bg-transparent text-white" placeholder="Search emojis..." onkeyup="filterEmojis()">
                    </div>
                </div>
                <div class="d-flex gap-1 p-2 border-bottom bg-dark overflow-x-auto emoji-category-tabs">
                    <button type="button" class="btn btn-sm btn-emoji-tab active" onclick="switchEmojiCategory('all', event)">All</button>
                    <button type="button" class="btn btn-sm btn-emoji-tab" onclick="switchEmojiCategory('smileys', event)">😀 Faces</button>
                    <button type="button" class="btn btn-sm btn-emoji-tab" onclick="switchEmojiCategory('gestures', event)">👍 Hands</button>
                    <button type="button" class="btn btn-sm btn-emoji-tab" onclick="switchEmojiCategory('hearts', event)">❤️ Hearts</button>
                    <button type="button" class="btn btn-sm btn-emoji-tab" onclick="switchEmojiCategory('objects', event)">🚀 Objects</button>
                    <button type="button" class="btn btn-sm btn-emoji-tab" onclick="switchEmojiCategory('animals', event)">🐶 Animals</button>
                    <button type="button" class="btn btn-sm btn-emoji-tab" onclick="switchEmojiCategory('food', event)">🍔 Food</button>
                </div>
                <div class="emoji-grid p-2" id="emojiGrid"></div>
            </div>
        </div>

        <!-- CARD FOOTER (TELEGRAM INPUT BAR) -->
        <div class="card-footer py-2 px-2 px-sm-3 border-top flex-shrink-0 position-relative telegram-footer">
            
            <!-- Top Row: Active Sender Segmented Switcher -->
            <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2 participant-switcher-bar">
                <div class="d-flex align-items-center gap-2 active-sender-wrap">
                    <span class="switcher-label d-flex align-items-center gap-1.5 text-slate-400 fw-semibold" style="font-size: 0.78rem;">
                        <i class="fas fa-paper-plane text-primary me-0.5" style="font-size: 0.72rem;"></i>
                        <span class="d-none d-sm-inline">Sending as:</span>
                    </span>
                    <div class="telegram-persona-capsule" role="group" aria-label="Sending persona switcher">
                        <input type="radio" class="btn-check" name="activePersona" id="persona1" value="{{ $name1 }}" checked onchange="updateActivePersona(this.value)">
                        <label class="persona-tab sender-label-blur text-truncate" for="persona1" id="radioLabel1">
                            <span class="persona-dot"></span>
                            <span>{{ $name1 }}</span>
                        </label>

                        <input type="radio" class="btn-check" name="activePersona" id="persona2" value="{{ $name2 }}" onchange="updateActivePersona(this.value)">
                        <label class="persona-tab sender-label-blur text-truncate" for="persona2" id="radioLabel2">
                            <span class="persona-dot"></span>
                            <span>{{ $name2 }}</span>
                        </label>
                    </div>
                </div>
                <small class="text-muted d-none d-sm-inline" style="font-size: 0.72rem;">Press <strong>Enter ↵</strong> to send</small>
            </div>

            <!-- Reply Quote Banner -->
            <div id="replyBar" class="reply-banner d-none mb-1.5">
                <div class="d-flex align-items-center justify-content-between w-100 px-3 py-1.5 rounded-pill bg-slate-900 border border-blue-500/40">
                    <div class="text-truncate d-flex align-items-center gap-1.5" style="font-size: 0.76rem;">
                        <i class="fas fa-reply text-blue-400"></i>
                        <span class="text-blue-400 fw-bold" id="replySenderName">Replying to</span>:
                        <span class="text-slate-300 text-truncate" id="replyPreviewText">...</span>
                    </div>
                    <button type="button" class="btn text-white-50 p-0 ms-2" onclick="cancelReply()" title="Cancel reply" aria-label="Cancel reply">
                        <i class="fas fa-times small"></i>
                    </button>
                </div>
            </div>

            <!-- Editing Message Banner -->
            <div id="editBar" class="edit-banner d-none mb-1.5">
                <div class="d-flex align-items-center justify-content-between w-100 px-3 py-1.5 rounded-pill bg-slate-900 border border-amber-500/40">
                    <div class="text-truncate d-flex align-items-center gap-1.5" style="font-size: 0.76rem;">
                        <i class="fas fa-pencil-alt text-amber-400"></i>
                        <span class="text-amber-400 fw-bold">Editing message</span>
                        <span class="text-white-50 small">(Press Esc or X to cancel)</span>
                    </div>
                    <button type="button" class="btn text-white-50 p-0 ms-2" onclick="cancelEdit()" title="Cancel edit" aria-label="Cancel edit">
                        <i class="fas fa-times small"></i>
                    </button>
                </div>
            </div>

            <!-- Typing Indicator (Support) -->
            <div id="typingIndicator" class="typing-indicator d-none mb-1">
                <span class="typing-bubble">
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                </span>
                <span class="text-muted small ms-1" style="font-size: 0.72rem;">typing...</span>
            </div>

            <!-- Input Row: [Emoji Icon] [Telegram Input Box] [Circular Send Button] -->
            <div class="chat-input-bar-wrap">
                <div class="chat-input-inner">
                    <!-- Emoji Button -->
                    <button type="button" class="btn btn-emoji-trigger" onclick="toggleEmojiPicker()" title="Browse emojis" aria-label="Browse emojis">
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
                            aria-label="Write a message"
                            onkeydown="handleInputKey(event)"
                            oninput="handleMessageInput(this)"></textarea>
                    </div>

                    <!-- Single Send Action Button (Telegram Round Plane Button) -->
                    <button type="button" class="btn btn-send-main btn-send-disabled" onclick="sendMessage(activePersona)" id="btnMainSend" title="Send message" aria-label="Send message" disabled>
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- ========================================== -->
<!-- CHAT SETTINGS & CUSTOMIZATION MODAL        -->
<!-- ========================================== -->
<div class="modal fade" id="chatSettingsModal" tabindex="-1" aria-labelledby="chatSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg text-white" style="border-radius: 18px; background: #16181f; border: 1px solid #2a2e39 !important;">
            <div class="modal-header py-3 px-4" style="border-bottom: 1px solid #2a2e39;">
                <h5 class="modal-title fw-bold text-white d-flex align-items-center gap-2" id="chatSettingsModalLabel" style="font-size: 1.05rem;">
                    <i class="fas fa-sliders-h text-primary"></i> Chat Settings & Themes
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                
                <!-- Section 1: Themes Selection -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-white small text-uppercase tracking-wider mb-2">
                        <i class="fas fa-palette me-1.5 text-info"></i> Color Theme
                    </label>
                    <div class="row g-2" id="themePickerGrid">
                        <div class="col-6">
                            <button type="button" class="btn w-100 theme-card active" onclick="selectChatTheme('midnight', this)">
                                <span class="theme-swatch" style="background: linear-gradient(135deg, #16181d, #2563eb);"></span>
                                <span class="theme-name">Midnight Dark</span>
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn w-100 theme-card" onclick="selectChatTheme('ocean', this)">
                                <span class="theme-swatch" style="background: linear-gradient(135deg, #080e1a, #0284c7);"></span>
                                <span class="theme-name">Ocean Blue</span>
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn w-100 theme-card" onclick="selectChatTheme('emerald', this)">
                                <span class="theme-swatch" style="background: linear-gradient(135deg, #022c22, #059669);"></span>
                                <span class="theme-name">Emerald Forest</span>
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn w-100 theme-card" onclick="selectChatTheme('amethyst', this)">
                                <span class="theme-swatch" style="background: linear-gradient(135deg, #18082e, #7c3aed);"></span>
                                <span class="theme-name">Amethyst Violet</span>
                            </button>
                        </div>
                        <div class="col-12">
                            <button type="button" class="btn w-100 theme-card" onclick="selectChatTheme('white', this)">
                                <span class="theme-swatch" style="background: linear-gradient(135deg, #f8fafc, #e2e8f0); border: 1px solid #cbd5e1;"></span>
                                <span class="theme-name">Minimalist Light</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Privacy Preferences -->
                <div class="mb-4 pt-3 border-top" style="border-color: #2a2e39 !important;">
                    <label class="form-label fw-bold text-white small text-uppercase tracking-wider mb-2">
                        <i class="fas fa-shield-alt me-1.5 text-warning"></i> Privacy & Presence
                    </label>
                    <div class="d-flex align-items-center justify-content-between p-2.5 rounded mb-2 bg-dark border border-secondary border-opacity-25">
                        <div>
                            <div class="fw-semibold text-white small">Active Status</div>
                            <div class="text-white-50" style="font-size: 0.72rem;">Show when you are active / online to other participant</div>
                        </div>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" id="toggleActiveStatus" checked onchange="toggleActiveStatusSetting(this.checked)">
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between p-2.5 rounded bg-dark border border-secondary border-opacity-25">
                        <div>
                            <div class="fw-semibold text-white small">Read Receipts</div>
                            <div class="text-white-50" style="font-size: 0.72rem;">Display double checkmarks when messages are seen</div>
                        </div>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" id="toggleReadReceipts" checked onchange="toggleReadReceiptsSetting(this.checked)">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Participant Nicknames -->
                <div class="pt-3 border-top" style="border-color: #2a2e39 !important;">
                    <label class="form-label fw-bold text-white small text-uppercase tracking-wider mb-2">
                        <i class="fas fa-user-edit me-1.5 text-primary"></i> Participant Nicknames
                    </label>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label for="inputName1" class="form-label text-white-50 small mb-1">Participant 1</label>
                            <input type="text" class="form-control form-control-sm bg-dark text-white border-secondary border-opacity-50" id="inputName1" value="{{ $name1 }}" placeholder="e.g. User 1" maxlength="50" required>
                        </div>
                        <div class="col-6">
                            <label for="inputName2" class="form-label text-white-50 small mb-1">Participant 2</label>
                            <input type="text" class="form-control form-control-sm bg-dark text-white border-secondary border-opacity-50" id="inputName2" value="{{ $name2 }}" placeholder="e.g. User 2" maxlength="50" required>
                        </div>
                    </div>
                    <div id="namesAlert" class="alert alert-danger py-2 px-3 small d-none mb-0"></div>
                </div>

            </div>
            <div class="modal-footer py-2 px-4" style="border-top: 1px solid #2a2e39;">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-sm rounded-pill fw-bold px-4" onclick="saveParticipantNames()"><i class="fas fa-save me-1"></i> Save Names</button>
            </div>
        </div>
    </div>
</div>
<!-- Backward compatibility alias for modal ID -->
<div id="editNamesModal" class="d-none"></div>
</div>
@endif

<!-- STYLES -->
<style>
/* ========================================================= */
/* GLOBAL RESET: PREVENT ANY HORIZONTAL SCROLLBAR            */
/* ========================================================= */
html, body {
    overflow-x: hidden !important;
    max-width: 100vw;
}
.wrapper, .main-panel, .main-panel > .container {
    overflow-x: hidden !important;
    max-width: 100%;
}
.chat-page-wrapper {
    overflow-x: hidden !important;
    width: 100% !important;
    max-width: 100% !important;
}

/* Full Viewport Fit for Active Chat Session: No outer page scroll */
body.chat-unlocked-mode, html.chat-unlocked-mode {
    height: 100%;
    height: 100dvh;
    overflow: hidden !important;
}
body.chat-unlocked-mode .main-panel {
    height: 100vh !important;
    height: 100dvh !important;
    max-height: 100vh !important;
    max-height: 100dvh !important;
    overflow: hidden !important;
    display: flex !important;
    flex-direction: column !important;
}
body.chat-unlocked-mode .main-panel > .container {
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
    body.chat-unlocked-mode .main-panel > .container {
        margin-top: 56px !important;
        height: calc(100dvh - 56px) !important;
        max-height: calc(100dvh - 56px) !important;
    }
}
body.chat-unlocked-mode .main-panel .page-inner {
    padding: 8px 12px 8px 12px !important;
    height: 100% !important;
    max-height: 100% !important;
    min-height: 0 !important;
    overflow: hidden !important;
    display: flex !important;
    flex-direction: column !important;
    flex: 1 1 0 !important;
}
@media (max-width: 767.98px) {
    body.chat-unlocked-mode .main-panel .page-inner {
        padding: 0 !important;
    }
}

/* PIN Security Verification Screen: Allow scrolling so keypad & unlock button are accessible */
body:not(.chat-unlocked-mode) .main-panel {
    min-height: 100vh;
    min-height: 100dvh;
    overflow-y: auto !important;
    overflow-x: hidden !important;
}
body:not(.chat-unlocked-mode) .main-panel > .container {
    margin-top: 65px !important;
    min-height: calc(100vh - 65px) !important;
    padding: 0 !important;
    overflow-x: hidden !important;
}
@media (max-width: 991.98px) {
    body:not(.chat-unlocked-mode) .main-panel > .container {
        margin-top: 56px !important;
        min-height: calc(100dvh - 56px) !important;
    }
}
body:not(.chat-unlocked-mode) .main-panel .page-inner {
    padding: 16px 12px !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
}

.main-panel .footer,
.page-header,
.main-panel .page-header {
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
@media (max-width: 767.98px) {
    .chat-main-card {
        border-radius: 0 !important;
        border: none !important;
        box-shadow: none !important;
    }
}

.telegram-header {
    background: #1a2035 !important;
    border-bottom: 1px solid #2a3454 !important;
    min-height: 52px !important;
    flex-shrink: 0 !important;
    backdrop-filter: blur(8px);
}
.telegram-header-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, #1572e8, #1c7ffc);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 0.95rem;
    box-shadow: 0 2px 6px rgba(21, 114, 232, 0.35);
}

/* Online Pulse Animation */
.online-pulse-wrap {
    position: relative;
    display: inline-flex;
    width: 8px;
    height: 8px;
    vertical-align: middle;
}
.online-pulse-ring {
    position: absolute;
    inset: 0;
    border-radius: 9999px;
    background-color: #34d399;
    opacity: 0.75;
    animation: pingAnim 1.6s cubic-bezier(0, 0, 0.2, 1) infinite;
}
.online-pulse-dot {
    position: relative;
    display: inline-flex;
    width: 8px;
    height: 8px;
    border-radius: 9999px;
    background-color: #10b981;
}
@keyframes pingAnim {
    75%, 100% {
        transform: scale(2.2);
        opacity: 0;
    }
}

/* Touch Target Header Buttons */
.btn-telegram-header {
    background: rgba(255, 255, 255, 0.08) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    color: #ffffff !important;
    font-size: 0.8rem !important;
    padding: 0 12px !important;
    height: 40px !important;
    min-width: 40px !important;
    border-radius: 10px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    transition: all 0.18s ease;
}
.btn-telegram-header:hover {
    background: #1572e8 !important;
    border-color: #1572e8 !important;
    color: #ffffff !important;
    transform: translateY(-1px);
}

/* ========================================================= */
/* KAIADMIN DASHBOARD MESSAGES STREAM                        */
/* ========================================================= */
.chat-messages-container {
    background: #121624 !important;
    overflow-y: auto !important;
    position: relative;
    flex: 1 1 auto !important;
    min-height: 0 !important;
}
.chat-messages-container::-webkit-scrollbar {
    width: 6px;
}
.chat-messages-container::-webkit-scrollbar-thumb {
    background: #2a3454;
    border-radius: 10px;
}

#messagesStream {
    display: flex;
    flex-direction: column;
    gap: 4px;
    width: 100%;
}

/* ========================================================= */
/* EMPTY STATE WITH GLOWING CHAT ICON                        */
/* ========================================================= */
.empty-chat-icon-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
}
.empty-chat-circle {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(21, 114, 232, 0.25) 0%, rgba(26, 32, 53, 0.85) 100%);
    border: 1px solid rgba(21, 114, 232, 0.35);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.7rem;
    color: #48abf7;
    box-shadow: 0 0 20px rgba(21, 114, 232, 0.25);
}

/* ========================================================= */
/* DAY-WISE DATE SEPARATORS & UNREAD MARKER                  */
/* ========================================================= */
.date-divider {
    text-align: center;
    position: sticky;
    top: 8px;
    z-index: 10;
    margin: 14px 0;
    pointer-events: none;
}
.date-divider span {
    background: rgba(32, 41, 64, 0.95) !important;
    color: #b9babf !important;
    border: 1px solid #2a3454 !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    letter-spacing: 0.3px;
    padding: 4px 14px !important;
    border-radius: 9999px !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.35) !important;
    backdrop-filter: blur(8px);
    display: inline-block;
}

.unread-messages-divider {
    text-align: center;
    margin: 14px 0;
    position: relative;
    z-index: 5;
    pointer-events: none;
}
.unread-messages-divider span {
    background: rgba(21, 114, 232, 0.16) !important;
    color: #48abf7 !important;
    border: 1px solid rgba(21, 114, 232, 0.45) !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    letter-spacing: 0.3px;
    padding: 4px 14px !important;
    border-radius: 9999px !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25) !important;
    display: inline-block;
}

/* ========================================================= */
/* ASYMMETRIC BUBBLES WITH CONSECUTIVE GROUPING              */
/* ========================================================= */
.message-row {
    display: flex;
    width: 100%;
    margin-bottom: 2px;
    animation: bubbleEnter 0.16s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
}
@keyframes bubbleEnter {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}

/* OUTGOING (SENT) - SENDER 1: VIBRANT GRADIENT BLUE */
.message-row.sender-1-row {
    justify-content: flex-end !important;
}
.message-row.sender-1-row .message-wrapper {
    display: flex;
    flex-direction: row-reverse;
    align-items: flex-end;
    gap: 8px;
    max-width: 80%;
}
.message-row.sender-1-row .message-bubble {
    background: linear-gradient(135deg, #1572e8, #1c7ffc) !important;
    color: #ffffff !important;
    padding: 9px 14px !important;
    box-shadow: 0 2px 8px rgba(21, 114, 232, 0.3) !important;
    border: none !important;
    position: relative;
}

/* Grouping radii: small corner on sender side */
.message-row.sender-1-row.group-single .message-bubble {
    border-radius: 18px 18px 4px 18px !important;
}
.message-row.sender-1-row.group-first .message-bubble {
    border-radius: 18px 18px 6px 18px !important;
    margin-bottom: 2px;
}
.message-row.sender-1-row.group-middle .message-bubble {
    border-radius: 18px 6px 6px 18px !important;
    margin-bottom: 2px;
}
.message-row.sender-1-row.group-last .message-bubble {
    border-radius: 18px 6px 4px 18px !important;
}

.message-row.sender-1-row .bubble-sender-name {
    color: #dbeafe !important;
    font-size: 0.72rem !important;
    font-weight: 700;
    margin-bottom: 2px;
}
.message-row.sender-1-row .bubble-meta {
    justify-content: flex-end;
    color: rgba(255, 255, 255, 0.75) !important;
    font-size: 11px !important;
    font-weight: 500;
}
.avatar-sender-1 {
    background: linear-gradient(135deg, #1572e8, #1c7ffc) !important;
    color: #ffffff !important;
}

/* INCOMING (RECEIVED) - SENDER 2: KAIADMIN NAVY CARD #202940 */
.message-row.sender-2-row {
    justify-content: flex-start !important;
}
.message-row.sender-2-row .message-wrapper {
    display: flex;
    flex-direction: row;
    align-items: flex-end;
    gap: 8px;
    max-width: 80%;
}
.message-row.sender-2-row .message-bubble {
    background: #202940 !important;
    color: #f4f6f9 !important;
    border: 1px solid #2a3454 !important;
    padding: 9px 14px !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25) !important;
    position: relative;
}

/* Grouping radii: small corner on receiver side */
.message-row.sender-2-row.group-single .message-bubble {
    border-radius: 18px 18px 18px 4px !important;
}
.message-row.sender-2-row.group-first .message-bubble {
    border-radius: 18px 18px 18px 6px !important;
    margin-bottom: 2px;
}
.message-row.sender-2-row.group-middle .message-bubble {
    border-radius: 6px 18px 18px 6px !important;
    margin-bottom: 2px;
}
.message-row.sender-2-row.group-last .message-bubble {
    border-radius: 6px 18px 18px 4px !important;
}

.message-row.sender-2-row .bubble-sender-name {
    color: #48abf7 !important;
    font-size: 0.72rem !important;
    font-weight: 700;
    margin-bottom: 2px;
}
.message-row.sender-2-row .bubble-meta {
    justify-content: flex-start;
    color: #8d9498 !important;
    font-size: 11px !important;
    font-weight: 500;
}
.avatar-sender-2 {
    background: #202940 !important;
    border: 1px solid #2a3454 !important;
    color: #48abf7 !important;
}

/* AVATAR STYLES & CONSECUTIVE HIDING */
.bubble-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.78rem;
    font-weight: 700;
    flex-shrink: 0;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.25);
    transition: opacity 0.15s ease;
}
.bubble-avatar.avatar-hidden {
    visibility: hidden !important;
    pointer-events: none !important;
}

/* TIMESTAMPS & READ RECEIPT COLORS */
.bubble-meta {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 4px;
}
.delivery-icon {
    font-size: 10px !important;
}
.delivery-read {
    color: #67e8f9 !important; /* cyan-300 / high contrast */
}
.delivery-delivered {
    color: rgba(255, 255, 255, 0.75) !important;
}
.delivery-sent {
    color: rgba(255, 255, 255, 0.45) !important;
}

/* QUICK MESSAGE ACTIONS (HOVER & FOCUS) */
.bubble-quick-actions {
    position: absolute;
    top: 4px;
    right: 4px;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.15s ease;
}
.message-bubble:hover .bubble-quick-actions,
.message-bubble:focus-within .bubble-quick-actions {
    opacity: 1;
    pointer-events: auto;
}
.btn-bubble-action {
    background: rgba(0, 0, 0, 0.5);
    border: none;
    color: #cbd5e1;
    width: 22px;
    height: 22px;
    border-radius: 6px;
    font-size: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
}
.btn-bubble-action:hover {
    color: #ffffff;
    background: rgba(0, 0, 0, 0.85);
}

/* ========================================================= */
/* DARK INPUT BAR & SEGMENTED SWITCHER                       */
/* ========================================================= */
.telegram-footer {
    background: #1a2035 !important;
    border-top: 1px solid #2a3454 !important;
    padding-bottom: max(12px, env(safe-area-inset-bottom, 12px)) !important;
}

.participant-switcher-bar {
    width: 100%;
}
.telegram-persona-capsule {
    display: inline-flex;
    background: #121624;
    padding: 3px;
    border-radius: 12px;
    border: 1px solid #2a3454;
    gap: 3px;
}
.telegram-persona-capsule .persona-tab {
    padding: 5px 14px;
    font-size: 0.8rem;
    font-weight: 600;
    color: #8d9498;
    border-radius: 9px;
    cursor: pointer;
    transition: all 0.15s ease;
    user-select: none;
    margin-bottom: 0;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.telegram-persona-capsule .persona-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #64748b;
    display: inline-block;
    transition: all 0.15s ease;
}
.telegram-persona-capsule input[type="radio"]:checked + .persona-tab {
    background: linear-gradient(135deg, #1572e8, #1c7ffc) !important;
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(21, 114, 232, 0.4);
    font-weight: 700;
}
.telegram-persona-capsule input[type="radio"]:checked + .persona-tab .persona-dot {
    background: #ffffff;
    box-shadow: 0 0 6px rgba(255, 255, 255, 0.8);
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
    width: 44px;
    height: 44px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    padding: 0;
    color: #8d9498;
    border: 1px solid #2e3856;
    background: #202940;
    transition: all 0.15s ease;
}
.btn-emoji-trigger:hover {
    color: #1572e8;
    border-color: #1572e8;
    background: #25304c;
    transform: scale(1.05);
}
.input-field-box {
    flex: 1 1 auto;
    min-width: 0;
    width: 100%;
}
.message-textarea {
    width: 100% !important;
    min-height: 44px !important;
    max-height: 120px !important;
    border-radius: 9999px !important;
    border: 1.5px solid #2e3856 !important;
    background: #202940 !important;
    padding: 11px 18px !important;
    font-size: 15px !important;
    line-height: 1.4 !important;
    color: #ffffff !important;
    resize: none !important;
    outline: none !important;
    box-shadow: none !important;
    transition: all 0.18s ease;
}
.message-textarea::placeholder {
    color: #718096 !important;
    opacity: 1 !important;
}
.message-textarea:focus {
    border-color: #1572e8 !important;
    box-shadow: 0 0 0 3px rgba(21, 114, 232, 0.25) !important;
    background: #1a2035 !important;
}

/* CIRCULAR GRADIENT SEND BUTTON WITH DISABLED STATE */
.btn-send-main {
    width: 44px !important;
    height: 44px !important;
    border-radius: 50% !important;
    background: linear-gradient(135deg, #1572e8, #1c7ffc) !important;
    color: #ffffff !important;
    padding: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    box-shadow: 0 2px 8px rgba(21, 114, 232, 0.4) !important;
    flex-shrink: 0;
    border: none !important;
    font-size: 1rem !important;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
.btn-send-main.btn-send-disabled,
.btn-send-main:disabled {
    opacity: 0.4 !important;
    cursor: not-allowed !important;
    transform: scale(0.92) !important;
    box-shadow: none !important;
}
.btn-send-main:not(:disabled):hover {
    transform: scale(1.08) !important;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.55) !important;
}
.btn-send-main:not(:disabled):active {
    transform: scale(0.95) !important;
}

/* TYPING INDICATOR */
.typing-indicator {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 2px 8px;
}
.typing-bubble {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #1e293b;
    border: 1px solid #334155;
    padding: 4px 8px;
    border-radius: 12px;
}
.typing-dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: #94a3b8;
    animation: typingBounce 1.4s infinite ease-in-out both;
}
.typing-dot:nth-child(1) { animation-delay: -0.32s; }
.typing-dot:nth-child(2) { animation-delay: -0.16s; }
@keyframes typingBounce {
    0%, 80%, 100% { transform: scale(0.6); opacity: 0.5; }
    40% { transform: scale(1.1); opacity: 1; }
}

/* ========================================================= */
/* COMPREHENSIVE PRIVACY BLUR (OBSCURE EVERYTHING)           */
/* ========================================================= */
.privacy-blur-active .message-bubble,
.privacy-blur-active .bubble-avatar,
.privacy-blur-active .bubble-reactions,
.privacy-blur-active .bubble-meta,
.privacy-blur-active .bubble-reply-preview,
.privacy-blur-active .date-divider span,
.privacy-blur-active .unread-messages-divider span,
.privacy-blur-active .telegram-header-avatar,
.privacy-blur-active .telegram-header h4,
.privacy-blur-active #presenceStatusText,
.privacy-blur-active #presencePulse,
.privacy-blur-active #pinnedMessageBanner,
.privacy-blur-active .active-sender-wrap,
.privacy-blur-active #typingIndicator,
.privacy-blur-active #replyBar,
.privacy-blur-active #editBar,
.privacy-blur-active .message-textarea {
    filter: blur(10px) !important;
    opacity: 0.22 !important;
    user-select: none !important;
    pointer-events: auto;
    transition: filter 0.25s ease, opacity 0.25s ease;
}

/* Hover peek: discreetly inspect a single message without exposing the rest */
.privacy-blur-active .message-bubble:hover,
.privacy-blur-active .message-row:hover .message-bubble {
    filter: blur(0px) !important;
    opacity: 1 !important;
    user-select: text !important;
}
.privacy-blur-active .message-row:hover .bubble-avatar,
.privacy-blur-active .message-row:hover .bubble-meta,
.privacy-blur-active .message-row:hover .bubble-reactions,
.privacy-blur-active .message-row:hover .bubble-reply-preview {
    filter: blur(0px) !important;
    opacity: 1 !important;
}
.privacy-blur-active .telegram-header:hover .telegram-header-avatar,
.privacy-blur-active .telegram-header:hover h4,
.privacy-blur-active .telegram-header:hover #presenceStatusText,
.privacy-blur-active .telegram-header:hover #presencePulse {
    filter: blur(0px) !important;
    opacity: 1 !important;
}
.privacy-blur-active .telegram-footer:hover .active-sender-wrap,
.privacy-blur-active .telegram-footer:hover .message-textarea {
    filter: blur(0px) !important;
    opacity: 1 !important;
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
        padding: 8px 12px !important;
        font-size: 0.88rem !important;
    }
    .telegram-header {
        padding: 6px 10px !important;
        min-height: 48px !important;
    }
    .btn-telegram-header {
        height: 36px !important;
        min-width: 36px !important;
        width: 36px !important;
        padding: 0 !important;
        font-size: 0.78rem !important;
        border-radius: 8px !important;
    }
    .telegram-header-avatar {
        width: 32px !important;
        height: 32px !important;
        font-size: 0.82rem !important;
    }
    .bubble-avatar {
        width: 26px !important;
        height: 26px !important;
        font-size: 0.68rem !important;
    }
    .telegram-persona-capsule .persona-tab {
        padding: 4px 10px !important;
        font-size: 0.74rem !important;
        max-width: 120px !important;
    }
    .btn-emoji-trigger, .btn-send-main {
        width: 40px !important;
        height: 40px !important;
        font-size: 0.95rem !important;
    }
    .message-textarea {
        min-height: 40px !important;
        padding: 9px 14px !important;
        font-size: 16px !important; /* Prevents auto zoom on iOS */
    }
    .chat-input-bar-wrap {
        padding-right: 0 !important;
    }
    .participant-switcher-bar {
        padding-right: 0 !important;
    }
    .chat-main-card {
        border-radius: 0 !important;
        border: none !important;
    }
    .emoji-picker-drawer {
        width: calc(100vw - 20px) !important;
        left: 10px !important;
        bottom: 74px !important;
        height: 250px !important;
    }
    .emoji-grid {
        grid-template-columns: repeat(6, 1fr) !important;
    }
    .scroll-down-btn {
        bottom: 76px !important;
        right: 14px !important;
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

/* ========================================================= */
/* SEARCH BAR & HIGHLIGHTING                                 */
/* ========================================================= */
.chat-search-bar {
    background: #111317 !important;
    border-bottom: 1px solid #282b34;
    transition: all 0.2s ease;
}
.search-highlight {
    background-color: #facc15 !important;
    color: #000000 !important;
    padding: 0 2px;
    border-radius: 2px;
    font-weight: 700;
}
.search-dimmed {
    opacity: 0.25 !important;
}

/* ========================================================= */
/* PINNED MESSAGE BANNER                                     */
/* ========================================================= */
.pinned-banner {
    background: rgba(30, 41, 59, 0.95) !important;
    border-bottom: 1px solid rgba(245, 158, 11, 0.4);
    cursor: pointer;
    backdrop-filter: blur(8px);
    transition: background 0.15s ease;
}
.pinned-banner:hover {
    background: rgba(51, 65, 85, 0.95) !important;
}

/* ========================================================= */
/* REPLY & EDIT BANNERS IN INPUT AREA                        */
/* ========================================================= */
.reply-banner, .edit-banner {
    animation: fadeInSlide 0.18s ease-out;
}
@keyframes fadeInSlide {
    from { opacity: 0; transform: translateY(-4px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ========================================================= */
/* BUBBLE REPLY QUOTE PREVIEWS                               */
/* ========================================================= */
.bubble-reply-preview {
    background: rgba(0, 0, 0, 0.2);
    border-left: 3px solid #60a5fa;
    padding: 3px 8px;
    border-radius: 4px;
    margin-bottom: 5px;
    font-size: 0.75rem;
    cursor: pointer;
    user-select: none;
    max-width: 100%;
}
.sender-1-row .bubble-reply-preview {
    background: rgba(0, 0, 0, 0.25);
    border-left-color: #93c5fd;
}
.bubble-reply-preview .reply-sender {
    font-weight: 700;
    font-size: 0.72rem;
    color: #93c5fd;
}
.sender-2-row .bubble-reply-preview .reply-sender {
    color: #60a5fa;
}
.bubble-reply-preview .reply-text {
    opacity: 0.85;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* ========================================================= */
/* REACTIONS PILLS & QUICK REACTION BAR                      */
/* ========================================================= */
.bubble-reactions-row {
    display: flex;
    flex-wrap: wrap;
    gap: 3px;
    margin-top: 4px;
}
.sender-1-row .bubble-reactions-row {
    justify-content: flex-end;
}
.reaction-pill {
    background: rgba(15, 23, 42, 0.85);
    border: 1px solid rgba(71, 85, 105, 0.6);
    color: #f1f5f9;
    border-radius: 9999px;
    padding: 1px 6px;
    font-size: 0.72rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    user-select: none;
    transition: transform 0.12s ease;
}
.reaction-pill:hover {
    transform: scale(1.1);
}
.reaction-pill.active {
    background: rgba(37, 99, 235, 0.35);
    border-color: #3b82f6;
    color: #93c5fd;
    font-weight: 700;
}
.quick-reaction-bar, .quick-reactions-bar {
    position: absolute;
    bottom: calc(100% + 4px);
    background: #1e293b;
    border: 1px solid #334155;
    border-radius: 9999px;
    padding: 2px 6px;
    display: flex;
    gap: 4px;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.4);
    z-index: 20;
    animation: fadeInSlide 0.15s ease-out;
}
.sender-1-row .quick-reaction-bar, .sender-1-row .quick-reactions-bar {
    right: 0;
}
.sender-2-row .quick-reaction-bar, .sender-2-row .quick-reactions-bar {
    left: 0;
}
.quick-reaction-btn {
    background: none;
    border: none;
    font-size: 1.15rem;
    padding: 2px 4px;
    cursor: pointer;
    transition: transform 0.12s ease;
    user-select: none;
}
.quick-reaction-btn:hover {
    transform: scale(1.3);
}

/* Double-Tap Heart Pop Burst Animation */
.heart-pop-animation {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.5);
    font-size: 2.2rem;
    pointer-events: none;
    animation: heartBurst 0.65s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    z-index: 50;
}
@keyframes heartBurst {
    0% { transform: translate(-50%, -50%) scale(0.3); opacity: 0.9; }
    50% { transform: translate(-50%, -50%) scale(1.4); opacity: 1; }
    100% { transform: translate(-50%, -70%) scale(1.1); opacity: 0; }
}

/* ========================================================= */
/* STICKERS & GIFS GRIDS                                     */
/* ========================================================= */
.stickers-grid, .gifs-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 6px;
    max-height: 200px;
    overflow-y: auto;
}
.sticker-item, .gif-item {
    background: #1e2026;
    border: 1px solid #2a2e39;
    border-radius: 10px;
    padding: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    color: #e2e8f0;
    text-align: center;
    cursor: pointer;
    transition: all 0.12s ease;
    user-select: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}
.sticker-item:hover, .gif-item:hover {
    background: #2a2e39;
    transform: translateY(-2px);
    border-color: #3b82f6;
    color: #ffffff;
}

/* ========================================================= */
/* THEMES & THEME CARD SELECTION                             */
/* ========================================================= */
.theme-card {
    background: #1e2026;
    border: 2px solid #2a2e39;
    border-radius: 12px;
    padding: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #ffffff;
    transition: all 0.15s ease;
}
.theme-card:hover {
    background: #272a33;
    border-color: #475569;
    color: #ffffff;
}
.theme-card.active {
    border-color: #3b82f6 !important;
    background: #1e2638 !important;
}
.theme-swatch {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: inline-block;
    flex-shrink: 0;
}
.theme-name {
    font-size: 0.82rem;
    font-weight: 600;
}

/* OCEAN BLUE THEME */
.chat-main-card.chat-theme-ocean .telegram-header {
    background: #0b1329 !important;
    border-color: #1e293b !important;
}
.chat-main-card.chat-theme-ocean .chat-messages-container {
    background: #060b18 !important;
}
.chat-main-card.chat-theme-ocean .telegram-footer {
    background: #0b1329 !important;
    border-color: #1e293b !important;
}
.chat-main-card.chat-theme-ocean .message-row.sender-1-row .message-bubble {
    background: linear-gradient(135deg, #0284c7, #0ea5e9) !important;
}

/* EMERALD FOREST THEME */
.chat-main-card.chat-theme-emerald .telegram-header {
    background: #03231a !important;
    border-color: #064e3b !important;
}
.chat-main-card.chat-theme-emerald .chat-messages-container {
    background: #021812 !important;
}
.chat-main-card.chat-theme-emerald .telegram-footer {
    background: #03231a !important;
    border-color: #064e3b !important;
}
.chat-main-card.chat-theme-emerald .message-row.sender-1-row .message-bubble {
    background: linear-gradient(135deg, #059669, #10b981) !important;
}

/* AMETHYST VIOLET THEME */
.chat-main-card.chat-theme-amethyst .telegram-header {
    background: #1b0a33 !important;
    border-color: #3b0764 !important;
}
.chat-main-card.chat-theme-amethyst .chat-messages-container {
    background: #100520 !important;
}
.chat-main-card.chat-theme-amethyst .telegram-footer {
    background: #1b0a33 !important;
    border-color: #3b0764 !important;
}
.chat-main-card.chat-theme-amethyst .message-row.sender-1-row .message-bubble {
    background: linear-gradient(135deg, #7c3aed, #9333ea) !important;
}
</style>

<!-- JAVASCRIPT LOGIC -->
<script>
    /* =================================================== */
    /* PIN KEYPAD & VERIFICATION SCRIPT                    */
    /* =================================================== */
    let currentPin = '';

    function enterDigit(digit) {
        if (currentPin.length < 4) {
            currentPin += digit;
            updatePinUi();
            if (currentPin.length === 4) {
                setTimeout(submitPin, 180);
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
        const pinScreen = document.getElementById('pinSecurityScreen');
        if (!pinScreen || pinScreen.classList.contains('d-none')) return;

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

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Verifying...';
        }

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
                @if($state === 'setup_pin')
                    window.location.reload();
                @else
                    unlockChatInUi();
                @endif
            } else {
                showPinAlert(response.body.message || 'Incorrect PIN code.');
                shakePinDots();
                clearPin();
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-unlock-alt me-2"></i> {{ $state === "setup_pin" ? "Set PIN & Continue" : "Verify & Unlock" }}';
                }
            }
        })
        .catch(err => {
            showPinAlert('An error occurred. Please try again.');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-unlock-alt me-2"></i> Verify & Unlock';
            }
        });
    }

    function unlockChatInUi() {
        const pinScreen = document.getElementById('pinSecurityScreen');
        const chatWrapper = document.getElementById('chatPageWrapper');
        if (pinScreen) pinScreen.classList.add('d-none');
        if (chatWrapper) chatWrapper.style.display = 'block';
        document.body.classList.add('chat-unlocked-mode');
        document.documentElement.classList.add('chat-unlocked-mode');
        startChatEngine();
    }

    function showPinAlert(msg) {
        const alertBox = document.getElementById('pinAlert');
        if (alertBox) {
            alertBox.textContent = msg;
            alertBox.classList.remove('d-none');
        }
    }

    function shakePinDots() {
        const container = document.getElementById('pinDots');
        if (container) {
            container.classList.add('shake');
            setTimeout(() => container.classList.remove('shake'), 400);
        }
    }

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
    let userIsTyping = false;
    let typingTimer = null;
    let userActiveStatus = localStorage.getItem('chat_active_status') !== 'false';
    let userReadReceipts = localStorage.getItem('chat_read_receipts') !== 'false';
    let activeChatTheme = localStorage.getItem('chat_theme_name') || 'midnight';
    let replyingTo = null;
    let editingMessageId = null;
    let currentPinnedMessage = null;
    let searchActive = false;



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

    // CHAT THEMES HANDLER
    function toggleChatTheme() {
        activeChatTheme = (activeChatTheme === 'white') ? 'midnight' : 'white';
        localStorage.setItem('chat_theme_name', activeChatTheme);
        applyThemeUi();
        syncThemeToServer(activeChatTheme);
    }

    function selectChatTheme(themeName, btn) {
        activeChatTheme = themeName;
        localStorage.setItem('chat_theme_name', themeName);
        applyThemeUi();
        syncThemeToServer(themeName);
        document.querySelectorAll('#themePickerGrid .theme-card').forEach(c => c.classList.remove('active'));
        if (btn) btn.classList.add('active');
    }

    function syncThemeToServer(themeName) {
        fetch('{{ route("chat.preferences") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ theme: themeName })
        }).catch(e => console.error('Theme sync error:', e));
    }

    function applyThemeUi() {
        const card = document.getElementById('chatMainCard');
        const icon = document.getElementById('themeIcon');
        const text = document.getElementById('themeBtnText');
        if (!card) return;

        // Remove all theme classes first
        card.classList.remove('chat-theme-white', 'chat-theme-ocean', 'chat-theme-emerald', 'chat-theme-amethyst');

        const mobIcon = document.getElementById('mobileThemeIcon');
        const mobText = document.getElementById('mobileThemeText');

        if (activeChatTheme === 'white') {
            card.classList.add('chat-theme-white');
            if (icon) icon.className = 'fas fa-moon me-1 text-dark';
            if (text) text.textContent = 'Black BG';
            if (mobIcon) mobIcon.className = 'fas fa-moon me-2 text-dark';
            if (mobText) mobText.textContent = 'Switch to Dark Theme';
        } else {
            if (activeChatTheme !== 'midnight') {
                card.classList.add('chat-theme-' + activeChatTheme);
            }
            if (icon) icon.className = 'fas fa-sun me-1 text-warning';
            if (text) text.textContent = 'White BG';
            if (mobIcon) mobIcon.className = 'fas fa-sun me-2 text-warning';
            if (mobText) mobText.textContent = 'Switch to Light Theme';
        }

        // Highlight matching theme card in modal
        document.querySelectorAll('#themePickerGrid .theme-card').forEach(c => {
            const isMatch = c.getAttribute('onclick') && c.getAttribute('onclick').includes("'" + activeChatTheme + "'");
            c.classList.toggle('active', isMatch);
        });
    }

    // PRIVACY CONTROLS
    function toggleActiveStatusSetting(checked) {
        userActiveStatus = checked;
        localStorage.setItem('chat_active_status', checked);
    }

    function toggleReadReceiptsSetting(checked) {
        userReadReceipts = checked;
        localStorage.setItem('chat_read_receipts', checked);
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
        const mobIcon = document.getElementById('mobileBlurIcon');
        const mobText = document.getElementById('mobileBlurText');

        if (blurEnabled) {
            if (card) card.classList.add('privacy-blur-active');
            if (icon) icon.className = 'fas fa-eye-slash text-warning';
            if (badge) badge.classList.remove('d-none');
            if (btnText) btnText.textContent = 'Unblur';
            if (mobIcon) mobIcon.className = 'fas fa-eye text-warning me-2';
            if (mobText) mobText.textContent = 'Disable Privacy Blur';
        } else {
            if (card) card.classList.remove('privacy-blur-active');
            if (icon) icon.className = 'fas fa-eye-slash';
            if (badge) badge.classList.add('d-none');
            if (btnText) btnText.textContent = 'Blur';
            if (mobIcon) mobIcon.className = 'fas fa-eye-slash text-info me-2';
            if (mobText) mobText.textContent = 'Privacy Blur Mode';
        }
    }

    function updateActivePersona(persona) {
        activePersona = persona;
        localStorage.setItem('chat_active_persona', persona);
        
        // Re-render conversation stream so sent messages are on the RIGHT and received on the LEFT
        document.getElementById('messagesStream').innerHTML = '';
        lastRenderedDate = '';
        lastRenderedSender = null;
        lastRenderedRowEl = null;
        lastMessageId = 0;
        fetchMessages(true);
    }

    function autoResizeTextarea(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
    }

    // ===================================================
    // FULL MEDIA DRAWER (EMOJIS, STICKERS, GIFS)
    // ===================================================
    function toggleEmojiPicker() {
        const drawer = document.getElementById('emojiPickerPopup');
        if (!drawer) return;
        if (drawer.classList.contains('d-none')) {
            drawer.classList.remove('d-none');
            renderEmojiCategory('all');
            const searchInput = document.getElementById('emojiSearchInput');
            if (searchInput) searchInput.focus();
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
        if (!grid) return;
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
        const q = (document.getElementById('emojiSearchInput').value || '').trim();
        const grid = document.getElementById('emojiGrid');
        if (!grid) return;
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
        if (!input) return;
        input.value += emoji;
        input.focus();
        autoResizeTextarea(input);
        handleMessageInput(input);
    }

    function sendDirectMediaMessage(text) {
        const input = document.getElementById('messageInput');
        if (input) {
            input.value = text;
            handleMessageInput(input);
        }
        sendMessage(activePersona);
        const drawer = document.getElementById('emojiPickerPopup');
        if (drawer) drawer.classList.add('d-none');
    }

    // ===================================================
    // SEARCH IN CONVERSATION
    // ===================================================
    function toggleSearch() {
        const bar = document.getElementById('chatSearchBar');
        if (!bar) return;
        searchActive = !searchActive;
        if (searchActive) {
            bar.classList.remove('d-none');
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.value = '';
                searchInput.focus();
            }
        } else {
            closeSearch();
        }
    }

    function closeSearch() {
        searchActive = false;
        const bar = document.getElementById('chatSearchBar');
        if (bar) bar.classList.add('d-none');
        const badge = document.getElementById('searchResultsBadge');
        if (badge) badge.classList.add('d-none');
        const searchInput = document.getElementById('searchInput');
        if (searchInput) searchInput.value = '';

        // Reset highlight & visible rows
        document.querySelectorAll('#messagesStream .message-row').forEach(row => {
            row.classList.remove('search-hidden', 'search-match');
            const textEl = row.querySelector('.message-text');
            if (textEl && textEl.dataset.originalHtml) {
                textEl.innerHTML = textEl.dataset.originalHtml;
                delete textEl.dataset.originalHtml;
            }
        });
    }

    function handleSearch(query) {
        const q = (query || '').trim().toLowerCase();
        const badge = document.getElementById('searchResultsBadge');
        const rows = document.querySelectorAll('#messagesStream .message-row');

        if (!q) {
            if (badge) badge.classList.add('d-none');
            rows.forEach(row => {
                row.classList.remove('search-hidden', 'search-match');
                const textEl = row.querySelector('.message-text');
                if (textEl && textEl.dataset.originalHtml) {
                    textEl.innerHTML = textEl.dataset.originalHtml;
                    delete textEl.dataset.originalHtml;
                }
            });
            return;
        }

        let matchCount = 0;
        let firstMatchRow = null;

        rows.forEach(row => {
            const textEl = row.querySelector('.message-text');
            if (!textEl) return;

            if (!textEl.dataset.originalHtml) {
                textEl.dataset.originalHtml = textEl.innerHTML;
            }

            const rawText = textEl.innerText.toLowerCase();
            if (rawText.includes(q)) {
                matchCount++;
                row.classList.remove('search-hidden');
                row.classList.add('search-match');
                if (!firstMatchRow) firstMatchRow = row;

                // Highlight matching characters safely
                const regex = new RegExp(`(${escapeRegex(q)})`, 'gi');
                textEl.innerHTML = textEl.dataset.originalHtml.replace(regex, '<mark class="search-highlight">$1</mark>');
            } else {
                row.classList.add('search-hidden');
                row.classList.remove('search-match');
                textEl.innerHTML = textEl.dataset.originalHtml;
            }
        });

        if (badge) {
            badge.textContent = `${matchCount} match${matchCount === 1 ? '' : 'es'}`;
            badge.classList.remove('d-none');
        }

        if (firstMatchRow) {
            firstMatchRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    function escapeRegex(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    // ===================================================
    // REPLY & EDIT FUNCTIONALITY
    // ===================================================
    function replyToMessage(id, sender, text) {
        cancelEdit();
        replyingTo = { id: id, sender: sender, text: text };
        const replyBar = document.getElementById('replyBar');
        const replySender = document.getElementById('replySenderName');
        const replyPreview = document.getElementById('replyPreviewText');

        if (replySender) replySender.textContent = sender || 'Participant';
        if (replyPreview) replyPreview.textContent = text || '';
        if (replyBar) replyBar.classList.remove('d-none');

        const input = document.getElementById('messageInput');
        if (input) input.focus();
    }

    function cancelReply() {
        replyingTo = null;
        const replyBar = document.getElementById('replyBar');
        if (replyBar) replyBar.classList.add('d-none');
    }

    function startEditMessage(id, text) {
        cancelReply();
        editingMessageId = id;
        const editBar = document.getElementById('editBar');
        if (editBar) editBar.classList.remove('d-none');

        const input = document.getElementById('messageInput');
        if (input) {
            input.value = text;
            autoResizeTextarea(input);
            handleMessageInput(input);
            input.focus();
            input.setSelectionRange(input.value.length, input.value.length);
        }
    }

    function cancelEdit() {
        editingMessageId = null;
        const editBar = document.getElementById('editBar');
        if (editBar) editBar.classList.add('d-none');

        const input = document.getElementById('messageInput');
        if (input) {
            input.value = '';
            autoResizeTextarea(input);
            handleMessageInput(input);
        }
    }

    function handleInputKey(event) {
        if (event.key === 'Escape') {
            if (editingMessageId) cancelEdit();
            if (replyingTo) cancelReply();
            if (searchActive) closeSearch();
            return;
        }

        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            sendMessage(activePersona);
        }
    }

    // ===================================================
    // MESSAGE ACTIONS: UNSEND, PIN, COPY, FORWARD
    // ===================================================
    function removeMessageElement(row) {
        if (!row) return;
        const prevRow = row.previousElementSibling;
        const nextRow = row.nextElementSibling;
        const stream = document.getElementById('messagesStream');

        row.remove();

        // Restore avatar to previous consecutive message if it became the last one
        if (prevRow && prevRow.classList.contains('message-row')) {
            if (!nextRow || !nextRow.classList.contains('message-row') || nextRow.dataset.sender !== prevRow.dataset.sender) {
                const avatar = prevRow.querySelector('.bubble-avatar');
                if (avatar) avatar.classList.remove('avatar-hidden');
                prevRow.classList.remove('group-middle', 'group-first');
                prevRow.classList.add('group-last');
            }
        }

        // Clean up empty date dividers and show empty state if all messages removed
        if (stream) {
            const messageRows = stream.querySelectorAll('.message-row');
            if (messageRows.length === 0) {
                stream.innerHTML = '';
                lastRenderedDate = '';
                lastRenderedSender = null;
                lastRenderedRowEl = null;
                const emptyChat = document.getElementById('emptyChat');
                if (emptyChat) emptyChat.classList.remove('d-none');
            } else {
                // Remove any date dividers that have no following message rows
                stream.querySelectorAll('.date-divider').forEach(divider => {
                    let next = divider.nextElementSibling;
                    let hasRow = false;
                    while (next && !next.classList.contains('date-divider')) {
                        if (next.classList.contains('message-row')) {
                            hasRow = true;
                            break;
                        }
                        next = next.nextElementSibling;
                    }
                    if (!hasRow) divider.remove();
                });
            }
        }
    }

    function unsendMessage(id) {
        if (!confirm('Unsend this message for everyone?')) return;

        // Immediately remove from sender's view with zero trace
        const row = document.getElementById('msg-' + id);
        if (row) {
            removeMessageElement(row);
        }

        if (currentPinnedMessage && currentPinnedMessage.id == id) {
            currentPinnedMessage = null;
            hidePinnedBanner();
        }

        fetch('{{ route("chat.delete", ":id") }}'.replace(':id', id), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .catch(err => console.error('Unsend error:', err));
    }

    function forwardMessage(id, text) {
        const input = document.getElementById('messageInput');
        if (!input) return;
        input.value = text;
        autoResizeTextarea(input);
        handleMessageInput(input);
        input.focus();
        scrollToBottom(true);
    }

    function togglePinMessage(id) {
        fetch('{{ route("chat.pin") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                message_id: id,
                id: id
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                if (data.message && data.message.is_pinned) {
                    currentPinnedMessage = data.message;
                    showPinnedBanner(data.message);
                } else {
                    currentPinnedMessage = null;
                    hidePinnedBanner();
                }
                if (data.message) {
                    updateExistingMessage(data.message);
                }
            }
        })
        .catch(err => console.error('Pin error:', err));
    }

    function unpinCurrentMessage() {
        if (!currentPinnedMessage) return;
        togglePinMessage(currentPinnedMessage.id);
    }

    function showPinnedBanner(msg) {
        const banner = document.getElementById('pinnedMessageBanner');
        const sender = document.getElementById('pinnedSender');
        const text = document.getElementById('pinnedText');
        if (!banner) return;
        if (sender) sender.textContent = msg.sender_name || 'Pinned';
        if (text) text.textContent = msg.message || '';
        banner.classList.remove('d-none');
    }

    function hidePinnedBanner() {
        const banner = document.getElementById('pinnedMessageBanner');
        if (banner) banner.classList.add('d-none');
    }

    function scrollToPinnedMessage() {
        if (!currentPinnedMessage) return;
        scrollToMessage(currentPinnedMessage.id);
    }

    function scrollToMessage(id) {
        const el = document.getElementById('msg-' + id);
        if (!el) return;
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        el.classList.add('message-highlight-flash');
        setTimeout(() => el.classList.remove('message-highlight-flash'), 1600);
    }

    // ===================================================
    // REACTIONS & QUICK REACTION BAR (OPTIMISTIC UI: 0ms)
    // ===================================================
    function optimisticToggleReaction(messageId, emoji) {
        const row = document.getElementById('msg-' + messageId);
        if (!row) return;
        const bubble = row.querySelector('.message-bubble');
        if (!bubble) return;

        let reactionsWrap = row.querySelector('.bubble-reactions');
        if (!reactionsWrap) {
            reactionsWrap = document.createElement('div');
            reactionsWrap.className = 'bubble-reactions';
            bubble.appendChild(reactionsWrap);
        }

        let targetPill = null;
        reactionsWrap.querySelectorAll('.reaction-pill').forEach(pill => {
            const emSpan = pill.querySelector('.reaction-emoji');
            if (emSpan && emSpan.textContent.trim() === emoji) {
                targetPill = pill;
            }
        });

        if (targetPill) {
            const countSpan = targetPill.querySelector('.reaction-count');
            let count = parseInt(countSpan ? countSpan.textContent : '1', 10) || 1;
            const isActive = targetPill.classList.contains('active');

            if (isActive) {
                count--;
                if (count <= 0) {
                    targetPill.remove();
                    if (reactionsWrap.querySelectorAll('.reaction-pill').length === 0) {
                        reactionsWrap.remove();
                    }
                } else {
                    if (countSpan) countSpan.textContent = count;
                    targetPill.classList.remove('active');
                }
            } else {
                count++;
                if (countSpan) countSpan.textContent = count;
                targetPill.classList.add('active');
            }
        } else {
            const newPill = document.createElement('button');
            newPill.type = 'button';
            newPill.className = 'reaction-pill active';
            newPill.onclick = function() {
                toggleReaction(messageId, emoji);
            };
            newPill.innerHTML = `
                <span class="reaction-emoji">${emoji}</span>
                <span class="reaction-count">1</span>
            `;
            reactionsWrap.appendChild(newPill);
        }
    }

    function toggleReaction(messageId, emoji) {
        closeAllQuickReactions();

        // 0ms INSTANT OPTIMISTIC UI:
        optimisticToggleReaction(messageId, emoji);
        if (emoji === '❤️') {
            const row = document.getElementById('msg-' + messageId);
            if (row) {
                const bubble = row.querySelector('.message-bubble');
                if (bubble) showHeartBurst(bubble);
            }
        }

        fetch('{{ route("chat.react") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                message_id: messageId,
                id: messageId,
                emoji: emoji,
                sender_name: activePersona,
                user: activePersona
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success' && data.message) {
                updateExistingMessage(data.message);
            }
        })
        .catch(err => console.error('Reaction error:', err));
    }

    function showQuickReactions(messageId, event) {
        if (event) event.stopPropagation();
        const existingBar = document.getElementById('quickBar-' + messageId);
        if (existingBar) {
            existingBar.remove();
            return;
        }

        closeAllQuickReactions();

        const row = document.getElementById('msg-' + messageId);
        if (!row) return;

        const bubble = row.querySelector('.message-bubble');
        if (!bubble) return;

        const quickBar = document.createElement('div');
        quickBar.id = 'quickBar-' + messageId;
        quickBar.className = 'quick-reaction-bar quick-reactions-bar shadow-lg';

        const quickEmojis = ['❤️', '👍', '😂', '🔥', '👏', '🎉', '😢'];
        quickEmojis.forEach(em => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'quick-reaction-btn';
            btn.textContent = em;
            btn.onclick = function(e) {
                e.stopPropagation();
                toggleReaction(messageId, em);
            };
            quickBar.appendChild(btn);
        });

        bubble.appendChild(quickBar);
    }

    function closeAllQuickReactions() {
        document.querySelectorAll('.quick-reactions-bar, .quick-reaction-bar').forEach(bar => bar.remove());
    }

    function showHeartBurst(bubble) {
        if (!bubble) return;
        const heartPop = document.createElement('div');
        heartPop.className = 'heart-pop-animation';
        heartPop.textContent = '❤️';
        bubble.appendChild(heartPop);
        setTimeout(() => heartPop.remove(), 700);
    }

    // ===================================================
    // UPDATE PARTICIPANT NAMES
    // ===================================================
    function saveParticipantNames() {
        const n1 = document.getElementById('inputName1').value.trim();
        const n2 = document.getElementById('inputName2').value.trim();
        const alertBox = document.getElementById('namesAlert');

        if (!n1 || !n2) {
            if (alertBox) {
                alertBox.textContent = 'Both participant names are required.';
                alertBox.classList.remove('d-none');
            }
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
                const modalEl = document.getElementById('chatSettingsModal') || document.getElementById('editNamesModal');
                if (modalEl) {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                }

                // Re-render messages
                document.getElementById('messagesStream').innerHTML = '';
                lastRenderedDate = '';
                lastRenderedSender = null;
                lastRenderedRowEl = null;
                lastMessageId = 0;
                fetchMessages(true);
            }
        });
    }

    // ===================================================
    // INITIAL LOAD & LIFECYCLE
    // ===================================================
    let chatEngineStarted = false;

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

    function startChatEngine() {
        if (chatEngineStarted) return;
        chatEngineStarted = true;

        applySoundUi();
        applyThemeUi();
        applyBlurUi();

        // Restore privacy toggles
        const toggleActive = document.getElementById('toggleActiveStatus');
        if (toggleActive) toggleActive.checked = userActiveStatus;
        const toggleReceipts = document.getElementById('toggleReadReceipts');
        if (toggleReceipts) toggleReceipts.checked = userReadReceipts;

        // Sync active persona radio button
        if (activePersona === participantNames.name2) {
            const r2 = document.getElementById('persona2');
            if (r2) r2.checked = true;
        } else {
            const r1 = document.getElementById('persona1');
            if (r1) r1.checked = true;
        }

        fetchMessages(true);
        startPolling();

        // Initialize message input & button state
        const msgInput = document.getElementById('messageInput');
        if (msgInput) {
            handleMessageInput(msgInput);
            msgInput.focus();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        @if($isUnlocked)
            unlockChatInUi();
        @endif

        document.addEventListener('visibilitychange', function() {
            if (!chatEngineStarted) return;
            if (document.hidden) {
                stopPolling();
            } else {
                fetchMessages(false);
                startPolling();
            }
        });

        const container = document.getElementById('chatContainer');
        if (container) {
            container.addEventListener('scroll', function() {
                const btnScroll = document.getElementById('btnScrollBottom');
                if (!btnScroll) return;
                const isNearBot = container.scrollHeight - container.scrollTop - container.clientHeight < 120;
                if (isNearBot) {
                    btnScroll.classList.add('d-none');
                } else {
                    btnScroll.classList.remove('d-none');
                }
            });
        }

        // Close emoji picker & quick reactions when clicking outside
        document.addEventListener('click', function(e) {
            const drawer = document.getElementById('emojiPickerPopup');
            if (drawer && !drawer.classList.contains('d-none')) {
                if (!drawer.contains(e.target) && !e.target.closest('button[onclick="toggleEmojiPicker()"]')) {
                    drawer.classList.add('d-none');
                }
            }
            if (!e.target.closest('.quick-reactions-bar') && !e.target.closest('.quick-reaction-bar') && !e.target.closest('.btn-bubble-react')) {
                closeAllQuickReactions();
            }
        });
    });

    // ===================================================
    // FETCH MESSAGES & REAL-TIME PRESENCE POLLING
    // ===================================================
    function fetchMessages(isInitial = false) {
        if (isPolling) return;
        isPolling = true;

        const params = new URLSearchParams();
        if (lastMessageId > 0) params.append('after_id', lastMessageId);
        params.append('current_user', activePersona);
        params.append('is_typing', userIsTyping ? '1' : '0');
        params.append('active_status', userActiveStatus ? '1' : '0');
        params.append('read_receipts', userReadReceipts ? '1' : '0');

        const url = '{{ route("chat.messages") }}?' + params.toString();

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
            if (!data) return;

            // 1. Sync other user's presence & typing
            const statusEl = document.getElementById('presenceStatusText');
            const pulseEl = document.getElementById('presencePulse');
            const typingBubble = document.getElementById('typingIndicator');

            if (data.other_presence) {
                const op = data.other_presence;
                if (op.is_typing) {
                    if (statusEl) {
                        statusEl.textContent = 'typing...';
                        statusEl.className = 'text-info fw-semibold';
                    }
                    if (typingBubble) typingBubble.classList.remove('d-none');
                    if (pulseEl) pulseEl.classList.remove('d-none');
                } else if (op.is_online) {
                    if (statusEl) {
                        statusEl.textContent = 'Active now';
                        statusEl.className = 'text-emerald-400 fw-semibold';
                    }
                    if (typingBubble) typingBubble.classList.add('d-none');
                    if (pulseEl) pulseEl.classList.remove('d-none');
                } else {
                    if (statusEl) {
                        statusEl.textContent = op.status_text || 'Offline';
                        statusEl.className = 'text-white-50 small';
                    }
                    if (typingBubble) typingBubble.classList.add('d-none');
                    if (pulseEl) pulseEl.classList.add('d-none');
                }
            }

            // 2. Sync pinned message
            if (data.pinned_message) {
                currentPinnedMessage = data.pinned_message;
                showPinnedBanner(data.pinned_message);
            } else if (!data.pinned_message && currentPinnedMessage) {
                currentPinnedMessage = null;
                hidePinnedBanner();
            }

            // 3. Process live updated messages (reactions, edits, deletions, receipts)
            if (data.updated_messages && data.updated_messages.length > 0) {
                data.updated_messages.forEach(updatedMsg => {
                    updateExistingMessage(updatedMsg);
                });
            }

            // 4. Process new messages
            const loadingChat = document.getElementById('loadingChat');
            if (loadingChat) loadingChat.classList.add('d-none');

            if (isInitial && (!data.messages || data.messages.length === 0)) {
                const emptyChat = document.getElementById('emptyChat');
                if (emptyChat) emptyChat.classList.remove('d-none');
                return;
            }

            if (data.messages && data.messages.length > 0) {
                const emptyChat = document.getElementById('emptyChat');
                if (emptyChat) emptyChat.classList.add('d-none');
                let hasNewFromOther = false;

                // On initial load, detect the first unread/unseen message from the other participant
                let firstUnreadMsgId = null;
                if (isInitial) {
                    for (const m of data.messages) {
                        if (m.is_deleted) continue;
                        const isFromOther = (m.sender_name || '').trim().toLowerCase() !== (activePersona || '').trim().toLowerCase();
                        if (isFromOther && !m.is_read) {
                            firstUnreadMsgId = m.id;
                            break;
                        }
                    }
                }

                data.messages.forEach(msg => {
                    if (msg.is_deleted) return;
                    if (msg.id > lastMessageId) {
                        lastMessageId = msg.id;

                        // Insert visual unread indicator divider before the first unseen message
                        if (isInitial && firstUnreadMsgId && msg.id === firstUnreadMsgId) {
                            const stream = document.getElementById('messagesStream');
                            if (stream && !stream.querySelector('#unreadMessagesDivider')) {
                                const unreadDivider = document.createElement('div');
                                unreadDivider.id = 'unreadMessagesDivider';
                                unreadDivider.className = 'unread-messages-divider';
                                unreadDivider.innerHTML = `<span><i class="fas fa-arrow-down me-1"></i> New Messages</span>`;
                                stream.appendChild(unreadDivider);
                            }
                        }

                        renderMessage(msg);
                        if (!isInitial && msg.sender_name !== activePersona) {
                            hasNewFromOther = true;
                        }
                    }
                });

                if (hasNewFromOther) {
                    playNotificationChime();
                }

                if (isInitial) {
                    // Start directly at unseen/new message or bottom with zero scroll-down animation
                    const container = document.getElementById('chatContainer');
                    if (container) {
                        container.style.scrollBehavior = 'auto';
                        if (firstUnreadMsgId) {
                            const unreadTarget = document.getElementById('unreadMessagesDivider') || document.getElementById('msg-' + firstUnreadMsgId);
                            if (unreadTarget) {
                                container.scrollTop = Math.max(0, unreadTarget.offsetTop - 50);
                            } else {
                                container.scrollTop = container.scrollHeight;
                            }
                        } else {
                            container.scrollTop = container.scrollHeight;
                        }
                        // Restore smooth scroll for subsequent interactions
                        setTimeout(() => {
                            container.style.scrollBehavior = '';
                        }, 120);
                    }
                } else if (isNearBottom()) {
                    scrollToBottom(true);
                }
            }
        })
        .catch(err => {
            isPolling = false;
            console.error('Polling error:', err);
        });
    }

    let lastRenderedDate = '';
    let lastRenderedSender = null;
    let lastRenderedRowEl = null;
    let typingPingTimer = null;

    function handleMessageInput(textarea) {
        autoResizeTextarea(textarea);
        const btnSend = document.getElementById('btnMainSend');
        const hasText = textarea.value.trim().length > 0;

        if (btnSend) {
            btnSend.disabled = !hasText;
            if (hasText) {
                btnSend.classList.remove('btn-send-disabled');
                btnSend.classList.add('btn-send-active');
            } else {
                btnSend.classList.add('btn-send-disabled');
                btnSend.classList.remove('btn-send-active');
            }
        }

        // Heartbeat typing state
        const wasTyping = userIsTyping;
        userIsTyping = hasText;

        if (typingTimer) clearTimeout(typingTimer);
        typingTimer = setTimeout(() => {
            userIsTyping = false;
        }, 2800);

        // Immediate ping when typing starts so other user sees it in <100ms
        if (!wasTyping && hasText) {
            if (typingPingTimer) clearTimeout(typingPingTimer);
            typingPingTimer = setTimeout(() => {
                fetchMessages(false);
            }, 80);
        }
    }

    function copyMessageText(id) {
        const el = document.getElementById('msg-' + id);
        if (!el) return;
        const textEl = el.querySelector('.message-text');
        if (!textEl) return;
        navigator.clipboard.writeText(textEl.innerText).then(() => {
            const btn = el.querySelector('.btn-bubble-copy');
            if (btn) {
                btn.innerHTML = '<i class="fas fa-check text-success"></i>';
                setTimeout(() => {
                    btn.innerHTML = '<i class="far fa-copy"></i>';
                }, 1500);
            }
        }).catch(err => console.error('Copy failed:', err));
    }

    // ===================================================
    // IN-PLACE UPDATE FOR EXISTING MESSAGES
    // ===================================================
    function updateExistingMessage(msg) {
        const row = document.getElementById('msg-' + msg.id);
        if (!row) return;

        // If message is deleted, completely remove it from view with zero trace
        if (msg.is_deleted) {
            removeMessageElement(row);
            if (currentPinnedMessage && currentPinnedMessage.id == msg.id) {
                currentPinnedMessage = null;
                hidePinnedBanner();
            }
            return;
        }

        const bubble = row.querySelector('.message-bubble');
        if (!bubble) return;

        // Update message text & edited badge
        const textEl = row.querySelector('.message-text');
        if (textEl) {
            textEl.innerHTML = escapeHtml(msg.message).replace(/\n/g, '<br>');
        }

        let editedBadge = row.querySelector('.edited-badge');
        if (msg.is_edited && !editedBadge) {
            const metaEl = row.querySelector('.bubble-meta');
            if (metaEl) {
                const span = document.createElement('span');
                span.className = 'edited-badge ms-1';
                span.textContent = '(edited)';
                metaEl.insertBefore(span, metaEl.querySelector('.delivery-icon'));
            }
        }

        // Update read receipt icon
        const deliveryIcon = row.querySelector('.delivery-icon');
        if (deliveryIcon) {
            if (msg.is_read) {
                deliveryIcon.className = 'fas fa-check-double ms-1 delivery-icon text-info';
                deliveryIcon.title = 'Read';
            } else {
                deliveryIcon.className = 'fas fa-check-double ms-1 delivery-icon text-muted';
                deliveryIcon.title = 'Delivered';
            }
        }

        // Update reactions bar
        let reactionsWrap = row.querySelector('.bubble-reactions');
        const reactionsHtml = buildReactionsHtml(msg);
        if (reactionsHtml) {
            if (!reactionsWrap) {
                reactionsWrap = document.createElement('div');
                reactionsWrap.className = 'bubble-reactions';
                bubble.appendChild(reactionsWrap);
            }
            reactionsWrap.innerHTML = reactionsHtml;
        } else if (reactionsWrap) {
            reactionsWrap.remove();
        }
    }

    function buildReactionsHtml(msg) {
        if (!msg.reactions || Object.keys(msg.reactions).length === 0) return '';
        let html = '';
        for (const [emoji, users] of Object.entries(msg.reactions)) {
            if (users && users.length > 0) {
                const hasReacted = users.includes(activePersona);
                html += `
                    <button type="button" class="reaction-pill ${hasReacted ? 'active' : ''}" onclick="toggleReaction(${msg.id}, '${emoji}')" title="${escapeHtml(users.join(', '))}">
                        <span class="reaction-emoji">${emoji}</span>
                        <span class="reaction-count">${users.length}</span>
                    </button>
                `;
            }
        }
        return html;
    }

    // ===================================================
    // RENDER SINGLE MESSAGE
    // ===================================================
    function renderMessage(msg) {
        if (!msg || msg.is_deleted) return;

        const existingRow = document.getElementById('msg-' + msg.id);
        if (existingRow) {
            updateExistingMessage(msg);
            return;
        }

        const stream = document.getElementById('messagesStream');
        if (!stream) return;

        const msgSender = (msg.sender_name || '').trim();
        const currentSender = (activePersona || '').trim();
        const isSentByMe = (msgSender.toLowerCase() === currentSender.toLowerCase());
        const currentDateLabel = msg.date_label || 'Today';

        // Date divider: strictly day-wise, ensure at most ONE divider per calendar date in stream
        if (currentDateLabel !== lastRenderedDate) {
            const existingDivider = stream.querySelector(`.date-divider[data-date="${currentDateLabel}"]`);
            if (!existingDivider) {
                const div = document.createElement('div');
                div.className = 'date-divider';
                div.dataset.date = currentDateLabel;
                div.innerHTML = `<span>${escapeHtml(currentDateLabel)}</span>`;
                stream.appendChild(div);
            }
            lastRenderedDate = currentDateLabel;
            lastRenderedSender = null;
            lastRenderedRowEl = null;
        }

        const isConsecutive = (lastRenderedSender !== null && lastRenderedSender === msgSender && lastRenderedRowEl !== null);
        const rowClass = isSentByMe ? 'sender-1-row' : 'sender-2-row';
        const avatarClass = isSentByMe ? 'avatar-sender-1' : 'avatar-sender-2';
        const initial = (msgSender || 'U').charAt(0).toUpperCase();

        if (isConsecutive) {
            const prevAvatar = lastRenderedRowEl.querySelector('.bubble-avatar');
            if (prevAvatar) prevAvatar.classList.add('avatar-hidden');
            if (lastRenderedRowEl.classList.contains('group-last')) {
                lastRenderedRowEl.classList.remove('group-last');
                lastRenderedRowEl.classList.add('group-middle');
            } else if (lastRenderedRowEl.classList.contains('group-single')) {
                lastRenderedRowEl.classList.remove('group-single');
                lastRenderedRowEl.classList.add('group-first');
            }
        }

        const msgDiv = document.createElement('div');
        msgDiv.className = `message-row ${rowClass} ${isConsecutive ? 'group-last group-consecutive' : 'group-single'}`;
        msgDiv.id = `msg-${msg.id}`;
        msgDiv.dataset.id = msg.id;
        msgDiv.dataset.sender = msgSender;
        msgDiv.dataset.date = currentDateLabel;

        const formattedText = escapeHtml(msg.message).replace(/\n/g, '<br>');

        const senderNameHtml = !isConsecutive ? `
            <div class="bubble-sender-name">
                ${escapeHtml(msgSender)}
            </div>
        ` : '';

        const avatarHtml = `
            <div class="bubble-avatar ${avatarClass}" title="${escapeHtml(msgSender)}">
                ${initial}
            </div>
        `;

        // Reply quote preview inside bubble
        let replyQuoteHtml = '';
        if (msg.reply_to_text) {
            replyQuoteHtml = `
                <div class="bubble-reply-preview" onclick="scrollToMessage(${msg.reply_to_id})">
                    <div class="reply-sender text-truncate">${escapeHtml(msg.reply_to_sender || 'Participant')}</div>
                    <div class="reply-text text-truncate">${escapeHtml(msg.reply_to_text)}</div>
                </div>
            `;
        }

        // Reactions counter pills
        const reactionsHtml = buildReactionsHtml(msg);
        const reactionsWrapHtml = reactionsHtml ? `<div class="bubble-reactions">${reactionsHtml}</div>` : '';

        // Delivery icon
        let deliveryIconHtml = '';
        if (isSentByMe) {
            const isRead = msg.is_read;
            deliveryIconHtml = `<i class="fas fa-check-double ms-1 delivery-icon ${isRead ? 'text-info' : 'text-muted'}" title="${isRead ? 'Read' : 'Delivered'}"></i>`;
        }

        const editedLabelHtml = msg.is_edited ? '<span class="edited-badge ms-1">(edited)</span>' : '';

        // Bubble Action Buttons (Quick React, Reply, Copy, Forward, Pin, Edit, Unsend)
        const isPinned = msg.is_pinned;
        const actionsHtml = `
            <div class="bubble-quick-actions">
                <button type="button" class="btn-bubble-action btn-bubble-react" onclick="showQuickReactions(${msg.id}, event)" title="React with emoji" aria-label="React">
                    <i class="far fa-smile"></i>
                </button>
                <button type="button" class="btn-bubble-action btn-bubble-reply" onclick="replyToMessage(${msg.id}, '${escapeJs(msgSender)}', '${escapeJs(msg.message)}')" title="Reply" aria-label="Reply">
                    <i class="fas fa-reply"></i>
                </button>
                <button type="button" class="btn-bubble-action btn-bubble-copy" onclick="copyMessageText('${msg.id}')" title="Copy text" aria-label="Copy">
                    <i class="far fa-copy"></i>
                </button>
                <div class="dropdown d-inline">
                    <button type="button" class="btn-bubble-action" data-bs-toggle="dropdown" aria-expanded="false" title="More" aria-label="More">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark shadow-sm border telegram-dropdown-menu">
                        <li>
                            <a class="dropdown-item py-1.5" href="javascript:void(0)" onclick="forwardMessage(${msg.id}, '${escapeJs(msg.message)}')">
                                <i class="fas fa-share me-2 text-info"></i> Forward
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-1.5" href="javascript:void(0)" onclick="togglePinMessage(${msg.id})">
                                <i class="fas fa-thumbtack me-2 text-warning"></i> ${isPinned ? 'Unpin Message' : 'Pin Message'}
                            </a>
                        </li>
                        ${isSentByMe ? `
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item py-1.5" href="javascript:void(0)" onclick="startEditMessage(${msg.id}, '${escapeJs(msg.message)}')">
                                <i class="fas fa-pencil-alt me-2 text-primary"></i> Edit Message
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-1.5 text-danger" href="javascript:void(0)" onclick="unsendMessage(${msg.id})">
                                <i class="fas fa-trash-alt me-2"></i> Unsend for Everyone
                            </a>
                        </li>
                        ` : ''}
                    </ul>
                </div>
            </div>
        `;

        msgDiv.innerHTML = `
            <div class="message-wrapper">
                ${avatarHtml}
                <div class="message-bubble position-relative" tabindex="0">
                    ${senderNameHtml}
                    ${replyQuoteHtml}
                    <div class="message-text" style="font-size: 0.93rem; line-height: 1.45; word-break: break-word;">
                        ${formattedText}
                    </div>
                    <div class="bubble-meta">
                        <span>${escapeHtml(msg.formatted_time || '')}</span>
                        ${editedLabelHtml}
                        ${deliveryIconHtml}
                    </div>
                    ${reactionsWrapHtml}
                    ${actionsHtml}
                </div>
            </div>
        `;

        // Attach Gestures: Double-Tap to ❤️ (mobile & desktop) and Mobile Swipe-to-Reply
        attachBubbleGestures(msgDiv, msg);

        stream.appendChild(msgDiv);
        lastRenderedSender = msgSender;
        lastRenderedRowEl = msgDiv;
    }

    function attachBubbleGestures(msgDiv, msg) {
        const bubble = msgDiv.querySelector('.message-bubble');
        if (!bubble || msg.is_deleted) return;

        let lastTapTime = 0;
        let lastTouchTime = 0;

        // Desktop double click
        bubble.addEventListener('click', function(e) {
            if (e.target.closest('.bubble-quick-actions') || e.target.closest('.reaction-pill') || e.target.closest('.bubble-reply-preview') || e.target.closest('.dropdown-menu')) {
                return;
            }
            const now = Date.now();
            if (now - lastTapTime < 350) {
                toggleReaction(msg.id, '❤️');
                showHeartBurst(bubble);
                lastTapTime = 0;
            } else {
                lastTapTime = now;
            }
        });

        // Mobile Swipe-to-reply gesture and mobile double tap
        let touchStartX = 0;
        let touchStartY = 0;
        let swipeDistance = 0;

        bubble.addEventListener('touchstart', function(e) {
            if (e.touches.length === 1) {
                touchStartX = e.touches[0].clientX;
                touchStartY = e.touches[0].clientY;
                swipeDistance = 0;
            }
        }, { passive: true });

        bubble.addEventListener('touchmove', function(e) {
            if (e.touches.length === 1) {
                const currentX = e.touches[0].clientX;
                const currentY = e.touches[0].clientY;
                const deltaX = currentX - touchStartX;
                const deltaY = currentY - touchStartY;

                // Only horizontal rightward swipe
                if (deltaX > 10 && Math.abs(deltaY) < 30) {
                    swipeDistance = Math.min(deltaX, 80);
                    bubble.style.transform = `translateX(${swipeDistance}px)`;
                    bubble.style.transition = 'none';
                }
            }
        }, { passive: true });

        bubble.addEventListener('touchend', function(e) {
            if (swipeDistance > 45) {
                replyToMessage(msg.id, msg.sender_name, msg.message);
                bubble.style.transition = 'transform 0.25s ease';
                bubble.style.transform = 'translateX(0)';
                swipeDistance = 0;
                return;
            }
            bubble.style.transition = 'transform 0.25s ease';
            bubble.style.transform = 'translateX(0)';
            swipeDistance = 0;

            if (e.target.closest('.bubble-quick-actions') || e.target.closest('.reaction-pill') || e.target.closest('.bubble-reply-preview') || e.target.closest('.dropdown-menu')) {
                return;
            }
            const now = Date.now();
            if (now - lastTouchTime < 350) {
                toggleReaction(msg.id, '❤️');
                showHeartBurst(bubble);
                lastTouchTime = 0;
            } else {
                lastTouchTime = now;
            }
        }, { passive: true });
    }

    // ===================================================
    // SEND MESSAGE (NEW MESSAGE OR EDIT SUBMIT)
    // ===================================================
    function sendMessage(senderName) {
        const input = document.getElementById('messageInput');
        const text = input.value.trim();

        if (!text) {
            input.focus();
            return;
        }

        // If in EDIT mode
        if (editingMessageId) {
            const targetId = editingMessageId;
            cancelEdit();

            fetch('{{ route("chat.edit") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    message_id: targetId,
                    id: targetId,
                    message: text
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success' && data.message) {
                    updateExistingMessage(data.message);
                }
            })
            .catch(err => console.error('Edit error:', err));
            return;
        }

        // Normal SEND mode
        const currentReply = replyingTo ? { ...replyingTo } : null;
        input.value = '';
        userIsTyping = false;
        autoResizeTextarea(input);
        handleMessageInput(input);
        cancelReply();
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
                sender_name: senderName,
                reply_to_id: currentReply ? currentReply.id : null,
                reply_to_text: currentReply ? currentReply.text : null,
                reply_to_sender: currentReply ? currentReply.sender : null
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success' && data.message) {
                if (data.message.id > lastMessageId) {
                    lastMessageId = data.message.id;
                    const emptyChat = document.getElementById('emptyChat');
                    if (emptyChat) emptyChat.classList.add('d-none');
                    renderMessage(data.message);
                    scrollToBottom(true);
                }
            }
        })
        .catch(err => {
            console.error('Send message error:', err);
            input.value = text;
            handleMessageInput(input);
        });
    }

    function isNearBottom() {
        const container = document.getElementById('chatContainer');
        if (!container) return true;
        return container.scrollHeight - container.scrollTop - container.clientHeight < 150;
    }

    function scrollToBottom(smooth = true) {
        const container = document.getElementById('chatContainer');
        if (!container) return;
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
                lastRenderedDate = '';
                lastRenderedSender = null;
                lastRenderedRowEl = null;
                const emptyChat = document.getElementById('emptyChat');
                if (emptyChat) emptyChat.classList.remove('d-none');
                lastMessageId = 0;
                hidePinnedBanner();
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

    function escapeJs(string) {
        if (!string) return '';
        return String(string)
            .replace(/\\/g, '\\\\')
            .replace(/'/g, "\\'")
            .replace(/"/g, '\\"')
            .replace(/\n/g, '\\n')
            .replace(/\r/g, '\\r');
    }
</script>
@endsection
