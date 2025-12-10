`
<style>
    /* Base Wrapper */
    .chat-wrapper {
        display: flex;
        flex-direction: column;
        padding: 20px;
        border-radius: 20px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s, color 0.3s;
        height: 70vh;
    }

    .chat-wrapper.light-mode {
        background-color: #ffffff;
        color: #000;
    }

    .chat-wrapper.dark-mode {
        background-color: #121212;
        color: #e0e0e0;
    }

    /* Layout */
    .chat-body {
        display: flex;
        flex: 1;
        overflow: hidden;
    }

    /* Sidebar Base */
    .chat-sidebar {
        width: 250px;
        padding: 15px;
        border-right: 1px solid #ddd;
        border-radius: 16px 0 0 16px;
        flex-shrink: 0;
        background-color: #f9f9f9;
        color: #212529;
    }

    .dark-mode .chat-sidebar {
        background-color: #1e1e1e;
        border-color: #333;
        color: #e0e0e0;
    }

    /* Sidebar Title */
    .chat-title {
        font-size: 18px;
        font-weight: 600;
        color: #000;
    }

    .dark-mode .chat-title {
        color: #fff;
    }

    /* Sidebar List Wrapper */
    .sidebar-list {
        list-style: none;
        padding: 0;
        margin: 0;
        border-radius: 8px;
        max-height: 80vh;
        overflow-y: auto;
        background-color: #f9f9f9;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .dark-mode .sidebar-list {
        background-color: #1e1e1e;
    }

    /* Hide scrollbar in all browsers */
    .sidebar-list::-webkit-scrollbar {
        width: 0;
        height: 0;
    }

    /* Group Label (e.g., Today, Yesterday) */
    .sidebar-list .list-group-item.bg-light {
        background-color: transparent !important;
        color: #6c757d;
        font-weight: 600;
        font-size: 11px;
        text-transform: uppercase;
    }

    .dark-mode .sidebar-list .list-group-item.bg-light {
        color: #a1a1a1;
    }

    /* Session Items */
    .sidebar-list li.list-group-item {
        padding: 6px 12px;
        transition: background-color 0.2s ease;
        border: none;
        background-color: transparent;
        display: flex;
        align-items: center;
    }

    .sidebar-list li.list-group-item:hover {
        background-color: #e9ecef;
    }

    .dark-mode .sidebar-list li.list-group-item:hover {
        background-color: #2a2a2a;
    }

    /* Sidebar Links */
    .sidebar-list a {
        display: block;
        width: 100%;
        color: #212529;
        text-decoration: none;
        padding: 4px 0;
        font-size: 14px;
        transition: color 0.2s ease;
    }

    .dark-mode .sidebar-list a {
        color: #e0e0e0;
    }

    /* Active Session Highlight */
    .sidebar-list a.fw-bold.text-primary {
        background-color: #e3f2fd;
        border-radius: 6px;
        padding: 6px 10px;
        color: #0d6efd;
    }

    .dark-mode .sidebar-list a.fw-bold.text-primary {
        background-color: #2c2c2e;
        color: #0d6efd;
    }

    /* Sidebar Icons */
    .sidebar-list a i {
        margin-right: 6px;
    }

    .dark-mode .sidebar-list a i {
        color: #bbb;
    }

    /* Button Tooltips & Icons (Optional Improvements) */
    #theme-toggle,
    #reset-btn {
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .dark-mode #theme-toggle,
    .dark-mode #reset-btn {
        color: #e0e0e0;
        background-color: #2c2c2e;
    }

    /* Main Chat Area */
    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
        padding-left: 15px;
    }

    /* Chat Container */
    .chat-container {
        flex: 1;
        padding: 15px;
        border-radius: 16px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        border: none !important;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .light-mode .chat-container {
        background: #f7f7f8;
    }

    .dark-mode .chat-container {
        background: #1e1e1e;
    }

    /* Messages */
    .message-wrapper {
        display: flex;
        align-items: flex-start;
        margin-bottom: 10px;
    }

    .bot-avatar {
        width: 36px;
        height: 36px;
        margin-right: 10px;
        border-radius: 50%;
        background-color: #eee;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: #007bff;
    }

    .dark-mode .bot-avatar {
        background-color: #2c2c2e;
        color: #0d6efd;
    }

    .message {
        max-width: 70%;
        padding: 10px 15px;
        border-radius: 18px;
        line-height: 1.5;
        word-wrap: break-word;
    }

    .user {
        align-self: flex-end;
        background-color: #007bff;
        color: white;
        margin-left: auto;
    }

    .dark-mode .user {
        background-color: #0d6efd;
    }

    .bot {
        background-color: #e5e5ea;
        color: black;
    }

    .dark-mode .bot {
        background-color: #2c2c2e;
        color: #e0e0e0;
    }

    .message-wrapper.user-message .bot-avatar {
        order: 2;
        margin-left: 6px;
        margin-right: 3px;
    }

    /* Input Area */
    .input-container {
        display: flex;
        align-items: center;
        padding: 8px;
        background-color: #fff;
        border: none;
        position: relative;
    }

    .dark-mode .input-container {
        background-color: #1a1a1a;
        border: none;
    }

    .input-container textarea {
        width: 100%;
        resize: none;
        padding: 10px 40px 10px 12px;
        border-radius: 20px;
        border: 1px solid #ccc;
        font-size: 14px;
        min-height: 40px;
        max-height: 150px;
        overflow-y: auto;
        box-sizing: border-box;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .dark-mode .input-container textarea {
        background-color: #2c2c2e;
        color: #fff;
        border-color: #555;
    }

    .input-container button {
        position: absolute;
        right: 20px;
        background: none;
        border: none;
        color: #007bff;
        font-size: 18px;
        cursor: pointer;
        outline: none;
    }

    .input-container button:hover {
        color: #0056b3;
    }

    /* Spinner */
    .spinner {
        width: 24px;
        height: 24px;
        border: 4px solid rgba(0, 0, 0, 0.1);
        border-left-color: #007bff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Responsive Fix */
    @media (max-width: 768px) {
        .chat-body {
            flex-direction: column;
        }

        .chat-sidebar {
            width: 100%;
            border-right: none;
            border-bottom: 1px solid #ddd;
            border-radius: 16px 16px 0 0;
        }

        .chat-main {
            padding-left: 0;
        }

        .chat-container {
            min-height: 300px;
        }
    }

    .chat-title {
        font-size: 18px;
        font-weight: 600;
        color: #000;
    }

    .dark-mode .chat-title {
        color: #fff;
    }
</style>
<style>
    /* Dropdown container */
  .user-dropdown {
    display: none; /* Hide by default */
    position: absolute;
    bottom: 100px;
    left: 0;
    width: 100%;
    background: white;
    border: 1px solid #ccc;
    border-radius: 6px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    max-height: 250px;
    overflow-y: auto;
    padding-bottom: 40px;
    z-index: 1000;
}


    /* Individual user item */
    .dropdown-item {
        display: flex;
        align-items: center;
        padding: 10px;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .dropdown-item:hover {
        background-color: #f0f0f0;
    }

    .dropdown-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 10px;
    }

    .dropdown-info .name {
        font-weight: bold;
        font-size: 14px;
        color: #333;
    }

    .dropdown-info .email {
        font-size: 12px;
        color: #777;
    }
    .typing-indicator {
    display: flex;
    align-items: center;
    padding: 10px 14px;
    border-radius: 12px;
    background: #f1f2f6;
    max-width: fit-content;
    font-size: 16px;
    color: #555;
}

.typing-dots {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 20px;
    gap: 5px;
}

.typing-dots span {
    width: 6px;
    height: 6px;
    background-color: #aaa;
    border-radius: 50%;
    animation: typing 1.2s infinite;
}

.typing-dots span:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-dots span:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0% { transform: translateY(0); opacity: 0.3; }
    50% { transform: translateY(-5px); opacity: 1; }
    100% { transform: translateY(0); opacity: 0.3; }
}
.message-icon {
    background: none;
    border: none;
    cursor: pointer;
    color: #aaa;
    margin-left: 8px;
}

.message-icon:hover {
    color: #333;
}

</style>

<style>


.chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
}

.chat-container {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
    background: #f9f9f9;
    position: relative;
    -webkit-overflow-scrolling: touch;
}


    /* Layout */
    .chat-body {
        display: flex;
        overflow: hidden;
        position: relative;
    }

    /* Sidebar Styles */
    .chat-sidebar {
        width: 250px;
        padding: 15px;
        border-right: 1px solid #ddd;
        border-radius: 16px 0 0 16px;
        flex-shrink: 0;
        background-color: #f9f9f9;
        color: #212529;
        transition: transform 0.3s ease;
        z-index: 100;
    }

    .dark-mode .chat-sidebar {
        background-color: #1e1e1e;
        border-color: #333;
        color: #e0e0e0;
    }

    /* Mobile Header */
    .mobile-chat-header {
        display: none;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        background-color: #f9f9f9;
        border-bottom: 1px solid #ddd;
    }

    .dark-mode .mobile-chat-header {
        background-color: #1e1e1e;
        border-color: #333;
    }

    .mobile-action-buttons {
        display: flex;
    }

    /* Mobile-specific styles */
    @media (max-width: 992px) {
        .mobile-chat-header {
            display: flex;
        }
        
        .chat-sidebar {
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            transform: translateX(-100%);
            z-index: 1000;
        }
        
        .chat-sidebar.mobile-sidebar-open {
            transform: translateX(0);
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.1);
        }
        
        .chat-main {
            padding-left: 0;
        }
        
        .chat-wrapper {
            height: 80vh;
            padding: 0;
        }
        
        .chat-container {
            height: calc(100% - 120px);
        }
        
        /* Fix for iOS scrolling */
        .chat-container {
            -webkit-overflow-scrolling: touch;
            overflow-y: scroll;
        }
    }

   
</style>