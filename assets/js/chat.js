class Chat {
    constructor() {
        this.socket = new WebSocket('wss://seu-servidor-websocket');
        this.chatBox = document.querySelector('.chat-box');
        this.messageInput = document.querySelector('.chat-input');
        
        this.initializeEvents();
    }

    initializeEvents() {
        this.socket.onmessage = (event) => {
            const message = JSON.parse(event.data);
            this.addMessage(message);
        };

        this.messageInput?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.sendMessage();
            }
        });
    }

    sendMessage() {
        const message = {
            text: this.messageInput.value,
            timestamp: new Date(),
            sender: 'client'
        };

        this.socket.send(JSON.stringify(message));
        this.messageInput.value = '';
    }

    addMessage(message) {
        const messageElement = document.createElement('div');
        messageElement.className = `chat-message ${message.sender}`;
        messageElement.innerHTML = `
            <div class="message-content">
                <p>${message.text}</p>
                <span class="timestamp">${new Date(message.timestamp).toLocaleTimeString()}</span>
            </div>
        `;
        
        this.chatBox.appendChild(messageElement);
        this.chatBox.scrollTop = this.chatBox.scrollHeight;
    }
}
