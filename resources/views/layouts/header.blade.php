
    <header class="header">
        <div class="logo">
            <img src="{{ asset('imagenes/logo_dre.png') }}" alt="Logo de la marca">
            <span class="logo-text">DIRECCION REGIONAL DE EDUCACION HUÁNUCO</span>
        </div>
        <nav>
            <ul class="nav-links">
                <li><a href="{{ route('visitas.index') }}">Visita</a></li>
                <li><a href="{{ route('reporte.index') }}">Reporte</a></li>
                <li><a href="{{ route('cronometro.index') }}">Cronometro</a></li>
                <li><a href="{{ route('recesos.index') }}">Recesos</a></li>
            </ul>
        </nav>
    </header>
</div>
