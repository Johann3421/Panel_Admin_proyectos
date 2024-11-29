<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aplicación DRE')</title>

    <!-- Bootstrap CSS desde CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome para iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Estilos propios -->
    @vite(['resources/css/styles.css', 'resources/js/script.js'])

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

    <!-- Botón del chatbot -->
<button id="bot-button">
    <img src="{{ Vite::asset('resources/images/chatbot2.png') }}" alt="Bot Icon" style="width: 64px; height: 64px;">
</button>

<!-- Overlay -->
<div id="overlay"></div>

<!-- Cuadro del chatbot -->
<div id="chat-box">
    <div id="chat-header">
        <img src="{{ Vite::asset('resources/images/chatbot2.png') }}" alt="Bot Icon" style="width: 40px; height: 40px; vertical-align: middle;">
        DREBOT
    </div>
    <div id="chat-content"></div>
    <div id="chat-footer">
        <input type="text" id="chat-input" placeholder="Escribe un mensaje...">
        <button id="send-button">
            <i class="fas fa-paper-plane"></i>
        </button>
        <button id="mic-button">
            <i class="fas fa-microphone"></i>
        </button>
    </div>
</div>

    <script>
        const botButton = document.getElementById('bot-button');
        const chatBox = document.getElementById('chat-box');
        const overlay = document.getElementById('overlay');
        const chatInput = document.getElementById('chat-input');
        const sendButton = document.getElementById('send-button');
        const micButton = document.getElementById('mic-button');
        const chatContent = document.getElementById('chat-content');

        let mediaRecorder;
        let audioChunks = [];

        botButton.addEventListener('click', toggleChatbox);
        overlay.addEventListener('click', toggleChatbox);

        // Manejar evento Enter para enviar mensaje
        chatInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        sendButton.addEventListener('click', sendMessage);

        micButton.addEventListener('click', toggleMicrophone);

        function toggleChatbox() {
            const isVisible = chatBox.style.display === 'flex';
            chatBox.style.display = isVisible ? 'none' : 'flex';
            overlay.style.display = isVisible ? 'none' : 'block';
        }

        async function sendMessage() {
            const userMessage = chatInput.value.trim();
            if (userMessage) {
                appendMessage('user', userMessage);
                chatInput.value = '';

                const loadingMessage = appendLoadingMessage();

                const response = await fetch('{{ route("chatbot.message") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ message: userMessage }),
                });

                chatContent.removeChild(loadingMessage);

                const data = await response.json();
                appendMessage('bot', data.response);

                // Generar y reproducir respuesta de audio
                playAudioResponse(data.response_audio_url);
            }
        }

        function appendMessage(sender, text) {
            const message = document.createElement('div');
            message.classList.add('message', sender);
            message.innerHTML = `<div class="bubble">${text}</div>`;
            chatContent.appendChild(message);
            chatContent.scrollTop = chatContent.scrollHeight;
        }

        function appendLoadingMessage() {
            const message = document.createElement('div');
            message.classList.add('message', 'bot');
            message.innerHTML = `
                <div class="bubble">
                    <div class="loading-dots">
                        <span></span><span></span><span></span>
                    </div>
                </div>`;
            chatContent.appendChild(message);
            chatContent.scrollTop = chatContent.scrollHeight;
            return message;
        }

        function toggleMicrophone() {
            if (mediaRecorder && mediaRecorder.state === 'recording') {
                mediaRecorder.stop();
                micButton.classList.remove('active');
            } else {
                startRecording();
                micButton.classList.add('active');
            }
        }

        function startRecording() {
            navigator.mediaDevices.getUserMedia({ audio: true })
                .then(stream => {
                    mediaRecorder = new MediaRecorder(stream);
                    audioChunks = [];
                    mediaRecorder.ondataavailable = event => {
                        audioChunks.push(event.data);
                    };
                    mediaRecorder.onstop = () => {
                        const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                        sendAudio(audioBlob);
                    };
                    mediaRecorder.start();
                })
                .catch(err => {
                    console.error('Error accessing microphone:', err);
                });
        }

        async function sendAudio(audioBlob) {
            const formData = new FormData();
            formData.append('audio', audioBlob);

            const response = await fetch('{{ route("chatbot.audio") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: formData,
            });

            const data = await response.json();
            appendMessage('bot', data.response);
            playAudioResponse(data.response_audio_url);
        }

        function playAudioResponse(audioUrl) {
            const audio = new Audio(audioUrl);
            audio.play();
        }
    </script>
</body>

</html>
