<link rel="stylesheet" href="/sistema/assets/css/chatbot.css">

<div class="chat-button" id="chatButton">
    <i class="fas fa-comment-dots"></i>
</div>

<div class="chat-widget" id="chatbot">
    <div class="chat-header">
        <h2>
            <i class="fas fa-robot"></i>
            TechHelp Bot
        </h2>
        <div class="actions">
            <button id="chatClose" title="Fechar chat">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    
    <div class="chat-box" id="chatMessages">
        <div class="chat-message admin">
            <img src="/sistema/assets/images/bot-avatar.png" alt="TechHelp Bot" class="avatar">
            <div class="message-content">
                <p>Olá, eu sou o assistente virtual da TechHelp!</p>
            </div>
        </div>
        <div class="chat-message admin">
            <img src="/sistema/assets/images/bot-avatar.png" alt="TechHelp Bot" class="avatar">
            <div class="message-content">
                <p>Escolha uma opção ou digite sua dúvida:</p>
                <div class="chat-options">
                    <button class="option-button" data-message="Informações sobre serviços e preços">
                        <i class="fas fa-info-circle"></i>
                        <span>Informações sobre Serviços</span>
                    </button>
                    <button class="option-button" data-message="Qual o horário de atendimento?">
                        <i class="fas fa-clock"></i>
                        <span>Horários de Atendimento</span>
                    </button>
                    <button class="option-button" data-message="Quais as formas de pagamento?">
                        <i class="fas fa-credit-card"></i>
                        <span>Formas de Pagamento</span>
                    </button>
                    <button class="option-button" data-message="Qual a garantia dos serviços?">
                        <i class="fas fa-shield-alt"></i>
                        <span>Garantia dos Serviços</span>
                    </button>
                    <button class="option-button" data-message="Gostaria de falar sobre outro assunto">
                        <i class="fas fa-comments"></i>
                        <span>Outros Assuntos</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="chat-footer">
        <input type="text" 
               id="userInput" 
               placeholder="Digite sua mensagem..." 
               autocomplete="off"
               maxlength="500"
               aria-label="Digite sua mensagem">
        <button class="send-button" id="sendMessage" aria-label="Enviar mensagem">
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const widget = document.getElementById('chatbot');
    const button = document.getElementById('chatButton');
    const closeBtn = document.getElementById('chatClose');
    const sendBtn = document.getElementById('sendMessage');
    const chatBox = document.getElementById('chatMessages');
    const userInput = document.getElementById('userInput');
    const header = document.querySelector('.chat-header');

    // Botão para limpar histórico (move this up)
    const clearBtn = document.createElement('button');
    clearBtn.className = 'clear-history';
    clearBtn.innerHTML = '<i class="fas fa-trash"></i>';
    clearBtn.title = 'Limpar histórico';
    
    // Modificar função de limpar histórico
    clearBtn.onclick = () => {
        if (confirm('Deseja limpar o histórico de mensagens?')) {
            localStorage.removeItem('chatMessages');
            loadSavedMessages(); // Isso vai recarregar as mensagens iniciais
        }
    };

    // Adicionar botão ao container de ações
    document.querySelector('.chat-header .actions').insertBefore(clearBtn, document.querySelector('#chatClose'));

    // Simplificar toggle do chat
    function toggleChat(show) {
        if (show) {
            widget.classList.add('active');
            button.classList.add('hidden');
            userInput.focus();
        } else {
            widget.classList.remove('active');
            button.classList.remove('hidden');
        }
    }

    // Event Listeners
    button?.addEventListener('click', () => toggleChat(true));
    closeBtn?.addEventListener('click', () => toggleChat(false));

    // Toggle chat on header click
    header?.addEventListener('click', toggleChat);
    closeBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        e.preventDefault();
        toggleChat();
    });

    // Melhoria no envio de mensagens
    function sendMessage(message) {
        if (!message.trim()) return;
        
        addMessage(message, 'client');
        
        fetch('/sistema/chatbot/processar.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'mensagem=' + encodeURIComponent(message)
        })
        .then(response => response.json())
        .then(data => {
            if (data && data.resposta) {
                setTimeout(() => {
                    addMessage(data.resposta, 'admin');
                }, 500);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            addMessage('Desculpe, tive um problema ao processar sua mensagem. Tente novamente.', 'admin');
        });
    }

    // Adicionar evento de clique ao botão de enviar
    sendBtn?.addEventListener('click', () => {
        const message = userInput.value.trim();
        if (message) {
            sendMessage(message);
            userInput.value = '';
        }
    });

    // Event listeners
    userInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && this.value.trim()) {
            const message = this.value.trim();
            sendMessage(message);
            this.value = '';
        }
    });

    document.querySelectorAll('.option-button').forEach(button => {
        button.addEventListener('click', function() {
            sendMessage(this.dataset.message);
        });
    });

    // Carregar mensagens salvas
    function loadSavedMessages() {
        const savedMessages = JSON.parse(localStorage.getItem('chatMessages') || '[]');
        
        if (savedMessages.length === 0) {
            // Se não tem mensagens salvas, inicializa com as mensagens padrão
            const initialMessages = [
                {
                    text: 'Olá, eu sou o assistente virtual da TechHelp!',
                    type: 'admin',
                    timestamp: new Date().toLocaleTimeString().slice(0,5)
                },
                {
                    text: getBotInitialOptions(),
                    type: 'admin',
                    timestamp: new Date().toLocaleTimeString().slice(0,5)
                }
            ];
            localStorage.setItem('chatMessages', JSON.stringify(initialMessages));
            return loadSavedMessages(); // Chama recursivamente com as novas mensagens
        }

        chatBox.innerHTML = ''; // Limpa mensagens existentes
        
        savedMessages.forEach(msg => {
            const div = document.createElement('div');
            div.className = `chat-message ${msg.type}`;
            
            let html = '';
            if (msg.type === 'admin') {
                html += '<img src="/sistema/assets/images/bot-avatar.png" alt="TechHelp Bot" class="avatar">';
            }
            
            html += `
                <div class="message-content">
                    ${msg.text}
                </div>
                <span class="timestamp">${msg.timestamp}</span>
            `;
            
            div.innerHTML = html;
            chatBox.appendChild(div);
        });
        
        chatBox.scrollTop = chatBox.scrollHeight;
        initializeOptionButtons(); // Reinicializa os botões após carregar as mensagens
    }

    // Salvar mensagens no localStorage
    function saveMessage(text, type, timestamp) {
        const savedMessages = JSON.parse(localStorage.getItem('chatMessages') || '[]');
        savedMessages.push({ text, type, timestamp });
        localStorage.setItem('chatMessages', JSON.stringify(savedMessages));
    }

    // Modificar função addMessage para salvar
    function addMessage(text, type) {
        const div = document.createElement('div');
        div.className = `chat-message ${type}`;
        const timestamp = new Date().toLocaleTimeString().slice(0,5);
        
        let html = '';
        if (type === 'admin') {
            html += '<img src="/sistema/assets/images/bot-avatar.png" alt="TechHelp Bot" class="avatar">';
        }
        
        html += `
            <div class="message-content">
                ${text}
            </div>
            <span class="timestamp">${timestamp}</span>
        `;
        
        div.innerHTML = html;
        chatBox.appendChild(div);
        chatBox.scrollTop = chatBox.scrollHeight;
        
        // Salvar mensagem
        saveMessage(text, type, timestamp);
        
        if (type === 'admin') {
            userInput.focus();
        }
    }

    // Função para inicializar mensagens padrão
    function initializeDefaultMessages() {
        const welcomeMessage = `
            <div class="chat-message admin">
                <img src="/sistema/assets/images/bot-avatar.png" alt="TechHelp Bot" class="avatar">
                <div class="message-content">
                    <p>Olá, eu sou o assistente virtual da TechHelp!</p>
                </div>
            </div>
            <div class="chat-message admin">
                <img src="/sistema/assets/images/bot-avatar.png" alt="TechHelp Bot" class="avatar">
                <div class="message-content">
                    <p>Escolha uma opção ou digite sua dúvida:</p>
                    <div class="chat-options">
                        <button class="option-button" data-message="Informações sobre serviços e preços">
                            <i class="fas fa-info-circle"></i>
                            <span>Informações sobre Serviços</span>
                        </button>
                        <button class="option-button" data-message="Qual o horário de atendimento?">
                            <i class="fas fa-clock"></i>
                            <span>Horários de Atendimento</span>
                        </button>
                        <button class="option-button" data-message="Quais as formas de pagamento?">
                            <i class="fas fa-credit-card"></i>
                            <span>Formas de Pagamento</span>
                        </button>
                        <button class="option-button" data-message="Qual a garantia dos serviços?">
                            <i class="fas fa-shield-alt"></i>
                            <span>Garantia dos Serviços</span>
                        </button>
                        <button class="option-button" data-message="Gostaria de falar sobre outro assunto">
                            <i class="fas fa-comments"></i>
                            <span>Outros Assuntos</span>
                        </button>
                    </div>
                </div>
            </div>
        `;
        chatBox.innerHTML = welcomeMessage;
        initializeOptionButtons();
    }

    // Função para adicionar event listeners aos botões de opção
    function initializeOptionButtons() {
        document.querySelectorAll('.option-button').forEach(button => {
            button.addEventListener('click', function() {
                const message = this.getAttribute('data-message');
                if (message) {
                    sendMessage(message);
                }
            });
        });
    }

    // Função para formatar as opções iniciais do bot
    function getBotInitialOptions() {
        return `
            <div class="message-content">
                <p>Escolha uma opção ou digite sua dúvida:</p>
                <div class="chat-options">
                    <button class="option-button" data-message="Informações sobre serviços e preços">
                        <i class="fas fa-info-circle"></i>
                        <span>Informações sobre Serviços</span>
                    </button>
                    <button class="option-button" data-message="Qual o horário de atendimento?">
                        <i class="fas fa-clock"></i>
                        <span>Horários de Atendimento</span>
                    </button>
                    <button class="option-button" data-message="Quais as formas de pagamento?">
                        <i class="fas fa-credit-card"></i>
                        <span>Formas de Pagamento</span>
                    </button>
                    <button class="option-button" data-message="Qual a garantia dos serviços?">
                        <i class="fas fa-shield-alt"></i>
                        <span>Garantia dos Serviços</span>
                    </button>
                    <button class="option-button" data-message="Gostaria de falar sobre outro assunto">
                        <i class="fas fa-comments"></i>
                        <span>Outros Assuntos</span>
                    </button>
                </div>
            </div>
        `;
    }

    // Carregar mensagens salvas ou inicializar padrão
    function loadMessages() {
        const savedMessages = JSON.parse(localStorage.getItem('chatMessages') || '[]');
        if (savedMessages.length > 0) {
            loadSavedMessages();
            initializeOptionButtons(); // Reinicializar botões após carregar mensagens
        } else {
            initializeDefaultMessages();
        }
    }

    // Inicializar chat
    loadMessages();

    // Garantir que elementos existem antes de adicionar ao body
    if (button && widget) {
        document.body.appendChild(button);
        document.body.appendChild(widget);
    }
});
</script>
