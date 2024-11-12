<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- ===== CSS ===== -->
    <!-- Incluye los estilos y scripts con Vite -->
    @vite(['resources/css/styles1.css', 'resources/js/script.js'])

    <!-- ===== BOX ICONS ===== -->
    <link href='https://cdn.jsdelivr.net/npm/boxicons@2.0.5/css/boxicons.min.css' rel='stylesheet'>

    <title>Login form responsive</title>
</head>

<body>
    <div class="l-form">
        <div class="form-container">
            <div class="logo-container">
                <img src="{{ Vite::asset('resources/images/logo_principal.jpeg') }}" alt="Logo">
            </div>

            <div class="form">
                <!-- Ruta del formulario apunta a la ruta de Laravel -->
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

    <!-- ===== MAIN JS ===== -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
</body>
</html>
<script>
/*===== FOCUS =====*/
const inputs = document.querySelectorAll(".form__input")

/*=== Add focus ===*/
function addfocus(){
    let parent = this.parentNode.parentNode
    parent.classList.add("focus")
}

/*=== Remove focus ===*/
function remfocus(){
    let parent = this.parentNode.parentNode
    if(this.value == ""){
        parent.classList.remove("focus")
    }
}

/*=== To call function===*/
inputs.forEach(input=>{
    input.addEventListener("focus",addfocus)
    input.addEventListener("blur",remfocus)
})

</script>