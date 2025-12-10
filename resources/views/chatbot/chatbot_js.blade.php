send-btn
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // DOM Elements
        let mentionedUsers = [];
        let users = [];

        const chatContainer = document.getElementById("chat-container");
        const inputField = document.getElementById("user-input");
        const sendBtn = document.getElementById("send-btn");
        const themeToggle = document.getElementById("theme-toggle");
        const chatWrapper = document.getElementById("chat-wrapper");
        const themeIcon = document.getElementById("theme-icon");
        const dropdown = document.getElementById("user-dropdown");

        const userAvatar = "{{ check_file(Auth::user()->avatar) ? get_file(Auth::user()->avatar) : asset('default-avatar.png') }}";
        let isSending = false; // To prevent multiple sends

        // Add message to chat
        function addMessage(text, className, formatted = false) {
            const wrapper = document.createElement("div");
            wrapper.className = "message-wrapper";
            if (className === "user") {
                wrapper.classList.add("user-message");
            }

            // Avatar
            const avatar = document.createElement("div");
            avatar.className = "bot-avatar";
            avatar.innerHTML = className === "user"
                ? `<img src="${userAvatar}" alt="User Avatar" style="width: 100%; height: 100%; border-radius: 50%;">`
                : '<i class="fas fa-robot"></i>';

            // Message bubble
            const message = document.createElement("div");
            message.className = `message ${className}`;
            message.dataset.originalText = text;

            const content = document.createElement("div");
            content.className = "message-text";

            // Apply custom markdown-to-HTML parsing (bold **TEXT**)
            const processedText = formatted ? parseBotResponse(text) : escapeHtml(text);
            content.innerHTML = processedText;

            message.appendChild(content);

            // Action buttons (for user messages)
            let actions = null;
            if (className === "user") {
                actions = document.createElement("div");
                actions.className = "message-actions";

                const editBtn = document.createElement("button");
                editBtn.className = "message-icon";
                editBtn.title = "Edit";
                editBtn.innerHTML = `<i class="fas fa-edit"></i>`;
                editBtn.onclick = () => {
                    inputField.value = message.dataset.originalText;
                    inputField.focus();
                    inputField.setSelectionRange(inputField.value.length, inputField.value.length);
                };

                const copyBtn = document.createElement("button");
                copyBtn.className = "message-icon";
                copyBtn.title = "Copy";
                copyBtn.innerHTML = `<i class="fas fa-copy"></i>`;
                copyBtn.onclick = () => {
                    navigator.clipboard
                        .writeText(message.dataset.originalText)
                        .then(() => toastrs("Copied", "Message copied to clipboard!", "success"))
                        .catch(() => toastrs("Error", "Failed to copy message", "error"));
                };

                actions.appendChild(editBtn);
                actions.appendChild(copyBtn);
            }

            // Order of elements
            if (className === "user") {
                wrapper.appendChild(message);
                if (actions) wrapper.appendChild(actions);
                wrapper.appendChild(avatar);
            } else {
                wrapper.appendChild(avatar);
                wrapper.appendChild(message);
            }

            chatContainer.appendChild(wrapper);
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        // ⬇️ Helper to convert **TEXT** to <strong>TEXT</strong>
        function processBoldSyntax(input) {
            return input.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
        }


        // Escape HTML to prevent injection
        function escapeHtml(text) {
            const div = document.createElement("div");
            div.innerText = text;
            return div.innerHTML;
        }

        // Format bot response
        function parseBotResponse(text) {
            // Convert **bold** to <strong>bold</strong>
            const applyBold = (str) => str.replace(/\*\*(.+?)\*\*/g, "<strong>$1</strong>");

            if (/^\s*[\d\-\*\.]/m.test(text)) {
                return (
                    text
                        .split("\n")
                        .filter((line) => line.trim())
                        .map((line) => {
                            const content = applyBold(line.replace(/^\d+\.?\s*|\-\s*|\*\s*/, ""));
                            return content ? `<li>${content}</li>` : "";
                        })
                        .join("")
                        .replace(/^(<li>.*<\/li>)+$/gm, "<ul>$&</ul>")
                );
            }

            // For plain text responses
            return applyBold(text).replace(/\n/g, "<br>");
        }

        // Send message to backend
        async function sendMessage() {
            const text = inputField.value.trim();
            if (!text || isSending) return;

            isSending = true;
            addMessage(text, "user");
            inputField.value = "";
            inputField.style.height = "auto";

            // Show typing indicator
            const spinnerWrapper = document.createElement("div");
            spinnerWrapper.className = "message-wrapper";
            spinnerWrapper.innerHTML = `
    <div class="bot-avatar">🤖</div>
    <div class="message bot typing-indicator">
        <div class="typing-dots"><span></span><span></span><span></span></div>
    </div>`;
            chatContainer.appendChild(spinnerWrapper);

            chatContainer.scrollTop = chatContainer.scrollHeight;

            // Prepare payload with mentions
            const payload = {
                message: text,
                mentions: mentionedUsers.map((user) => ({
                    name: user.name,
                    email: user.email,
                })),
            };
            // Check if @ is used but no valid mentions are available
            if (text.includes("@") && payload.mentions.length === 0) {
                spinnerWrapper.remove();
                isSending = false; // Don't forget to reset this flag
                toastrs("Error", "You mentioned someone, but no valid users were detected. Please use proper mentions.", "error");
                return;
            }

            try {
                const response = await fetch("{{ route('chatbot.send') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    body: JSON.stringify(payload),
                });

                const data = await response.json();
                spinnerWrapper.remove();

                if (data.reply) {
                    addMessage(data.reply, "bot", true);
                } else {
                    addMessage("⚠️ No response received from the server.", "bot");
                }
            } catch (error) {
                console.error("Chatbot Error:", error);
                spinnerWrapper.remove();
                addMessage("I couldn't process your question properly. Please try again.", "bot");
            } finally {
                isSending = false;
                mentionedUsers = []; // Reset mentions after send
            }
        }

        // Auto-grow textarea height
        inputField.addEventListener("input", function () {
            this.style.height = "auto";
            this.style.height = Math.min(this.scrollHeight, 200) + "px";
        });

        // Send message on button click
        sendBtn.addEventListener("click", sendMessage);

        // Improved Enter key handling
        inputField.addEventListener("keydown", function (e) {
            if (e.key === "Enter" && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // Theme toggling
        function toggleTheme() {
            const isDark = !chatWrapper.classList.contains("dark-mode");
            chatWrapper.classList.toggle("dark-mode", isDark);
            chatWrapper.classList.toggle("light-mode", !isDark);
            localStorage.setItem("chat-theme", isDark ? "dark" : "light");
            themeIcon.className = isDark ? "fas fa-sun" : "fas fa-moon";
        }

        themeToggle.addEventListener("click", toggleTheme);

        // Show welcome messages
        function showTypingThenMessages(messages, index = 0) {
            if (index >= messages.length) return;

            const spinnerWrapper = document.createElement("div");
            spinnerWrapper.className = "message-wrapper";
            spinnerWrapper.innerHTML = `
                <div class="bot-avatar"><i class="fas fa-robot"></i></div>
                <div class="message bot typing-indicator">
                <div class="bot-avatar">🤖</div><div class="typing-dots"><span></span><span></span><span></span></div></div>`;
            chatContainer.appendChild(spinnerWrapper);
            chatContainer.scrollTop = chatContainer.scrollHeight;

            setTimeout(() => {
                spinnerWrapper.remove();
                addMessage(messages[index], "bot", true);
                showTypingThenMessages(messages, index + 1);
            }, 1300);
        }

        // Handle user mentions
        function setupMentionHandling() {
            // Fetch user list on load
            fetch("{{ route('users.list') }}")
                .then((response) => response.json())
                .then((data) => {
                    users = data.map((user) => ({
                        ...user,
                        avatar: user.avatar || userAvatar,
                    }));
                });

            // Handle input in the textarea
            inputField.addEventListener("input", function () {
                const cursorPosition = inputField.selectionStart;
                const textBeforeCursor = inputField.value.slice(0, cursorPosition);

                const match = textBeforeCursor.match(/@(\w*)$/i);

                if (match) {
                    const keyword = match[1].toLowerCase();
                    const matchedUsers = users.filter((user) => user.name.toLowerCase().includes(keyword));

                    if (matchedUsers.length > 0) {
                        dropdown.innerHTML = matchedUsers
                            .map(
                                (user) => `
                            <div class="dropdown-item" data-name="${user.name}" data-email="${user.email}">
                                <img src="${user.avatar}" class="dropdown-avatar" alt="${user.name}">
                                <div class="dropdown-info">
                                    <div class="name">${user.name}</div>
                                    <div class="email">${user.email}</div>
                                </div>
                            </div>
                        `
                            )
                            .join("");

                        dropdown.style.display = "block";
                    } else {
                        dropdown.style.display = "none";
                    }
                } else {
                    dropdown.style.display = "none";
                }
            });

            // Handle mention selection
            dropdown.addEventListener("click", function (e) {
                const item = e.target.closest(".dropdown-item");
                if (item) {
                    const selectedName = item.dataset.name;
                    const selectedEmail = item.dataset.email;

                    const cursorPosition = inputField.selectionStart;
                    const textBefore = inputField.value.slice(0, cursorPosition);
                    const textAfter = inputField.value.slice(cursorPosition);

                    const newTextBefore = textBefore.replace(/@(\w*)$/, "@" + selectedName + " ");
                    inputField.value = newTextBefore + textAfter;

                    dropdown.style.display = "none";
                    inputField.focus();
                    inputField.setSelectionRange(newTextBefore.length, newTextBefore.length);

                    // Add to mentioned users if not already there
                    if (!mentionedUsers.find((user) => user.email === selectedEmail)) {
                        mentionedUsers.push({ name: selectedName, email: selectedEmail });
                    }
                }
            });

            // Hide dropdown when clicking outside
            document.addEventListener("click", function (e) {
                if (!dropdown.contains(e.target) && e.target !== inputField) {
                    dropdown.style.display = "none";
                }
            });
        }

        // Initialize
        function init() {
            const savedTheme = localStorage.getItem("chat-theme") || "light";
            chatWrapper.classList.add(savedTheme + "-mode");
            themeIcon.className = savedTheme === "dark" ? "fas fa-sun" : "fas fa-moon";

            if (!sessionStorage.getItem("welcomeShown")) {
                const welcomeMessages = ["Hello 👋", "Welcome to 5 Core, {{ addslashes(Auth::user()->name) }}!", "How can I assist you today?"];
                showTypingThenMessages(welcomeMessages);
                sessionStorage.setItem("welcomeShown", "true");
            }

            setupMentionHandling();
        }

        init();
    });
</script>

<script>
    document.getElementById("reset-btn").addEventListener("click", function () {
        const resetIcon = document.getElementById("reset-icon");

        // Change icon to spinner
        resetIcon.classList.remove("fa-edit");
        resetIcon.classList.add("fa-spinner", "fa-spin");

        fetch("{{ route('chatbot.clear') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.status === "cleared") {
                    // Redirect to chatbot route after short delay (optional)
                    window.location.href = "{{ route('chatbot') }}";
                } else {
                    resetIcon.classList.remove("fa-spinner", "fa-spin");
                    resetIcon.classList.add("fa-edit");
                }
            })
            .catch((error) => {
                console.error("Error clearing session:", error);
                // Revert icon on error
                resetIcon.classList.remove("fa-spinner", "fa-spin");
                resetIcon.classList.add("fa-edit");
            });
    });
</script>

<script>
    function scrollToBottom() {
        const chatContainer = document.getElementById("chat-container");
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    }

    // Scroll to bottom on page load
    window.addEventListener("load", scrollToBottom);

    // Optional: Scroll when sending a new message dynamically
    document.getElementById("send-btn").addEventListener("click", () => {
        setTimeout(scrollToBottom, 100);
    });
</script>

<script>
    let loading = false;
    let page = 1;
    let allLoaded = false;

    const chatContainer = document.getElementById("chat-container");
    const loadingIndicator = document.getElementById("chat-loading");

    async function loadChats() {
        if (loading || allLoaded) return;

        loadingIndicator.style.display = "block";
        loading = true;

        const previousHeight = chatContainer.scrollHeight;

        try {
            const response = await fetch(`/load-more-chats?page=${page}`);
            const result = await response.json();

            if (!result.html.trim()) {
                allLoaded = true;
            } else {
                const tempDiv = document.createElement("div");
                tempDiv.innerHTML = result.html;

                Array.from(tempDiv.children).forEach((el) => {
                    chatContainer.prepend(el);
                });

                chatContainer.scrollTop = chatContainer.scrollHeight - previousHeight;
            }

            if (!result.hasMore) {
                allLoaded = true;
            }
        } catch (error) {
            console.error("Failed to load messages:", error);
        }

        loadingIndicator.style.display = "none";
        loading = false;
    }

    // Load first page on load
    window.addEventListener("DOMContentLoaded", () => {
        loadChats();
    });

    // Infinite scroll up
    chatContainer.addEventListener("scroll", () => {
        if (chatContainer.scrollTop === 0 && !loading && !allLoaded) {
            page++;
            loadChats();
        }
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const chatContainer = document.getElementById("chat-container");
        const scrollBtn = document.getElementById("scroll-to-bottom-btn");

        // Show/hide scroll-to-bottom button on scroll
        chatContainer.addEventListener("scroll", () => {
            const distanceFromBottom = chatContainer.scrollHeight - chatContainer.scrollTop - chatContainer.clientHeight;
            const threshold = 50; // Adjust as needed

            if (distanceFromBottom > threshold) {
                scrollBtn.style.display = "block";
            } else {
                scrollBtn.style.display = "none";
            }
        });

        // Scroll to bottom smoothly
        scrollBtn.addEventListener("click", () => {
            chatContainer.scrollTo({
                top: chatContainer.scrollHeight,
                behavior: "smooth",
            });
            scrollBtn.style.display = "none";
        });

        // Scroll to bottom on page load
        chatContainer.scrollTop = chatContainer.scrollHeight;
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.addEventListener("click", function (e) {
            const inputField = document.getElementById("user-input");

            // Strip HTML utility
            function stripHTML(html) {
                const temp = document.createElement("div");
                temp.innerHTML = html;
                return temp.textContent || temp.innerText || "";
            }

            // Edit button handler
            if (e.target.closest(".edit-btn")) {
                const btn = e.target.closest(".edit-btn");
                const rawMessage = btn.dataset.message;
                const cleanMessage = stripHTML(rawMessage);
                if (inputField) {
                    inputField.value = cleanMessage;
                    inputField.focus();
                    inputField.setSelectionRange(cleanMessage.length, cleanMessage.length);
                } else {
                    console.warn("Input field not found.");
                }
            }

            // Copy button handler
            if (e.target.closest(".copy-btn")) {
                const btn = e.target.closest(".copy-btn");
                const rawMessage = btn.dataset.message;
                const cleanMessage = stripHTML(rawMessage);
                navigator.clipboard
                    .writeText(cleanMessage)
                    .then(() => toastrs("Success", "Message copied to clipboard!", "Success"))
                    .catch(() => alert("Failed to copy."));
            }
        });
    });

    function stripHTML(html) {
        const temp = document.createElement("div");
        temp.innerHTML = html;
        return temp.textContent || temp.innerText || "";
    }
</script>