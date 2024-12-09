<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aplicación DRE')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Estilos propios -->
    @vite(['resources/css/styles.css', 'resources/js/script.js', 'resources/js/chatbot.js'])
</head>

<body>
    <div class="wrapper d-flex">
        <aside class="main-sidebar sidebar-dark-primary elevation-4" style="width: 250px;">
            @include('layouts.aside')
        </aside>

        <div class="flex-grow-1">
            <header class="main-header">
                @include('layouts.header')
            </header>

            <div class="content-wrapper">
                <section class="content">
                    @yield('content')
                </section>
            </div>
        </div>
    </div>

    <!-- Botones para abrir los cuadros de chat -->
    <button id="bot-button" class="btn">
        <img src="{{ asset('images/chatbot2.png') }}" alt="Bot Icon" style="width: 64px; height: 64px;">
    </button>
    <button id="form-button" class="btn btn-primary">
        <i class="fas fa-comment-dots"></i>
    </button>

    <!-- Overlay -->
    <div id="overlay" style="display: none;"></div>

    <!-- Cuadro del chatbot -->
    <div id="chat-box" style="display: none;">
        <div id="chat-header">
            <img src="{{ asset('images/chatbot2.png') }}" alt="Bot Icon" style="width: 40px; height: 40px; vertical-align: middle;">
            DREBOT
        </div>
        <div id="chat-content"></div>
        <div id="chat-footer">
            <input type="text" id="chat-input" placeholder="Escribe un mensaje...">
            <button id="send-button">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>

    <!-- Formulario del chat -->
    <div id="chat-form-container" style="display: none;">
        <div id="chat-form-header">
            <span>Enviar Mensaje</span>
            <button id="clear-history" class="btn btn-sm btn-danger">Limpiar</button>
        </div>
        <div id="chat-history"></div>
        <div id="chat-form-content">
            <textarea id="chat-input-form" rows="2" placeholder="Escribe tu mensaje..."></textarea>
            <button id="form-send-button"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Chat-box elementos
        const botButton = document.getElementById('bot-button');
        const chatBox = document.getElementById('chat-box');
        const chatInput = document.getElementById('chat-input');
        const sendButton = document.getElementById('send-button');
        const chatContent = document.getElementById('chat-content');
        const overlay = document.getElementById('overlay');

        // Chat-form elementos
        const formButton = document.getElementById('form-button');
        const chatFormContainer = document.getElementById('chat-form-container');
        const chatInputForm = document.getElementById('chat-input-form');
        const sendButtonForm = document.getElementById('form-send-button');
        const chatHistory = document.getElementById('chat-history');
        const clearHistoryButton = document.getElementById('clear-history');

        // Prompt base
        const basePrompt = `
        Eres un asistente avanzado y amigable.
        Proporciónale instrucciones claras al usuario si solicita ayuda, como:
        - Si dice "¿cómo registrar visita?" explícales los pasos detallados sobre el flujo de registro de visita.
        - Si pide "listar recesos", muestra cómo se hace.
        - Sé preciso y utiliza ejemplos para enseñar el uso del sistema.
        `;

        // Mostrar/Ocultar chat-box
        botButton.addEventListener('click', () => toggleVisibility(chatBox, true));
        overlay.addEventListener('click', () => toggleVisibility(chatBox, false));

        // Mostrar/Ocultar chat-form
        formButton.addEventListener('click', () => {
            const isVisible = chatFormContainer.style.display === 'flex';
            chatFormContainer.style.display = isVisible ? 'none' : 'flex';
        });

        // Enviar mensaje desde el chat-box
        sendButton.addEventListener('click', sendMessageBox);
        chatInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessageBox();
            }
        });

        // Enviar mensaje desde el chat-form
        chatInputForm.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessageForm();
            }
        });

        sendButtonForm.addEventListener('click', sendMessageForm);

        // Limpiar historial de mensajes en el chat-form
        clearHistoryButton.addEventListener('click', () => {
            chatHistory.innerHTML = '';
        });

        // Función para alternar visibilidad
        function toggleVisibility(element, isOverlay = false) {
            const isVisible = element.style.display === 'flex';
            element.style.display = isVisible ? 'none' : 'flex';
            if (isOverlay) overlay.style.display = isVisible ? 'none' : 'block';
        }

        // Función para enviar mensaje en el chat-box
        async function sendMessageBox() {
            const userMessage = chatInput.value.trim();
            if (!userMessage) return;

            appendMessageToBox('user', userMessage);
            chatInput.value = '';

            try {
                const response = await fetch('{{ route("chatbot.message") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ message: userMessage }),
                });

                const data = await response.json();
                appendMessageToBox('bot', data.response);
            } catch {
                appendMessageToBox('bot', 'Ocurrió un error al procesar tu solicitud.');
            }
        }

        // Función para enviar mensaje en el chat-form
        async function sendMessageForm() {
            const userMessage = chatInputForm.value.trim();
            if (!userMessage) return;

            appendMessageToHistory('user', userMessage);
            chatInputForm.value = '';

            try {
                const response = await fetch('/api/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({ prompt: basePrompt, message: userMessage }),
                });

                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                const data = await response.json();
                appendMessageToHistory('bot', data.response || 'Lo siento, no puedo responder en este momento.');
            } catch (error) {
                console.error('Error:', error);
                appendMessageToHistory('bot', 'Ocurrió un error al procesar tu solicitud.');
            }
        }

        // Funciones para manejar mensajes en el historial del chat-form
        function appendMessageToHistory(sender, text) {
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('message', sender);
            messageDiv.innerHTML = `<div class="bubble">${text}</div>`;
            chatHistory.appendChild(messageDiv);
            chatHistory.scrollTop = chatHistory.scrollHeight;
        }

        // Función para manejar mensajes en el chat-box
        function appendMessageToBox(sender, text) {
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('message', sender);
            messageDiv.innerHTML = `<div class="bubble">${text}</div>`;
            chatContent.appendChild(messageDiv);
            chatContent.scrollTop = chatContent.scrollHeight;
        }
    });
</script>

</body>

</html>
