@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
    <div class="dash-header">
        <div>
            <h1>Panel general</h1>
            <p class="dash-subtitulo">Resumen de ausentismo · {{ ucfirst($mesSeleccionado->translatedFormat('F Y')) }}</p>
        </div>

        <div class="dash-header-acciones">
            <a href="{{ route('admin.reportes.index') }}" class="link-reportes">Ver reportes y exportar →</a>
            <form method="GET" action="{{ route('admin.dashboard') }}" class="selector-mes-form">
                <select name="mes" class="selector-mes" onchange="this.form.submit()">
                    @foreach($mesesDisponibles as $mes)
                        <option value="{{ $mes['valor'] }}" {{ request('mes', now()->format('Y-m')) === $mes['valor'] ? 'selected' : '' }}>
                            {{ $mes['etiqueta'] }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="kpis">
        <div class="kpi-card kpi-navy">
            <span class="kpi-label">Total de ausencias</span>
            <span class="kpi-valor">{{ $totalAusenciasMes }}</span>
            <span class="kpi-nota">lecciones ausentes este mes</span>
        </div>

        <div class="kpi-card">
            <span class="kpi-label">Estudiante con más ausencias</span>
            @if($estudianteTop)
                <span class="kpi-valor-texto">{{ $estudianteTop->estudiante->nombre_completo ?? '—' }}</span>
                <span class="kpi-nota">{{ $estudianteTop->total }} ausencias · {{ $estudianteTop->estudiante->seccion->nombre ?? '—' }}</span>
            @else
                <span class="kpi-valor-texto">Sin datos</span>
                <span class="kpi-nota">No hay ausencias registradas este mes</span>
            @endif
        </div>

        <div class="kpi-card">
            <span class="kpi-label">Sección más afectada</span>
            @if($seccionTop)
                <span class="kpi-valor-texto">{{ $seccionTop->seccion->nombre ?? '—' }}</span>
                <span class="kpi-nota">{{ $seccionTop->total }} ausencias acumuladas</span>
            @else
                <span class="kpi-valor-texto">Sin datos</span>
                <span class="kpi-nota">No hay ausencias registradas este mes</span>
            @endif
        </div>

        <div class="kpi-card {{ $docentesPendientes->count() > 0 ? 'kpi-alerta' : 'kpi-ok' }}">
            <span class="kpi-label">Docentes sin reportar</span>
            <span class="kpi-valor">{{ $docentesPendientes->count() }}</span>
            <span class="kpi-nota">de {{ \App\Models\Docente::where('activo', true)->count() }} docentes activos</span>
        </div>
    </div>

    {{-- Buscadores --}}
    <div class="graficos-grid">
        <div class="tarjeta-grafico">
            <h2 class="grafico-titulo">Buscar estudiante</h2>
            <div class="buscador-wrap">
                <input type="text" id="buscadorEstudiante" class="buscador-input" placeholder="Escribí el nombre del estudiante..." autocomplete="off">
                <div id="resultadosEstudiante" class="buscador-resultados"></div>
            </div>
            <p class="grafico-vacio" style="padding-top:14px;">Buscá y hacé clic para ver su detalle de ausencias del mes.</p>
        </div>

        <div class="tarjeta-grafico">
            <h2 class="grafico-titulo">Buscar docente</h2>
            <div class="buscador-wrap">
                <input type="text" id="buscadorDocente" class="buscador-input" placeholder="Escribí el nombre del docente..." autocomplete="off">
                <div id="resultadosDocente" class="buscador-resultados"></div>
            </div>
            <p class="grafico-vacio" style="padding-top:14px;">Buscá y hacé clic para ver a quiénes les reportó ausencias.</p>
        </div>
    </div>

    {{-- Ausencias por sección --}}
    <div class="graficos-grid">
        <div class="tarjeta-grafico tarjeta-ancha">
            <div class="cabecera-con-selector">
                <h2 class="grafico-titulo">Ausencias por sección</h2>
                <select id="selectorSeccion" class="selector-mes">
                    <option value="">Elegí una sección…</option>
                    @foreach($secciones as $s)
                        <option value="{{ $s->id }}" {{ (string) $seccionFiltroId === (string) $s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div id="contenedorAlumnosSeccion">
                @if(!$seccionFiltroId)
                    <p class="grafico-vacio">Elegí una sección arriba para ver el detalle de sus ausencias.</p>
                @elseif($alumnosSeccion->isEmpty())
                    <p class="grafico-vacio">Esa sección no tiene ausencias registradas este mes.</p>
                @else
                    <div class="tabla-scroll tabla-scroll-corta">
                        <table class="tabla-docentes">
                            <thead>
                                <tr>
                                    <th>Estudiante</th>
                                    <th style="text-align:right;">Ausencias</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($alumnosSeccion as $fila)
                                    <tr class="fila-clicable" data-id="{{ $fila->estudiante_id }}">
                                        <td>{{ $fila->estudiante->nombre_completo ?? '—' }}</td>
                                        <td style="text-align:right;">{{ $fila->total }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="grafico-vacio" style="padding-top:10px; padding-bottom:0;">Hacé clic en un estudiante para ver el detalle.</p>
                @endif
            </div>
        </div>

        <div class="tarjeta-grafico">
            <h2 class="grafico-titulo">Top 15 estudiantes</h2>
            @if($topEstudiantes->isEmpty())
                <p class="grafico-vacio">No hay ausencias registradas este mes.</p>
            @else
                <ol class="lista-top-estudiantes">
                    @foreach($topEstudiantes as $registro)
                        <li class="fila-clicable" data-id="{{ $registro->estudiante_id }}">
                            <span class="top-nombre">{{ $registro->estudiante->nombre_completo ?? '—' }}</span>
                            <span class="top-seccion">{{ $registro->estudiante->seccion->nombre ?? '—' }}</span>
                            <span class="top-total">{{ $registro->total }}</span>
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>
    </div>

    {{-- Docentes pendientes --}}
    <div class="tarjeta-tabla tarjeta-docentes-pendientes">
        <div class="tabla-cabecera-simple">
            <h2 class="grafico-titulo">Docentes que aún no han reportado</h2>
        </div>
        @if($docentesPendientes->isEmpty())
            <p class="grafico-vacio" style="padding:8px 20px 24px;">Todos los docentes activos ya reportaron este mes. 🎉</p>
        @else
            <div class="tabla-scroll tabla-scroll-corta">
                <table class="tabla-docentes">
                    <tbody>
                        @foreach($docentesPendientes as $docente)
                            <tr>
                                <td data-label="Nombre">{{ $docente->nombre }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @push('styles')
    <style>
        * { box-sizing: border-box; }

        .dash-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 14px;
            margin-bottom: 24px;
        }
        .dash-subtitulo { color: #7a7462; font-size: 14px; margin: 4px 0 0; }
        .dash-header-acciones { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
        .link-reportes {
            color: var(--navy-dark);
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            border-bottom: 1px solid var(--dorado-dark);
            padding-bottom: 2px;
        }
        .link-reportes:hover { color: var(--dorado-dark); }
        .selector-mes {
            padding: 10px 14px;
            border: 1px solid #e2ddd0;
            border-radius: 10px;
            font-size: 14px;
            background: #fff;
            max-width: 100%;
        }
        .selector-mes:focus { outline: none; border-color: var(--dorado-dark); }

        /* --- KPIs --- */
        .kpis {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .kpi-card {
            background: #fff;
            border-radius: 14px;
            padding: 20px 22px;
            box-shadow: 0 4px 16px rgba(15,45,77,0.08);
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
            overflow: hidden;
        }
        .kpi-navy { background: var(--navy-dark); color: #fff; }
        .kpi-navy .kpi-label { color: var(--dorado); }
        .kpi-navy .kpi-nota { color: rgba(255,255,255,0.7); }
        .kpi-alerta { border-left: 4px solid #b3261e; }
        .kpi-ok { border-left: 4px solid var(--verde); }

        .kpi-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #7a7462;
        }
        .kpi-valor {
            font-family: Georgia, serif;
            font-size: 34px;
            color: var(--navy-dark);
            line-height: 1.1;
        }
        .kpi-navy .kpi-valor { color: #fff; }
        .kpi-valor-texto {
            font-family: Georgia, serif;
            font-size: 18px;
            color: var(--navy-dark);
            line-height: 1.3;
            margin-top: 2px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .kpi-nota {
            font-size: 12px;
            color: #7a7462;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* --- Tarjetas / grid genérico --- */
        .graficos-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .tarjeta-grafico {
            background: #fff;
            border-radius: 14px;
            padding: 20px 22px;
            box-shadow: 0 4px 16px rgba(15,45,77,0.08);
            min-width: 0;
            overflow: visible;
        }
        .tarjeta-ancha { grid-column: span 2; }
        .grafico-titulo {
            font-family: Georgia, serif;
            color: var(--navy-dark);
            font-size: 16px;
            margin: 0 0 16px;
        }
        .grafico-vacio {
            color: #94a3b8;
            font-size: 14px;
            padding: 30px 0;
            text-align: center;
            margin: 0;
        }

        .cabecera-con-selector {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 16px;
        }
        .cabecera-con-selector .grafico-titulo { margin: 0; }
        .cabecera-con-selector .selector-mes { min-width: 220px; }

        /* --- Buscadores --- */
        .buscador-wrap { position: relative; }
        .buscador-input {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #e2ddd0;
            border-radius: 10px;
            font-size: 14px;
        }
        .buscador-input:focus { outline: none; border-color: var(--dorado-dark); }

        /* Oculto por defecto: solo se muestra cuando hay resultados que renderizar */
        .buscador-resultados {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid #e2ddd0;
            border-radius: 12px;
            box-shadow: 0 12px 28px rgba(15,45,77,0.16);
            z-index: 30;
            max-height: 260px;
            overflow-y: auto;
            padding: 6px;
        }
        .buscador-resultados.visible { display: block; }
        .buscador-resultados::-webkit-scrollbar { width: 6px; }
        .buscador-resultados::-webkit-scrollbar-track { background: transparent; }
        .buscador-resultados::-webkit-scrollbar-thumb { background: #e2ddd0; border-radius: 10px; }
        .buscador-resultados::-webkit-scrollbar-thumb:hover { background: var(--dorado-dark); }

        .buscador-item {
            padding: 9px 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            border-radius: 8px;
            transition: background 0.12s ease;
        }
        .buscador-item + .buscador-item { margin-top: 2px; }
        .buscador-item:hover { background: var(--crema); }

        .buscador-avatar {
            flex-shrink: 0;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--navy-dark);
            color: var(--dorado);
            font-family: Georgia, serif;
            font-weight: 700;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .buscador-info {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 1px;
        }
        .buscador-nombre {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--navy-dark);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .buscador-chip {
            flex-shrink: 0;
            background: var(--crema);
            color: var(--navy-dark);
            font-size: 11px;
            font-weight: 600;
            padding: 3px 9px;
            border-radius: 20px;
        }
        .buscador-vacio {
            cursor: default;
            color: #94a3b8;
            justify-content: center;
            font-size: 13px;
            padding: 16px 10px;
        }
        .buscador-vacio:hover { background: transparent; }

        /* --- Filas / items clicables (tablas y listas) --- */
        .fila-clicable { cursor: pointer; transition: background 0.12s ease; }
        .fila-clicable:hover { background: var(--crema); }

        .lista-top-estudiantes {
            list-style: none;
            margin: 0;
            padding: 0;
            max-height: 216px; /* ~5 filas visibles, el resto scrollea */
            overflow-y: auto;
        }
        .lista-top-estudiantes::-webkit-scrollbar { width: 6px; }
        .lista-top-estudiantes::-webkit-scrollbar-track { background: transparent; }
        .lista-top-estudiantes::-webkit-scrollbar-thumb { background: #e2ddd0; border-radius: 10px; }
        .lista-top-estudiantes::-webkit-scrollbar-thumb:hover { background: var(--dorado-dark); }
        .lista-top-estudiantes li {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 4px;
            border-bottom: 1px solid #f0ede4;
            font-size: 13px;
            border-radius: 8px;
        }
        .lista-top-estudiantes li:last-child { border-bottom: none; }
        .top-nombre {
            flex: 1;
            min-width: 0;
            color: var(--navy-dark);
            font-weight: 600;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .top-seccion { color: #94a3b8; font-size: 12px; flex-shrink: 0; }
        .top-total {
            background: var(--crema);
            color: var(--navy-dark);
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 12px;
            flex-shrink: 0;
        }

        /* --- Docentes pendientes --- */
        .tarjeta-docentes-pendientes { margin-top: 8px; }
        .tabla-cabecera-simple { padding: 18px 20px 4px; }
        .tabla-scroll-corta { max-height: 320px; }

        /* --- Tabla de detalle dentro de los modales SweetAlert2 --- */
        .modal-tabla-scroll {
            max-height: 280px;
            overflow-y: auto;
            text-align: left;
            border: 1px solid #f0ede4;
            border-radius: 10px;
        }
        .modal-tabla-scroll::-webkit-scrollbar { width: 6px; }
        .modal-tabla-scroll::-webkit-scrollbar-track { background: transparent; }
        .modal-tabla-scroll::-webkit-scrollbar-thumb { background: #e2ddd0; border-radius: 10px; }
        .modal-tabla-scroll::-webkit-scrollbar-thumb:hover { background: #c9a344; }
        .modal-tabla-scroll table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .modal-tabla-scroll thead th {
            position: sticky;
            top: 0;
            background: #f7f4ec;
            text-align: left;
            padding: 8px 10px;
            font-size: 11.5px;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            color: #7a7462;
            border-bottom: 1px solid #e2ddd0;
        }
        .modal-tabla-scroll tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid #f0ede4;
        }
        .modal-tabla-scroll tbody tr:last-child td { border-bottom: none; }
        .modal-resumen {
            text-align: left;
            color: #7a7462;
            margin: 0 0 12px;
            font-size: 13px;
        }

        /* --- Responsive --- */
        @media (max-width: 1024px) {
            .kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 900px) {
            .graficos-grid { grid-template-columns: minmax(0, 1fr); }
            .tarjeta-ancha { grid-column: span 1; }
        }
        @media (max-width: 640px) {
            .dash-header { flex-direction: column; align-items: stretch; }
            .selector-mes { width: 100%; }
            .kpis { grid-template-columns: minmax(0, 1fr); }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function debounce(fn, delay) {
            let temporizador;
            return (...args) => {
                clearTimeout(temporizador);
                temporizador = setTimeout(() => fn(...args), delay);
            };
        }

        function iniciales(nombre) {
            return nombre
                .split(' ')
                .filter(Boolean)
                .slice(0, 2)
                .map(p => p[0])
                .join('')
                .toUpperCase();
        }

        const mesActual = "{{ $mesSeleccionado->format('Y-m') }}";

        // --- Buscador de estudiantes ---
        const inputEstudiante = document.getElementById('buscadorEstudiante');
        const resultadosEstudiante = document.getElementById('resultadosEstudiante');

        inputEstudiante.addEventListener('input', debounce(async (e) => {
            const q = e.target.value.trim();

            if (q.length < 2) {
                resultadosEstudiante.innerHTML = '';
                resultadosEstudiante.classList.remove('visible');
                return;
            }

            const resp = await fetch(`{{ route('admin.dashboard.buscarEstudiantes') }}?q=${encodeURIComponent(q)}`);
            const datos = await resp.json();

            if (datos.length === 0) {
                resultadosEstudiante.innerHTML = '<div class="buscador-item buscador-vacio">Sin resultados</div>';
            } else {
                resultadosEstudiante.innerHTML = datos.map(d => `
                    <div class="buscador-item" data-id="${d.id}">
                        <span class="buscador-avatar">${iniciales(d.nombre)}</span>
                        <span class="buscador-info">
                            <span class="buscador-nombre">${d.nombre}</span>
                        </span>
                        <span class="buscador-chip">${d.seccion}</span>
                    </div>
                `).join('');
            }
            resultadosEstudiante.classList.add('visible');
        }, 300));

        resultadosEstudiante.addEventListener('click', (e) => {
            const item = e.target.closest('.buscador-item[data-id]');
            if (!item) return;
            abrirModalEstudiante(item.dataset.id);
            resultadosEstudiante.innerHTML = '';
            resultadosEstudiante.classList.remove('visible');
            inputEstudiante.value = '';
        });

        // --- Filas clicables: Ausencias por sección + Top 15 (delegado, porque la tabla se re-renderiza por AJAX) ---
        document.addEventListener('click', (e) => {
            const fila = e.target.closest('.fila-clicable[data-id]');
            if (!fila) return;
            abrirModalEstudiante(fila.dataset.id);
        });

        async function abrirModalEstudiante(id) {
            const base = "{{ url('/admin/dashboard/estudiante') }}";
            const resp = await fetch(`${base}/${id}?mes=${mesActual}`);
            const d = await resp.json();

            const filas = d.registros.map(r => `
                <tr>
                    <td>${r.fecha}</td>
                    <td>${r.materia}</td>
                    <td>${r.docente}</td>
                    <td style="text-align:right;">${r.cantidad}</td>
                </tr>
            `).join('') || '<tr><td colspan="4" style="text-align:center; color:#94a3b8; padding:14px;">Sin ausencias este mes</td></tr>';

            Swal.fire({
                title: d.nombre,
                html: `
                    <p class="modal-resumen">
                        ${d.seccion} · ${d.total_mes} ausencias este mes · ${d.total_historico} históricas
                    </p>
                    <div class="modal-tabla-scroll">
                        <table>
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Materia</th>
                                    <th>Docente</th>
                                    <th style="text-align:right;">Cant.</th>
                                </tr>
                            </thead>
                            <tbody>${filas}</tbody>
                        </table>
                    </div>
                `,
                confirmButtonColor: '#163d63',
                width: 500,
            });
        }

        // --- Buscador de docentes ---
        const inputDocente = document.getElementById('buscadorDocente');
        const resultadosDocente = document.getElementById('resultadosDocente');

        inputDocente.addEventListener('input', debounce(async (e) => {
            const q = e.target.value.trim();

            if (q.length < 2) {
                resultadosDocente.innerHTML = '';
                resultadosDocente.classList.remove('visible');
                return;
            }

            const resp = await fetch(`{{ route('admin.dashboard.buscarDocentes') }}?q=${encodeURIComponent(q)}`);
            const datos = await resp.json();

            if (datos.length === 0) {
                resultadosDocente.innerHTML = '<div class="buscador-item buscador-vacio">Sin resultados</div>';
            } else {
                resultadosDocente.innerHTML = datos.map(d => `
                    <div class="buscador-item" data-id="${d.id}">
                        <span class="buscador-avatar">${iniciales(d.nombre)}</span>
                        <span class="buscador-info">
                            <span class="buscador-nombre">${d.nombre}</span>
                        </span>
                    </div>
                `).join('');
            }
            resultadosDocente.classList.add('visible');
        }, 300));

        resultadosDocente.addEventListener('click', async (e) => {
            const item = e.target.closest('.buscador-item[data-id]');
            if (!item) return;
            const id = item.dataset.id;

            const base = "{{ url('/admin/dashboard/docente') }}";
            const resp = await fetch(`${base}/${id}?mes=${mesActual}`);
            const d = await resp.json();

            const filas = d.registros.map(r => `
                <tr>
                    <td>${r.fecha}</td>
                    <td>${r.estudiante}</td>
                    <td>${r.seccion}</td>
                    <td>${r.materia}</td>
                    <td style="text-align:right;">${r.cantidad}</td>
                </tr>
            `).join('') || '<tr><td colspan="5" style="text-align:center; color:#94a3b8; padding:14px;">Sin ausencias reportadas este mes</td></tr>';

            Swal.fire({
                title: d.nombre,
                html: `
                    <p class="modal-resumen">
                        ${d.total_mes} ausencias reportadas este mes
                    </p>
                    <div class="modal-tabla-scroll">
                        <table>
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Estudiante</th>
                                    <th>Sección</th>
                                    <th>Materia</th>
                                    <th style="text-align:right;">Cant.</th>
                                </tr>
                            </thead>
                            <tbody>${filas}</tbody>
                        </table>
                    </div>
                `,
                confirmButtonColor: '#163d63',
                width: 560,
            });

            resultadosDocente.innerHTML = '';
            resultadosDocente.classList.remove('visible');
            inputDocente.value = '';
        });

        // Cerrar los dropdowns si se hace clic afuera
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.buscador-wrap')) {
                resultadosEstudiante.innerHTML = '';
                resultadosEstudiante.classList.remove('visible');
                resultadosDocente.innerHTML = '';
                resultadosDocente.classList.remove('visible');
            }
        });

        // --- Filtro de "Ausencias por sección" vía AJAX: sin recargar, sin mover el scroll ---
        const selectorSeccion = document.getElementById('selectorSeccion');
        const contenedorAlumnosSeccion = document.getElementById('contenedorAlumnosSeccion');

        selectorSeccion.addEventListener('change', async () => {
            const seccionId = selectorSeccion.value;

            if (!seccionId) {
                contenedorAlumnosSeccion.innerHTML = '<p class="grafico-vacio">Elegí una sección arriba para ver el detalle de sus ausencias.</p>';
                return;
            }

            contenedorAlumnosSeccion.innerHTML = '<p class="grafico-vacio">Cargando…</p>';

            const url = `{{ route('admin.dashboard.ausenciasPorSeccion') }}?seccion_id=${seccionId}&mes=${mesActual}`;
            const resp = await fetch(url);
            const d = await resp.json();

            if (d.alumnos.length === 0) {
                contenedorAlumnosSeccion.innerHTML = '<p class="grafico-vacio">Esa sección no tiene ausencias registradas este mes.</p>';
                return;
            }

            const filas = d.alumnos.map(a => `
                <tr class="fila-clicable" data-id="${a.id}">
                    <td>${a.nombre}</td>
                    <td style="text-align:right;">${a.total}</td>
                </tr>
            `).join('');

            contenedorAlumnosSeccion.innerHTML = `
                <div class="tabla-scroll tabla-scroll-corta">
                    <table class="tabla-docentes">
                        <thead>
                            <tr>
                                <th>Estudiante</th>
                                <th style="text-align:right;">Ausencias</th>
                            </tr>
                        </thead>
                        <tbody>${filas}</tbody>
                    </table>
                </div>
                <p class="grafico-vacio" style="padding-top:10px; padding-bottom:0;">Hacé clic en un estudiante para ver el detalle.</p>
            `;
        });
    </script>
    @endpush
@endsection