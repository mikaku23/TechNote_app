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
    // helpers: await transitionend or animationend with timeout fallback
    function awaitTransitionEnd(el, timeout = 700) {
        return new Promise((resolve) => {
            let done = false;
            const onEnd = (e) => {
                // ensure we only react to transitions on the element itself
                if (e.target !== el) return;
                if (done) return;
                done = true;
                el.removeEventListener("transitionend", onEnd);
                resolve(true);
            };
            el.addEventListener("transitionend", onEnd);
            // fallback
            setTimeout(() => {
                if (done) return;
                done = true;
                el.removeEventListener("transitionend", onEnd);
                resolve(false);
            }, timeout);
        });
    }

    function awaitAnimationEnd(el, timeout = 500) {
        return new Promise((resolve) => {
            let done = false;
            const onEnd = (e) => {
                if (e.target !== el) return;
                if (done) return;
                done = true;
                el.removeEventListener("animationend", onEnd);
                resolve(true);
            };
            el.addEventListener("animationend", onEnd);
            setTimeout(() => {
                if (done) return;
                done = true;
                el.removeEventListener("animationend", onEnd);
                resolve(false);
            }, timeout);
        });
    }

    /* new openChat: smooth expand (with settle) then show popup */
    /* new openChat: faster settle, no dead time */
    /* openChat: overshoot (stretch) -> quick correct -> show popup instantly with popup-settle */
   async function openChat() {
       floating.style.display = "";
       await new Promise((r) => requestAnimationFrame(r));
       const btnRect = floating.getBoundingClientRect();

       const island = document.createElement("div");
       island.className = "island-transition suction";
       island.style.left = btnRect.left + "px";
       island.style.top = btnRect.top + "px";
       island.style.width = btnRect.width + "px";
       island.style.height = btnRect.height + "px";
       island.style.borderRadius =
           Math.max(btnRect.width, btnRect.height) + "px";
       island.style.opacity = "1";
       document.body.appendChild(island);

       /* mulai nyedot floating */
       floating.classList.add("sucked");

       /* ukur popup */
       chatPopup.style.visibility = "hidden";
       chatPopup.style.display = "block";
       const popupRect = chatPopup.getBoundingClientRect();
       chatPopup.style.display = "";
       chatPopup.style.visibility = "";

       /* OVERSHOOT + STRETCH (inti rasa nyedot) */
       const stretchW = popupRect.width * 1.12;
       const stretchH = popupRect.height * 0.92;

       island.style.transition = [
           "left .32s cubic-bezier(.12,1,.25,1)",
           "top .32s cubic-bezier(.12,1,.25,1)",
           "width .32s cubic-bezier(.12,1,.25,1)",
           "height .32s cubic-bezier(.12,1,.25,1)",
           "border-radius .22s cubic-bezier(.2,.9,.2,1)",
           "transform .32s cubic-bezier(.12,1,.25,1)",
           "box-shadow .32s ease",
       ].join(", ");

       requestAnimationFrame(() => {
           island.style.left =
               popupRect.left - (stretchW - popupRect.width) / 2 + "px";
           island.style.top =
               popupRect.top - (stretchH - popupRect.height) / 2 + "px";
           island.style.width = stretchW + "px";
           island.style.height = stretchH + "px";
           island.style.borderRadius = "18px";
           island.style.transform = "scaleX(.92) scaleY(1.08)";
           island.style.boxShadow = "0 40px 110px rgba(2,6,23,0.48)";
       });

       await awaitTransitionEnd(island, 420);

       /* QUICK SNAP KE UKURAN ASLI (kunci rasa dinamis) */
       island.style.transition = "all .11s cubic-bezier(.3,.9,.2,1)";

       requestAnimationFrame(() => {
           island.style.left = popupRect.left + "px";
           island.style.top = popupRect.top + "px";
           island.style.width = popupRect.width + "px";
           island.style.height = popupRect.height + "px";
           island.style.borderRadius = "14px";
           island.style.transform = "scale(1)";
           island.style.filter = "none";
       });

       await awaitTransitionEnd(island, 160);

       /* buka popup TANPA jeda */
       island.remove();
       floating.style.display = "none";

       chatPopup.classList.add("ready");
       chatPopup.setAttribute("aria-hidden", "false");

       chatPopup.classList.add("popup-settle");
       chatPopup.addEventListener(
           "animationend",
           () => chatPopup.classList.remove("popup-settle"),
           { once: true }
       );

       setTimeout(() => chatInput.focus(), 16);
   }

    /* new closeChat: create island from popup -> shrink to floating (bottom-right) */
    /* new closeChat: quicker collapse, no lag */
    async function closeChat() {
        floating.style.display = "";
        await new Promise((r) => requestAnimationFrame(r));
        const btnRect = floating.getBoundingClientRect();
        const popupRect = chatPopup.getBoundingClientRect();

        const island = document.createElement("div");
        island.className = "island-transition";
        island.style.left = popupRect.left + "px";
        island.style.top = popupRect.top + "px";
        island.style.width = popupRect.width + "px";
        island.style.height = popupRect.height + "px";
        island.style.borderRadius = "14px";
        island.style.boxShadow = "0 24px 60px rgba(2,6,23,0.34)";
        document.body.appendChild(island);

        chatPopup.classList.remove("ready");
        chatPopup.setAttribute("aria-hidden", "true");

        island.style.transition = [
            "left .36s cubic-bezier(.25,.9,.32,1)",
            "top .36s cubic-bezier(.25,.9,.32,1)",
            "width .36s cubic-bezier(.25,.9,.32,1)",
            "height .36s cubic-bezier(.25,.9,.32,1)",
            "border-radius .26s cubic-bezier(.3,.9,.2,1)",
            "box-shadow .36s cubic-bezier(.25,.9,.32,1)",
        ].join(", ");

        requestAnimationFrame(() => {
            island.style.left = btnRect.left + "px";
            island.style.top = btnRect.top + "px";
            island.style.width = btnRect.width + "px";
            island.style.height = btnRect.height + "px";
            island.style.borderRadius =
                Math.max(btnRect.width, btnRect.height) + "px";
            island.style.boxShadow = "0 16px 40px rgba(2,6,23,0.26)";
        });

        await awaitTransitionEnd(island, 200);

        island.classList.add("island-collapse-settle");
        await awaitAnimationEnd(island, 50);

        island.remove();
        floating.classList.remove("sucked");
        floating.style.display = "";
        chatInput.blur();
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
