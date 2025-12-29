(function () {
    const toggleBtn = document.getElementById("chat-toggle");
    const chatPopup = document.getElementById("chat-popup");
    const closeBtn = document.getElementById("chat-close");
    const chatForm = document.getElementById("chat-form");
    const chatInput = document.getElementById("chat-input");
    const chatMessages = document.getElementById("chat-messages");
    const sendBtn = document.getElementById("chat-send");
    const floating = document.getElementById("floating-chat");

    // initial welcome
    function initialBotIntro() {
        appendBotMessage(
            "Halo — chat ini khusus untuk bantuan aplikasi layanan teknisi. Ketik pertanyaan tentang penginstalan, perbaikan, rekap, atau contact."
        );
    }

    // open chat: morph effect (hide floating, show popup)
    function openChat() {
        // hide floating button with scale/opacity
        floating.style.transition = "transform .28s ease, opacity .28s ease";
        floating.style.transform = "scale(.92)";
        floating.style.opacity = "0";
        setTimeout(() => {
            floating.style.display = "none";
        }, 300);

        chatPopup.setAttribute("aria-hidden", "false");
        chatPopup.classList.add("open");
        setTimeout(() => chatInput.focus(), 220);
    }

    // close chat: show floating again
    function closeChat() {
        chatPopup.classList.remove("open");
        chatPopup.setAttribute("aria-hidden", "true");
        // reveal floating
        floating.style.display = "block";
        // small delay for visibility then animate
        requestAnimationFrame(() => {
            floating.style.opacity = "0";
            floating.style.transform = "scale(.92)";
            setTimeout(() => {
                floating.style.transition =
                    "transform .28s ease, opacity .28s ease";
                floating.style.transform = "";
                floating.style.opacity = "";
            }, 20);
        });
    }

    // append user message
    function appendUserMessage(text) {
        const d = document.createElement("div");
        d.className = "msg user enter";
        d.textContent = text;
        chatMessages.appendChild(d);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        // ensure transition triggers
        requestAnimationFrame(() => d.classList.add("enter"));
    }

    // append bot message with entrance animation
    function appendBotMessage(text) {
        const d = document.createElement("div");
        d.className = "msg bot";
        d.textContent = text;
        chatMessages.appendChild(d);
        // small timeout so CSS transition animates
        requestAnimationFrame(() => d.classList.add("enter"));
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // create typing indicator element and append; return element
    function createTypingIndicator() {
        const wrapper = document.createElement("div");
        wrapper.className = "typing-wrapper";
        const t = document.createElement("div");
        t.className = "typing";
        t.setAttribute("aria-hidden", "true");
        const dots = document.createElement("span");
        dots.className = "dots";
        for (let i = 0; i < 3; i++) {
            const dot = document.createElement("span");
            dot.className = "dot";
            dots.appendChild(dot);
        }
        t.appendChild(dots);
        wrapper.appendChild(t);
        chatMessages.appendChild(wrapper);
        // animate in
        requestAnimationFrame(() => t.classList.add("show"));
        chatMessages.scrollTop = chatMessages.scrollHeight;
        return t; // return the inner typing node for fade-out control
    }

    // remove typing indicator with fade-out animation
    function removeTypingIndicator(typingNode) {
        return new Promise((res) => {
            if (!typingNode) return res();
            typingNode.classList.add("fade-out");
            // wait transition
            setTimeout(() => {
                // remove wrapper (parent)
                const parent = typingNode.parentElement;
                if (parent) parent.remove();
                res();
            }, 280);
        });
    }

    // send message routine: append user msg, show typing indicator, fetch, then replace typing with bot reply
    async function sendMessage() {
        const text = chatInput.value.trim();
        if (!text) return;
        appendUserMessage(text);
        chatInput.value = "";
        sendBtn.disabled = true; // prevent double click but do not change text
        // create typing indicator
        const typingNode = createTypingIndicator();

        try {
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            const token = tokenMeta ? tokenMeta.getAttribute("content") : "";

            const res = await fetch("/chatbot", {
                method: "POST",
                credentials: "same-origin",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": token || "",
                    Accept: "application/json",
                },
                body: JSON.stringify({ message: text }),
            });

            if (!res.ok) {
                let serverText = "";
                try {
                    const json = await res.json();
                    serverText =
                        json.message ?? json.reply ?? JSON.stringify(json);
                } catch (e) {
                    serverText = await res.text();
                }
                await removeTypingIndicator(typingNode);
                appendBotMessage(
                    `Maaf, gagal menghubungi server (status ${res.status}). ${serverText}`
                );
            } else {
                const data = await res.json();
                const reply =
                    data.reply || "Maaf, terjadi kesalahan pada server.";
                // simulate small delay to make animation natural (optional)
                // await new Promise(r => setTimeout(r, 250));
                await removeTypingIndicator(typingNode);
                appendBotMessage(reply);
            }
        } catch (err) {
            await removeTypingIndicator(typingNode);
            appendBotMessage(
                "Terjadi kesalahan jaringan. Cek konsol browser untuk detail."
            );
            console.error("Chat send error:", err);
        } finally {
            sendBtn.disabled = false;
        }
    }

    // events
    toggleBtn.addEventListener("click", () => {
        // if popup closed -> open
        const isOpen = chatPopup.classList.contains("open");
        if (!isOpen) openChat();
        else closeChat();
    });
    closeBtn.addEventListener("click", closeChat);

    sendBtn.addEventListener("click", sendMessage);
    chatForm.addEventListener("submit", (e) => {
        e.preventDefault();
        sendMessage();
    });
    chatInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // first load welcome (only after DOM ready)
    initialBotIntro();
})();
