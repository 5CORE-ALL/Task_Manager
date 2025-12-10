@extends('layouts.main') 
@section('page-title') 
    {{ __('chatbot') }} 
@endsection 

@section('page-breadcrumb') 
    {{ __('chatbot') }} 
@endsection 

@push('css') 
    @include('chatbot.chatbot_css'); 
@endpush 

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="chat-wrapper light-mode" id="chat-wrapper">
            <!-- Mobile Header -->
            <div class="mobile-chat-header d-lg-none">
                <button id="mobile-sidebar-toggle" class="btn btn-sm btn-primary">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="mobile-action-buttons">
                    <button class="btn btn-sm btn-primary me-1 mobile-upload-btn" 
                        data-ajax-popup="true"
                        data-size="lg"
                        data-title="{{ __('Trained ChatBot Modal') }}"
                        data-url="{{ route('chatbot.uploadFAQ') }}"
                        data-bs-toggle="tooltip"
                        data-bs-original-title="{{ __('Trained ChatBot Modal') }}">
                        <i class="fa fa-upload"></i>
                    </button>
                    <button class="btn btn-sm btn-secondary me-1 mobile-theme-toggle">
                        <i class="fas fa-moon"></i>
                    </button>
                    <button class="btn btn-sm btn-warning mobile-reset-btn">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>
            </div>

            <div class="chat-body d-flex">
                <!-- Sidebar -->
                <div class="chat-sidebar mobile-sidebar">
                    <div class="d-flex justify-content-between align-items-center pb-4">
                        <h3 class="chat-title mb-0">History</h3>
                        <div class="d-flex align-items-center d-none d-lg-flex">
                            <a class="btn btn-sm btn-primary me-1"
                                data-ajax-popup="true"
                                data-size="lg"
                                data-title="{{ __('Trained ChatBot Modal') }}"
                                data-url="{{ route('chatbot.uploadFAQ') }}"
                                data-bs-toggle="tooltip"
                                data-bs-original-title="{{ __('Trained ChatBot Modal') }}">
                                <i class="fa fa-upload"></i>
                            </a>
                            <button id="theme-toggle" class="btn btn-sm btn-secondary me-1" title="Toggle Theme">
                                <i id="theme-icon" class="fas fa-moon"></i>
                            </button>
                            <button id="reset-btn" class="btn btn-sm btn-warning" title="Reset Chat">
                                <i class="fas fa-edit" id="reset-icon"></i>
                            </button>
                        </div>
                    </div>

                    <ul class="sidebar-list list-group list-group-flush" style="max-height: 50vh; overflow-y: auto;">
                        @foreach ($groupedSessions as $label => $sessions) 
                            @if(count($sessions) > 0)
                            <span class="list-group-item bg-light fw-bold text-uppercase small text-muted text-primary px-3 py-2 border-0">
                                {{ $label }}
                            </span>
                            @foreach ($sessions as $session)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-3 py-2 border-0 hover-bg-light">
                                <a href="{{ route('Ai.chatbot', $session->session) }}" class="d-block text-decoration-none w-100 sidebar-session-link {{ $chatbot_session_id === $session->session ? 'text-primary fw-bold active-session' : '' }}">
                                    {{ \Illuminate\Support\Str::limit($session->last_question ?? 'No question', 40) }}
                                </a>
                            </li>
                            @endforeach 
                            @endif 
                        @endforeach
                    </ul>
                </div>

                <!-- Chat Area -->
                <div class="chat-main">
                    <div class="chat-container" id="chat-container">
                        <div id="chat-loading" style="text-align: center; padding: 10px; display: none;">
                            <em>Loading more messages...</em>
                        </div>
                    </div>
                    <div class="input-container">
                        <button id="scroll-to-bottom-btn" class="scroll-bottom-btn">
                            <i class="fas fa-arrow-down"></i>
                        </button>
                        <textarea id="user-input" rows="3" placeholder="Type your message..."></textarea>
                        <button id="send-btn" title="Send">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                        <div id="user-dropdown" class="user-dropdown"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 

@push('scripts') 
    @include('chatbot.chatbot_js'); 
@endpush

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile sidebar toggle
    const mobileSidebarToggle = document.getElementById('mobile-sidebar-toggle');
    const chatSidebar = document.querySelector('.chat-sidebar');
    const mobileThemeToggle = document.querySelector('.mobile-theme-toggle');
    const mobileResetBtn = document.querySelector('.mobile-reset-btn');
    
    if (mobileSidebarToggle) {
        mobileSidebarToggle.addEventListener('click', function() {
            chatSidebar.classList.toggle('mobile-sidebar-open');
        });
    }
    
    // Sync mobile theme toggle with main theme toggle
    if (mobileThemeToggle) {
        mobileThemeToggle.addEventListener('click', function() {
            document.getElementById('theme-toggle').click();
        });
    }
    
    // Sync mobile reset button with main reset button
    if (mobileResetBtn) {
        mobileResetBtn.addEventListener('click', function() {
            document.getElementById('reset-btn').click();
        });
    }
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        if (window.innerWidth <= 992) {
            const isClickInsideSidebar = chatSidebar.contains(event.target);
            const isClickOnToggle = mobileSidebarToggle.contains(event.target);
            
            if (!isClickInsideSidebar && !isClickOnToggle && chatSidebar.classList.contains('mobile-sidebar-open')) {
                chatSidebar.classList.remove('mobile-sidebar-open');
            }
        }
    });
    
    // Fix for iOS scroll issue
    const chatContainer = document.getElementById('chat-container');
    if (chatContainer) {
        // Add overflow scrolling for iOS
        chatContainer.style.webkitOverflowScrolling = 'touch';
        
        // Force redraw to fix scroll issue
        setTimeout(function() {
            chatContainer.style.height = 'calc(100% - 1px)';
            setTimeout(function() {
                chatContainer.style.height = '100%';
            }, 10);
        }, 100);
    }
    
    // Scroll to bottom initially
    setTimeout(function() {
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    }, 300);
});
</script>