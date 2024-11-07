<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ url('/') }}" class="brand-link">
        <div class="logo">
            <img src="{{ asset('imagenes/logo_dre.png') }}" alt="Logo de la marca" class="brand-image img-circle elevation-3">
            <span class="brand-text font-weight-light">DRE-HUÁNUCO</span>
        </div>
    </a>
    
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('visitas.index') }}" class="nav-link">
                        <i class="material-icons">person_add</i>
                        <p>Registrar visitas</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('reporte.index') }}" class="nav-link">
                        <i class="material-icons">assessment</i>
                        <p>Reporte</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('cronometro.index') }}" class="nav-link">
                        <i class="material-icons">access_time</i>
                        <p>Cronometro</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('recesos.index') }}" class="nav-link">
                        <i class="material-icons">assessment</i>
                        <p>Recesos</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
