<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Estilos y scripts -->
    @vite(['resources/css/styles1.css', 'resources/js/script.js'])
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.5/css/boxicons.min.css" rel="stylesheet">
    <title>Inicio de Sesión</title>
</head>
<body>
    <!-- Contenedor de burbujas -->
    <div class="bubbles">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
    </div>

    <!-- Contenedor del formulario -->
    <div class="l-form">
        <div class="form-container">
            <div class="logo-container">
                <img src="{{ Vite::asset('resources/images/logo_principal.jpeg') }}" alt="Logo">
            </div>
            <div class="form">
                <form action="{{ route('login.submit') }}" method="POST" class="form__content">
                    @csrf
                    <h1 class="form__title">Bienvenido</h1>
                    @if($errors->has('loginError'))
                        <div class="error">{{ $errors->first('loginError') }}</div>
                    @endif
                    <div class="form__div form__div-one">
                        <div class="form__icon">
                            <i class='bx bx-user-circle'></i>
                        </div>
                        <div class="form__div-input">
                            <label for="username" class="form__label">Usuario</label>
                            <input type="text" class="form__input" name="username" id="username" required>
                        </div>
                    </div>
                    <div class="form__div">
                        <div class="form__icon">
                            <i class='bx bx-lock'></i>
                        </div>
                        <div class="form__div-input">
                            <label for="password" class="form__label">Contraseña</label>
                            <input type="password" class="form__input" name="password" id="password" required>
                        </div>
                    </div>
                    <input type="submit" class="form__button" value="Login">
                </form>
            </div>
        </div>
    </div>

    <!-- Script JS -->
    <script>
        // Spinner del preloader
        window.onload = function () {
            const preloader = document.getElementById('preloader');
            preloader.style.display = 'none';

            // Fondo Dinámico
            const background = document.getElementById('dynamic-background');
            const images = [
                'https://source.unsplash.com/1920x1080/?nature',
                'https://source.unsplash.com/1920x1080/?city',
                'https://source.unsplash.com/1920x1080/?abstract',
                'https://source.unsplash.com/1920x1080/?technology'
            ];
            const randomImage = images[Math.floor(Math.random() * images.length)];
            background.style.backgroundImage = `linear-gradient(to bottom, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.7)), url(${randomImage})`;
        };

        // Gestión de focus en los campos del formulario
        const inputs = document.querySelectorAll(".form__input");
        inputs.forEach(input => {
            input.addEventListener("focus", () => input.closest('.form__div').classList.add("focus"));
            input.addEventListener("blur", () => {
                if (!input.value) input.closest('.form__div').classList.remove("focus");
            });
        });
        
    </script>
</body>
</html>
