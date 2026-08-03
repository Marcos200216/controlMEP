@extends('admin.layout')

@section('content')

@push('styles')
<style>
    .reportes-filtros {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 16px rgba(15,45,77,0.08);
        padding: 20px 22px;
        margin-bottom: 22px;
    }

    .reportes-filtros form {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 14px;
        align-items: end;
    }

    .reportes-filtros label {
        display: block;
        font-size: 0.8rem;
        color: var(--navy-dark);
        font-weight: 600;
        margin-bottom: 5px;
    }

    .reportes-filtros select,
    .reportes-filtros input[type="date"],
    .reportes-filtros input[type="number"] {
        width: 100%;
        border: 1px solid #e2ddd0;
        border-radius: 10px;
        padding: 10px 12px;
        font-size: 0.9rem;
        background: #fff;
    }

    .reportes-filtros select:focus,
    .reportes-filtros input:focus {
        outline: none;
        border-color: var(--dorado-dark);
    }

    .reportes-filtros-acciones {
        display: flex;
        gap: 10px;
        grid-column: 1 / -1;
        justify-content: flex-end;
        flex-wrap: wrap;
        margin-top: 4px;
    }

    .btn-reporte {
        border: none;
        border-radius: 10px;
        padding: 10px 18px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        transition: transform 0.12s ease, box-shadow 0.12s ease;
    }

    .btn-reporte:hover {
        transform: translateY(-1px);
    }

    .btn-filtrar {
        background: var(--navy);
        color: #fff;
    }

    .btn-limpiar {
        background: #f1ede2;
        color: var(--navy-dark);
    }

    .btn-exportar {
        background: var(--dorado-dark);
        color: #fff;
        box-shadow: 0 3px 10px rgba(201,163,68,0.35);
    }

    .reportes-graficas {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 22px;
    }

    .tarjeta-reporte {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 16px rgba(15,45,77,0.08);
        padding: 20px 22px;
    }

    .tarjeta-reporte h3 {
        font-family: Georgia, serif;
        color: var(--navy-dark);
        font-size: 1.05rem;
        margin: 0 0 14px 0;
    }

    .grafico-lienzo {
        position: relative;
        height: 260px;
        overflow-x: auto;
        overflow-y: hidden;
    }

    .grafico-lienzo-inner {
        position: relative;
        height: 100%;
        min-width: 100%;
    }

    /* Scrollbar discreto para los gráficos con desbordamiento horizontal */
    .grafico-lienzo::-webkit-scrollbar {
        height: 8px;
    }

    .grafico-lienzo::-webkit-scrollbar-thumb {
        background: #d8d2c0;
        border-radius: 999px;
    }

    .grafico-lienzo::-webkit-scrollbar-track {
        background: transparent;
    }

    .reportes-tablas {
        display: grid;
        grid-template-columns: 1.3fr 1fr;
        gap: 20px;
    }

    @media (max-width: 900px) {
        .reportes-tablas {
            grid-template-columns: 1fr;
        }
    }

    .tabla-scroll {
        max-height: 420px;
        overflow-y: auto;
        border: 1px solid #ede8db;
        border-radius: 10px;
    }

    table.tabla-reporte {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.88rem;
    }

    table.tabla-reporte thead th {
        position: sticky;
        top: 0;
        background: var(--crema);
        color: var(--navy-dark);
        text-align: left;
        padding: 10px 14px;
        font-weight: 600;
        border-bottom: 1px solid #ede8db;
        z-index: 1;
    }

    table.tabla-reporte tbody td {
        padding: 9px 14px;
        border-bottom: 1px solid #f2efe6;
        color: #3d3a30;
    }

    table.tabla-reporte tbody tr:last-child td {
        border-bottom: none;
    }

    table.tabla-reporte tbody tr:hover {
        background: #faf8f2;
    }

    .badge-total {
        display: inline-block;
        min-width: 26px;
        text-align: center;
        padding: 3px 9px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 0.82rem;
        background: #eef3ee;
        color: var(--verde);
    }

    .badge-critico {
        background: #fbeaea;
        color: #a13a3a;
    }

    .sin-datos {
        padding: 22px;
        text-align: center;
        color: #928d7c;
        font-size: 0.9rem;
    }

    .umbral-nota {
        font-size: 0.8rem;
        color: #857f6d;
        margin-bottom: 10px;
    }

    .reportes-aviso {
        background: #fdf6e3;
        border: 1px solid #e6d9a8;
        color: #6b5a1f;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 0.85rem;
        margin-bottom: 16px;
    }
</style>
@endpush

<div class="reportes-filtros">
    @if (session('reporte_aviso'))
        <div class="reportes-aviso">⚠ {{ session('reporte_aviso') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.reportes.index') }}">
        <div>
            <label for="f-nivel">Nivel</label>
            <select name="nivel" id="f-nivel">
                <option value="">Todos</option>
                @for ($n = 7; $n <= 11; $n++)
                    <option value="{{ $n }}" @selected(request('nivel') == $n)>{{ $n }}°</option>
                @endfor
            </select>
        </div>

        <div>
            <label for="f-seccion">Sección</label>
            <select name="seccion_id" id="f-seccion">
                <option value="">Todas</option>
                @foreach ($secciones as $seccion)
                    <option
                        value="{{ $seccion->id }}"
                        data-nivel="{{ $seccion->nivel }}"
                        @selected(request('seccion_id') == $seccion->id)
                    >
                        {{ $seccion->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="f-materia">Materia</label>
            <select name="materia_id" id="f-materia">
                <option value="">Todas</option>
                @foreach ($materias as $materia)
                    <option value="{{ $materia->id }}" @selected(request('materia_id') == $materia->id)>
                        {{ $materia->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="f-docente">Docente</label>
            <select name="docente_id" id="f-docente">
                <option value="">Todos</option>
                @foreach ($docentes as $docente)
                    <option value="{{ $docente->id }}" @selected(request('docente_id') == $docente->id)>
                        {{ $docente->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="f-desde">Desde</label>
            <input type="date" name="desde" id="f-desde" value="{{ $desde }}">
        </div>

        <div>
            <label for="f-hasta">Hasta</label>
            <input type="date" name="hasta" id="f-hasta" value="{{ $hasta }}">
        </div>

        <div>
            <label for="f-umbral">Umbral crónico</label>
            <input type="number" name="umbral" id="f-umbral" min="1" value="{{ $umbral }}">
        </div>

        <div class="reportes-filtros-acciones">
            <a href="{{ route('admin.reportes.index') }}" class="btn-reporte btn-limpiar">Limpiar</a>
            <button type="submit" class="btn-reporte btn-filtrar">Filtrar</button>
            <a href="{{ route('admin.reportes.exportar', request()->query()) }}" class="btn-reporte btn-exportar">
                ⬇ Exportar a Excel
            </a>
        </div>
    </form>
</div>

<div class="reportes-graficas">
    <div class="tarjeta-reporte">
        <h3>Ausencias por sección</h3>
        <div class="grafico-lienzo">
            <div class="grafico-lienzo-inner" id="wrapSeccion">
                <canvas id="graficoSeccion"></canvas>
            </div>
        </div>
    </div>

    <div class="tarjeta-reporte">
        <h3>Ausencias por nivel</h3>
        <div class="grafico-lienzo">
            <canvas id="graficoNivel"></canvas>
        </div>
    </div>

    <div class="tarjeta-reporte">
        <h3>Tendencia diaria</h3>
        <div class="grafico-lienzo">
            <div class="grafico-lienzo-inner" id="wrapTendencia">
                <canvas id="graficoTendencia"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="reportes-tablas">
    <div class="tarjeta-reporte">
        <h3>Ranking de estudiantes (top 50)</h3>
        <div class="tabla-scroll">
            @if ($ranking->isEmpty())
                <div class="sin-datos">No hay registros con estos filtros.</div>
            @else
                <table class="tabla-reporte">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Sección</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ranking as $fila)
                            <tr>
                                <td>{{ $fila->estudiante->nombre_completo ?? '—' }}</td>
                                <td>{{ $fila->estudiante->seccion->nombre ?? '—' }}</td>
                                <td>
                                    <span class="badge-total @if($fila->total >= $umbral) badge-critico @endif">
                                        {{ $fila->total }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div class="tarjeta-reporte">
        <h3>Ausentismo crónico</h3>
        <div class="umbral-nota">Estudiantes con {{ $umbral }} o más ausencias en el período filtrado.</div>
        <div class="tabla-scroll">
            @if ($cronicos->isEmpty())
                <div class="sin-datos">Nadie supera el umbral actual.</div>
            @else
                <table class="tabla-reporte">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cronicos as $fila)
                            <tr>
                                <td>{{ $fila->estudiante->nombre_completo ?? '—' }}</td>
                                <td><span class="badge-total badge-critico">{{ $fila->total }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/chart.umd.min.js') }}"></script>
<script>
    const coloresPalette = {
        navy: '#163d63',
        navyDark: '#0f2d4d',
        dorado: '#c9a344',
        verde: '#3a6b47',
        crema: '#f7f4ec',
    };

    Chart.defaults.font.family = "-apple-system, 'Segoe UI', sans-serif";
    Chart.defaults.color = '#5a5648';

    // --- Filtrar secciones según el nivel seleccionado ---
    const selectNivel = document.getElementById('f-nivel');
    const selectSeccion = document.getElementById('f-seccion');
    const opcionesSeccion = Array.from(selectSeccion.options);

    function filtrarSecciones() {
        const nivel = selectNivel.value;
        const seccionActual = selectSeccion.value;

        selectSeccion.innerHTML = '';

        opcionesSeccion.forEach(opt => {
            const esTodas = opt.value === '';
            const coincideNivel = !nivel || opt.dataset.nivel === nivel;

            if (esTodas || coincideNivel) {
                selectSeccion.appendChild(opt);
            }
        });

        // Si la sección que estaba seleccionada ya no aplica al nivel, resetear a "Todas"
        const sigueDisponible = Array.from(selectSeccion.options)
            .some(opt => opt.value === seccionActual);

        selectSeccion.value = sigueDisponible ? seccionActual : '';
    }

    selectNivel.addEventListener('change', filtrarSecciones);

    // Aplicar al cargar la página (por si viene un nivel ya seleccionado desde la URL)
    filtrarSecciones();

    // --- Ausencias por sección (barra) ---
    const datosSeccion = @json($porSeccion->map(fn($f) => [
        'label' => $f->seccion->nombre ?? '—',
        'total' => (int) $f->total,
    ]));

    // Ancho mínimo por barra para que no se compriman cuando hay muchas secciones
    const anchoPorBarra = 42;
    const wrapSeccion = document.getElementById('wrapSeccion');
    wrapSeccion.style.width = Math.max(
        wrapSeccion.parentElement.clientWidth,
        datosSeccion.length * anchoPorBarra
    ) + 'px';

    new Chart(document.getElementById('graficoSeccion'), {
        type: 'bar',
        data: {
            labels: datosSeccion.map(d => d.label),
            datasets: [{
                label: 'Ausencias',
                data: datosSeccion.map(d => d.total),
                backgroundColor: coloresPalette.navy,
                borderRadius: 6,
            }],
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
        },
    });

    // --- Ausencias por nivel (doughnut) ---
    const datosNivel = @json($porNivel->map(fn($f) => [
        'label' => $f->nivel . '°',
        'total' => (int) $f->total,
    ]));

    new Chart(document.getElementById('graficoNivel'), {
        type: 'doughnut',
        data: {
            labels: datosNivel.map(d => d.label),
            datasets: [{
                data: datosNivel.map(d => d.total),
                backgroundColor: [
                    coloresPalette.navy,
                    coloresPalette.dorado,
                    coloresPalette.verde,
                    coloresPalette.navyDark,
                    '#a37b3f',
                ],
                borderWidth: 2,
                borderColor: '#fff',
            }],
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } },
        },
    });

    // --- Tendencia diaria (línea) ---
    const datosTendencia = @json($tendenciaDiaria->map(fn($f) => [
        'fecha' => \Carbon\Carbon::parse($f->fecha)->format('d/m'),
        'total' => (int) $f->total,
    ]));

    // Ancho mínimo por punto para que la línea no se aplaste con rangos largos
    const anchoPorPunto = 45;
    const wrapTendencia = document.getElementById('wrapTendencia');
    wrapTendencia.style.width = Math.max(
        wrapTendencia.parentElement.clientWidth,
        datosTendencia.length * anchoPorPunto
    ) + 'px';

    new Chart(document.getElementById('graficoTendencia'), {
        type: 'line',
        data: {
            labels: datosTendencia.map(d => d.fecha),
            datasets: [{
                label: 'Ausencias',
                data: datosTendencia.map(d => d.total),
                borderColor: coloresPalette.dorado,
                backgroundColor: 'rgba(224,190,107,0.18)',
                fill: true,
                tension: 0.3,
                pointRadius: 3,
            }],
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
        },
    });
</script>
@endpush