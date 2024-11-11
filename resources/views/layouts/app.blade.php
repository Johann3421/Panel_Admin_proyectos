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
    </div>

    <!-- Bootstrap JS desde CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

