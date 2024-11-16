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


    <!-- Estilos propios y JavaScript -->
    @vite(['resources/css/styles.css', 'resources/js/script.js'])
    
</head>

<body>
    <div class="wrapper d-flex">
        <!-- Aside con ancho fijo -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4" style="width: 250px;">
            @include('layouts.aside')
        </aside>
        
        <!-- Contenedor para header y contenido -->
        <div class="flex-grow-1">
            <!-- Header ocupa el ancho restante -->
            <header class="main-header">
                @include('layouts.header')
            </header>

            <!-- Contenido principal -->
            <div class="content-wrapper">
                <section class="content">
                    @yield('content')
                </section>
            </div>
        </div>
        <!-- Botón flotante en la esquina -->
<div id="chat-button" style="position: fixed; bottom: 20px; right: 20px; cursor: pointer;">
    <button onclick="toggleChat()">Chat</button>
</div>

<!-- Contenedor del chat -->
<div id="chat-widget" style="display: none; position: fixed; bottom: 60px; right: 20px; width: 300px; height: 400px; border: 1px solid #ccc; background: white;">
    <iframe src="/botman/tinker" style="width: 100%; height: 100%; border: none;"></iframe>
</div>

    </div>

    <!-- Bootstrap JS desde CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function toggleChat() {
        var chatWidget = document.getElementById('chat-widget');
        chatWidget.style.display = chatWidget.style.display === 'none' ? 'block' : 'none';
    }
</script>
<script>
    var botmanWidget = {
        title: "Asistente Virtual",
        mainColor: "#4e8cff",
        bubbleBackground: "#007bff",
        headerTextColor: "#fff",
        introMessage: "👋 ¡Hola! Soy tu asistente para el sistema de Registro de Visitas. ¿En qué puedo ayudarte?",
        placeholderText: "Escribe un mensaje...",
        aboutText: "",
        chatServer: "/botman" // Ruta hacia el controlador de tu chatbot
    };
</script>
<script src="https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/widget.js"></script>

</body>

</html>

