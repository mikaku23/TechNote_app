@php
$currentUser = auth()->user();
@endphp

@if($currentUser)
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">

<div id="dm-chat-root">
    <button id="dm-chat-toggle" class="dm-chat-fab">
        💬
        <span id="dm-chat-unread-badge" style="display:none;"></span>
    </button>

    <div id="dm-chat-panel">
        <div class="dm-chat-layout">

            <!-- CONTACT LIST -->
            <div class="dm-chat-contacts">
                <div class="dm-contacts-header">Chat</div>
                <div id="dm-chat-contacts-list">
                    <!-- kontak render via JS -->
                </div>
            </div>

            <!-- CHAT AREA -->
            <div class="dm-chat-area">
                <div class="dm-chat-header">
                    <div class="name" id="dm-contact-name">Admin</div>
                    <div class="status" id="dm-contact-status">offline</div>
                </div>

                <div id="dm-chat-messages" class="dm-chat-messages">
                    <!-- bubble -->
                </div>

                <div class="dm-chat-input">
                    <input id="dm-chat-text" type="text" placeholder="Tulis pesan..." />
                    <button id="dm-send-msg">➤</button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    window.DM_USER_ID = {
        {
            $currentUser - > id
        }
    };
    window.DM_USER_ROLE = "{{ $currentUser->role?->status ?? 'user' }}";
</script>

<script src="{{ asset('js/chat.js') }}"></script>
@endif