let dmCurrentConversationId = null;

const dmToggle = document.getElementById("dm-chat-toggle");
const dmPanel = document.getElementById("dm-chat-panel");

dmToggle.addEventListener("click", async () => {
    dmPanel.classList.toggle("show");
    if (dmPanel.classList.contains("show")) {
        await dmFetchContacts();
    }
});

async function dmFetchContacts() {
    const res = await axios.get("/chat/contacts");
    dmRenderContacts(res.data);
}

function dmRenderContacts(list) {
    const box = document.getElementById("dm-chat-contacts");
    box.innerHTML = "";

    list.forEach((item) => {
        const c = item.conversation;
        const el = document.createElement("div");
        el.className = "dm-contact";
        el.innerHTML = `
            <div>${
                item.unread_count
                    ? `<span class="badge">${item.unread_count}</span>`
                    : ""
            }</div>
        `;
        el.onclick = () => dmOpenConversation(c.id);
        box.appendChild(el);
    });
}

async function dmOpenConversation(id) {
    dmCurrentConversationId = id;
    const res = await axios.get("/chat/messages/" + id);
    dmRenderMessages(res.data.messages, res.data.first_unread_id);
    await axios.post("/chat/mark-read/" + id);
}

function dmRenderMessages(messages, firstUnread) {
    const box = document.getElementById("dm-chat-messages");
    box.innerHTML = "";

    messages.forEach((m) => {
        if (firstUnread && m.id === firstUnread) {
            const d = document.createElement("div");
            d.className = "dm-unread-divider";
            d.innerText = "Pesan belum dibaca";
            box.appendChild(d);
        }

       const bubble = document.createElement("div");
       bubble.className =
           "dm-bubble " + (msg.sender_id === DM_USER_ID ? "me" : "other");
       bubble.innerText = msg.body;
        box.appendChild(bubble);
    });

    box.scrollTop = box.scrollHeight;
}

document.getElementById("dm-send-msg").onclick = async () => {
    const input = document.getElementById("dm-chat-text");
    if (!input.value || !dmCurrentConversationId) return;

    await axios.post("/chat/send", {
        conversation_id: dmCurrentConversationId,
        body: input.value,
    });

    input.value = "";
};
