<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Administrativo')</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --navy-dark: #0f2d4d;
            --navy: #163d63;
            --dorado-dark: #c9a344;
            --dorado: #e0be6b;
            --verde: #3a6b47;
            --crema: #f7f4ec;
            --ancho-sidebar: 230px;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: -apple-system, "Segoe UI", sans-serif;
            background: var(--crema);
        }

        /* Topbar solo visible en móvil */
        .topbar-movil {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 56px;
            background: var(--navy-dark);
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
            z-index: 100;
        }
        .topbar-movil .marca {
            color: var(--dorado);
            font-family: Georgia, serif;
            font-size: 15px;
            font-weight: 700;
        }
        .btn-hamburguesa {
            background: none;
            border: none;
            color: var(--crema);
            cursor: pointer;
            padding: 6px;
        }

        .overlay-sidebar {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 90;
        }

        /* Sidebar: position FIXED, no depende de flex ni del alto del contenido.
           Queda pegado al viewport siempre, sin importar cuán larga sea la página. */
        .sidebar {
            width: var(--ancho-sidebar);
            background: var(--navy-dark);
            color: var(--crema);
            padding: 24px 0;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            overflow-y: auto;
            z-index: 80;
        }
        .sidebar-header {
            text-align: center;
            padding: 0 20px 20px;
            margin-bottom: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .logo-colegio {
            max-width: 90px;
            width: 100%;
            height: auto;
            margin-bottom: 10px;
        }
        .nombre-usuario {
            color: var(--dorado);
            font-family: Georgia, serif;
            font-size: 14px;
            margin: 0;
        }
        .sidebar a {
            display: block;
            color: var(--crema);
            text-decoration: none;
            padding: 12px 20px;
            font-size: 14px;
            border-left: 3px solid transparent;
        }
        .sidebar a:hover, .sidebar a.active {
            background: var(--navy);
            border-left-color: var(--dorado-dark);
            color: var(--dorado);
        }
        .sidebar form { margin-top: auto; padding: 12px 20px 0; }
        .sidebar button {
            width: 100%;
            background: transparent;
            border: 1px solid rgba(255,255,255,0.2);
            color: var(--crema);
            padding: 10px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
        }
        .sidebar button:hover { border-color: var(--dorado-dark); color: var(--dorado); }

        /* Content: ya NO es flex-item, así que se separa del sidebar con margin-left */
        .content {
            margin-left: var(--ancho-sidebar);
            padding: 32px 40px;
            min-height: 100vh;
        }
        .content h1 {
            font-family: Georgia, serif;
            color: var(--navy-dark);
            margin-top: 0;
        }

        /* --- Componentes compartidos: tarjetas de tabla --- */
        .tarjeta-tabla {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 16px rgba(15,45,77,0.08);
            overflow: hidden;
        }
        .tabla-scroll { max-height: 480px; overflow-y: auto; }
        .tabla-docentes { width: 100%; border-collapse: collapse; font-size: 14px; }
        .tabla-docentes thead th {
            background: var(--crema);
            color: var(--navy-dark);
            font-weight: 600;
            text-align: left;
            padding: 14px 20px;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        .tabla-docentes .col-acciones { text-align: right; width: 140px; }
        .tabla-docentes tbody tr { border-top: 1px solid #f0ede4; transition: background 0.1s; }
        .tabla-docentes tbody tr:hover { background: #fbfaf6; }
        .tabla-docentes td { padding: 13px 20px; }
        .fila-vacia-texto { padding: 28px 20px; text-align: center; color: #94a3b8; }

        .acciones { display: flex; gap: 8px; justify-content: flex-end; }
        .btn-icono-solo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.15s ease;
            flex-shrink: 0;
            text-decoration: none;
        }
        .btn-editar { background: #eef4fa; color: var(--navy); }
        .btn-editar:hover { background: var(--navy); color: #fff; transform: translateY(-1px); }
        .btn-eliminar { background: #fbeceb; color: #b3261e; }
        .btn-eliminar:hover { background: #b3261e; color: #fff; transform: translateY(-1px); }

        @media (max-width: 768px) {
            .topbar-movil { display: flex; }

            .sidebar {
                top: 0; left: 0; bottom: 0;
                z-index: 95;
                transform: translateX(-100%);
                transition: transform 0.2s ease;
                width: 240px;
                padding-top: 20px;
            }
            .sidebar.abierto { transform: translateX(0); }

            .overlay-sidebar.visible { display: block; }

            .content {
                margin-left: 0;
                padding: 20px 16px;
                padding-top: 76px; /* espacio para la topbar fija */
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="topbar-movil">
        <span class="marca">Liceo Liberia</span>
        <button class="btn-hamburguesa" onclick="toggleSidebar()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="6" x2="21" y2="6"/>
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>
    </div>

    <div class="overlay-sidebar" id="overlay" onclick="toggleSidebar()"></div>

    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('images/escudo.png') }}" alt="Logo Liceo Liberia" class="logo-colegio">
            <p class="nombre-usuario">{{ Auth::user()?->name ?? 'Invitado' }}</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('admin.docentes.index') }}" class="{{ request()->routeIs('admin.docentes.*') ? 'active' : '' }}">Docentes</a>
        <a href="{{ route('admin.materias.index') }}" class="{{ request()->routeIs('admin.materias.*') ? 'active' : '' }}">Materias</a>
        <a href="{{ route('admin.secciones.index') }}" class="{{ request()->routeIs('admin.secciones.*') ? 'active' : '' }}">Secciones</a>
        <a href="{{ route('admin.estudiantes.index') }}" class="{{ request()->routeIs('admin.estudiantes.*') ? 'active' : '' }}">Estudiantes</a>
        <a href="{{ route('admin.reportes.index') }}" class="{{ request()->routeIs('admin.reportes.*') ? 'active' : '' }}">Reportes</a>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit">Cerrar sesión</button>
        </form>
    </nav>

    <main class="content">
        @if(session('exito'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    Swal.fire({ icon: 'success', title: '{{ session('exito') }}', timer: 2000, showConfirmButton: false });
                });
            </script>
        @endif
        @if(session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    Swal.fire({ icon: 'error', title: 'No se pudo completar la acción', text: {!! json_encode(session('error')) !!} });
                });
            </script>
        @endif
        @yield('content')
    </main>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('abierto');
            document.getElementById('overlay').classList.toggle('visible');
        }
    </script>
    @stack('scripts')
</body>
</html>