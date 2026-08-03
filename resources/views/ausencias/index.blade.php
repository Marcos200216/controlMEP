<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Registrar ausencias</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<script defer src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    * { box-sizing: border-box; }
:root {
    --navy: #0f2d4d;
    --navy-light: #163d63;
    --gold: #c9a344;
    --gold-light: #e0be6b;
    --green: #3a6b47;
    --cream: #f7f4ec;
    --text: #1f2937;
    --muted: #64748b;
    --border: #e5e7eb;
    --dia-seleccionable-bg: #e8f0e9;
    --dia-seleccionable-border: var(--green);
}
body {
    margin: 0;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    background: var(--cream);
    color: var(--text);
    padding: 1rem 1rem 3rem;
}
.franja {
    max-width: 960px;
    margin: 0 auto 1.25rem;
    background: var(--navy);
    color: #fff;
    border-radius: 14px;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 8px 20px -8px rgba(15,45,77,0.4);
}
.franja h1 { font-size: 1rem; font-weight: 600; margin: 0; }
.franja a { color: var(--gold-light); font-size: 0.8rem; text-decoration: none; }

.contenedor { max-width: 960px; margin: 0 auto; }

.tarjeta {
    background: #fff;
    border-radius: 16px;
    border: 1px solid var(--border);
    padding: 1.25rem;
    margin-bottom: 1.25rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}

.fila-selects {
    display: flex;
    gap: 0.9rem;
    flex-wrap: wrap;
    align-items: flex-end;
}
.campo { flex: 1 1 190px; min-width: 160px; }
.campo label {
    display: block;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.4px;
    margin-bottom: 0.35rem;
}
select { width: 100%; }

.btn-estudiante {
    flex: 1 1 190px;
    min-width: 160px;
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}
.btn-estudiante label {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.4px;
}
.btn-estudiante button {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 0.85rem;
    border-radius: 10px;
    border: 1.5px solid var(--gold-light);
    background: #fdf9ef;
    color: var(--navy);
    font-weight: 600;
    font-size: 0.88rem;
    cursor: pointer;
    text-align: left;
    width: 100%;
}
.btn-estudiante button:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-estudiante button:not(:disabled):hover { background: #faf3e0; }
.icono-persona { flex-shrink: 0; }

.oculto { display: none !important; }

/* --- Calendario centrado + notita de advertencia anclada en la esquina --- */
#bloque-calendario {
    position: relative;
}
.calendario-layout {
    display: block;
}
.calendario-central {
    max-width: 440px;
    margin: 0 auto;
}
.panel-advertencia {
    display: none;
    position: absolute;
    top: 1rem;
    right: 1rem;
    max-width: 210px;
    gap: 0.4rem;
    align-items: flex-start;
    background: #fdf9ef;
    border: 1px solid var(--gold-light);
    border-radius: 10px;
    padding: 0.5rem 0.65rem;
    font-size: 0.68rem;
    line-height: 1.3;
    color: var(--navy);
    box-shadow: 0 4px 14px -6px rgba(15,45,77,0.25);
}
.panel-advertencia.visible { display: flex; }
.panel-advertencia svg { flex-shrink: 0; width: 14px; height: 14px; margin-top: 0.1rem; color: var(--gold); }
.panel-advertencia strong { color: var(--navy); }

.calendario-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.85rem;
}
.calendario-header h2 {
    font-size: 0.95rem;
    font-weight: 600;
    margin: 0;
    color: var(--navy);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 220px;
}
.mes-nav { display: flex; align-items: center; gap: 0.6rem; }
.mes-nav button {
    width: 30px; height: 30px; border-radius: 8px;
    border: 1px solid var(--border); background: #fff; font-size: 1rem; cursor: pointer;
}
.mes-nav button:disabled { opacity: 0.3; cursor: not-allowed; }
.mes-nav span { font-size: 0.88rem; font-weight: 500; }

.dias-header, .grid-dias {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 5px;
}
.dias-header div { text-align: center; font-size: 0.7rem; color: #9ca3af; padding: 4px 0; }
.dia {
    position: relative;
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    font-size: 0.85rem;
    border: 1px solid var(--border);
    background: #fff;
    color: #cbd5e1;
    cursor: default;
}
.dia.futuro { color: #d1d5db; border-color: transparent; cursor: not-allowed; }
.dia.fin-semana { color: #d1d5db; border-color: transparent; cursor: not-allowed; background: transparent; }
.dia.vacio { visibility: hidden; }
.dia.seleccionable {
    background: var(--dia-seleccionable-bg);
    border: 2px solid var(--dia-seleccionable-border);
    color: var(--navy);
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 1px 3px rgba(58,107,71,0.2);
}
.dia.seleccionable:hover {
    background: var(--navy);
    border-color: var(--navy);
    color: #fff;
    box-shadow: none;
}
.badge {
    position: absolute; top: -5px; right: -5px;
    width: 16px; height: 16px; border-radius: 50%;
    background: #b91c1c; color: #fff; font-size: 0.62rem;
    display: flex; align-items: center; justify-content: center;
}
.badge.borrador { background: var(--gold); color: var(--navy); }

.fila-obs-guardar {
    display: flex;
    gap: 0.7rem;
    align-items: stretch;
    flex-wrap: wrap;
    margin-top: 1.1rem;
}
textarea {
    flex: 1 1 100%;
    padding: 0.7rem;
    border-radius: 10px;
    border: 1px solid #d1d5db;
    font-size: 0.85rem;
    resize: none;
    font-family: inherit;
    min-height: 60px;
}
.btn-guardar-datos {
    flex: 1 1 100%;
    padding: 0.7rem;
    border: none;
    border-radius: 10px;
    background: var(--navy);
    color: #fff;
    font-weight: 600;
    font-size: 0.88rem;
    cursor: pointer;
}
.btn-guardar-datos:disabled { background: #9ca3af; cursor: not-allowed; }
.btn-guardar-datos:not(:disabled):hover { background: var(--navy-light); }

.tarjeta-agregados {
    display: flex;
}
.caja-agregados {
    display: flex;
    width: 100%;
}
.panel-lista {
    flex: 1 1 300px;
    min-width: 0;
    padding-right: 1.25rem;
    border-right: 1px solid var(--border);
}
.panel-lista p.contador { font-size: 0.82rem; color: var(--muted); margin: 0 0 0.6rem; }
#lista-chips-contenido {
    max-height: 170px;
    overflow-y: auto;
    padding-right: 0.4rem;
}
.panel-enviar {
    flex: 0 0 190px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding-left: 1.25rem;
}

/* --- En móvil: apilar lista y botón en vertical, divisor abajo en vez de al lado --- */
@media (max-width: 600px) {
    .caja-agregados {
        flex-direction: column;
    }
    .panel-lista {
        padding-right: 0;
        padding-bottom: 1rem;
        border-right: none;
        border-bottom: 1px solid var(--border);
        flex: 0 1 auto;
    }
    .panel-enviar {
        flex: 0 0 auto;
        padding-left: 0;
        padding-top: 1rem;
    }
    .btn-enviar {
        max-width: 100%;
    }
    /* La notita de advertencia pasa a ocupar el ancho completo, arriba del calendario */
    .panel-advertencia {
        position: static;
        max-width: 100%;
        margin-bottom: 0.85rem;
        font-size: 0.72rem;
    }
}

.chip {
    display: flex; align-items: center; justify-content: space-between;
    padding: 0.55rem 0.75rem; background: #f8fafc; border: 1px solid var(--border);
    border-radius: 9px; margin-bottom: 0.5rem; font-size: 0.85rem;
}
.chip-nombre { display: flex; align-items: center; gap: 0.55rem; }
.avatar {
    width: 26px; height: 26px; border-radius: 50%;
    background: var(--navy); color: var(--gold-light);
    font-size: 0.68rem; font-weight: 700;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.chip button {
    border: none; background: none; color: #9ca3af; font-size: 1rem; cursor: pointer; padding: 0 0.25rem;
}
.chip button:hover { color: #b91c1c; }
.btn-enviar {
    width: 100%;
    max-width: 170px;
    padding: 0.8rem;
    border: none;
    border-radius: 10px;
    background: var(--green);
    color: #fff;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
}
.btn-enviar:disabled { background: #9ca3af; cursor: not-allowed; }
.btn-enviar:not(:disabled):hover { filter: brightness(1.08); }

/* --- Ajustes específicos para pantallas grandes (desktop) --- */
@media (min-width: 700px) {
    .fila-obs-guardar {
        flex-wrap: nowrap;
    }
    textarea {
        flex: 1 1 auto;
    }
    .btn-guardar-datos {
        flex: 0 0 160px;
        align-self: stretch;
    }
}

/* overlays / modales */
.overlay {
    display: none; position: fixed; inset: 0; background: rgba(15,45,77,0.55);
    align-items: center; justify-content: center; z-index: 20; padding: 1rem;
}
.overlay.activo { display: flex; }

.modal-dia {
    background: #fff; border-radius: 16px; padding: 1.25rem; width: 100%; max-width: 280px;
    border-top: 4px solid var(--gold);
}
.modal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.2rem; }
.modal-header span { font-size: 0.9rem; font-weight: 600; color: var(--navy); }
.modal-header button { border: none; background: none; font-size: 1.1rem; color: #9ca3af; cursor: pointer; }
.modal-dia p.info { font-size: 0.78rem; color: var(--muted); margin: 0 0 0.85rem; }
.pills { display: flex; gap: 0.5rem; justify-content: center; margin-bottom: 0.75rem; }
.pill {
    width: 42px; height: 42px; border-radius: 50%; border: 1px solid #d1d5db;
    background: #fff; font-weight: 600; font-size: 0.95rem; cursor: pointer;
}
.pill.activo { background: var(--navy); color: #fff; border-color: var(--navy); }
.eliminar-dia {
    width: 100%; padding: 0.65rem; border: 1px solid #fecaca; border-radius: 10px;
    background: #fff; color: #b91c1c; font-size: 0.85rem; cursor: pointer; display: none;
}
.eliminar-dia.visible { display: block; }

.modal-estudiantes {
    background: #fff; border-radius: 16px; width: 100%; max-width: 460px;
    max-height: 85vh; display: flex; flex-direction: column;
    border-top: 4px solid var(--gold);
}
.modal-estudiantes-header { padding: 1.1rem 1.1rem 0.6rem; }
.modal-estudiantes-header h3 { margin: 0 0 0.7rem; font-size: 1rem; color: var(--navy); }
.modal-estudiantes-header input {
    width: 100%; padding: 0.65rem 0.8rem; border-radius: 10px; border: 1px solid var(--border);
    font-size: 0.9rem;
}
.lista-modal { overflow-y: auto; padding: 0.4rem 0.6rem 1rem; }
.item-modal-estudiante {
    display: flex; align-items: center; gap: 0.7rem;
    padding: 0.7rem 0.6rem; border-radius: 10px; cursor: pointer; font-size: 0.9rem;
}
.item-modal-estudiante:hover { background: #fdf9ef; }
.item-modal-estudiante .agregado-check { margin-left: auto; color: var(--green); font-size: 0.8rem; font-weight: 600; }
.cerrar-modal-estudiantes {
    border: none; background: none; font-size: 1.2rem; color: #9ca3af; cursor: pointer;
    position: absolute; top: 0.9rem; right: 1rem;
}
.modal-estudiantes { position: relative; }
</style>
</head>
<body>

<div class="franja">
    <h1>Registrar ausencias</h1>
    <a href="{{ route('inicio') }}">← Inicio</a>
</div>

<div class="contenedor">

    <div class="tarjeta">
        <div class="fila-selects">
            <div class="campo">
                <label for="docente_id">Docente</label>
                <select id="docente_id">
                    <option value="">Selecciona</option>
                    @foreach ($docentes as $docente)
                        <option value="{{ $docente->id }}">{{ $docente->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="campo">
                <label for="materia_id">Materia</label>
                <select id="materia_id">
                    <option value="">Selecciona</option>
                    @foreach ($materias as $materia)
                        <option value="{{ $materia->id }}">{{ $materia->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="campo">
                <label for="seccion_id">Sección</label>
                <select id="seccion_id">
                    <option value="">Selecciona</option>
                    @foreach ($secciones as $seccion)
                        <option value="{{ $seccion->id }}">{{ $seccion->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="btn-estudiante">
                <label>Estudiante</label>
                <button id="btn-abrir-estudiantes" disabled type="button">
                    <svg class="icono-persona" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                    <span id="texto-estudiante-seleccionado">Seleccionar estudiante</span>
                </button>
            </div>
        </div>
    </div>

    <div class="tarjeta" id="bloque-calendario">
        <div class="calendario-layout">
            <div class="calendario-central">
                <div class="calendario-header">
                    <h2 id="titulo-estudiante-calendario">Calendario</h2>
                    <div class="mes-nav">
                        <button id="btn-mes-anterior">‹</button>
                        <span id="titulo-mes"></span>
                        <button id="btn-mes-siguiente" disabled>›</button>
                    </div>
                </div>
                <div class="dias-header"><div>L</div><div>M</div><div>M</div><div>J</div><div>V</div><div>S</div><div>D</div></div>
                <div class="grid-dias" id="grid-dias"></div>

                <div class="fila-obs-guardar">
                    <textarea id="observacion" placeholder="Observaciones (opcional)"></textarea>
                    <button class="btn-guardar-datos" id="btn-guardar-datos" disabled>Guardar datos</button>
                </div>
            </div>

            <aside class="panel-advertencia" id="panel-advertencia-calendario">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                <p>
                    Solo puedes trabajar con <strong>un estudiante a la vez</strong>. Antes de seleccionar otro estudiante, u otro docente, materia o sección, presiona <strong>«Guardar datos»</strong>; de lo contrario, los días que marques en el calendario no quedarán incluidos en el reporte.
                </p>
            </aside>
        </div>
    </div>

    <div class="tarjeta tarjeta-agregados">
        <div class="caja-agregados">
            <div class="panel-lista">
                <p class="contador"><span id="contador-agregados">0</span> estudiantes agregados a este reporte</p>
                <div id="lista-chips-contenido"></div>
            </div>
            <div class="panel-enviar">
                <button class="btn-enviar" id="btn-enviar-reporte" disabled>Enviar reporte</button>
            </div>
        </div>
    </div>

</div>

<!-- Modal seleccionar estudiante -->
<div class="overlay" id="overlay-estudiantes">
    <div class="modal-estudiantes">
        <button class="cerrar-modal-estudiantes" id="cerrar-modal-estudiantes">✕</button>
        <div class="modal-estudiantes-header">
            <h3>Selecciona un estudiante</h3>
            <input type="text" id="buscar-estudiante" placeholder="Buscar por nombre...">
        </div>
        <div class="lista-modal" id="lista-modal-estudiantes"></div>
    </div>
</div>

<!-- Modal día del calendario -->
<div class="overlay" id="overlay-dia">
    <div class="modal-dia">
        <div class="modal-header">
            <span id="modal-fecha"></span>
            <button id="modal-cerrar">✕</button>
        </div>
        <p class="info">Cantidad de ausencias ese día</p>
        <div class="pills" id="modal-pills">
            <button class="pill" data-n="1">1</button>
            <button class="pill" data-n="2">2</button>
            <button class="pill" data-n="3">3</button>
            <button class="pill" data-n="4">4</button>
            <button class="pill" data-n="5">5</button>
        </div>
        <button class="eliminar-dia" id="modal-eliminar">Eliminar registro</button>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// URLs
const urlDatosEstudiante = "{{ route('ausencias.datosEstudiante') }}";
const urlGuardarDia      = "{{ route('ausencias.dia.guardar') }}";
const urlEliminarDia     = "{{ route('ausencias.dia.eliminar') }}";
const urlGuardarDatos    = "{{ route('ausencias.guardarDatos') }}";
const urlQuitarEstudiante= "{{ route('ausencias.quitarEstudiante') }}";
const urlEnviarReporte   = "{{ route('ausencias.enviar') }}";

// Datos embebidos desde el servidor
const todosLosEstudiantes = @json($estudiantes); // [{id, nombre_completo, seccion_id}, ...]
let agregados = @json($agregadosIniciales ?? []); // { estudiante_id: {nombre, observacion} }

const meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];

let estudianteSeleccionado = null; // {id, nombre_completo}
let ausenciasGuardadas = {};
let ausenciasBorrador = {};
let mesActual = new Date();
mesActual.setDate(1);
let diaSeleccionado = null;

// Valores previos de los selects, para poder revertir si el docente intenta
// cambiarlos con días sin guardar para el estudiante actual.
let valoresPrevios = { docente_id: '', materia_id: '', seccion_id: '' };
let bloqueandoCambioSelect = false;

// Instancias de Choices.js (se declaran aquí para poder resetearlas después de guardar)
let choiceDocente, choiceMateria, choiceSeccion;

function pad(n){ return n.toString().padStart(2,'0'); }
function fechaISO(a,m,d){ return `${a}-${pad(m+1)}-${pad(d)}`; }

function idsSeleccionados() {
    return {
        docente_id: document.getElementById('docente_id').value,
        materia_id: document.getElementById('materia_id').value,
        seccion_id: document.getElementById('seccion_id').value,
    };
}

function actualizarBotonEstudiante() {
    const { docente_id, materia_id, seccion_id } = idsSeleccionados();
    document.getElementById('btn-abrir-estudiantes').disabled = !(docente_id && materia_id && seccion_id);
}

// --- ¿Hay días marcados en el calendario del estudiante actual sin pasar por "Guardar datos"? ---
function haySinGuardar() {
    return !!estudianteSeleccionado && Object.keys(ausenciasBorrador).length > 0;
}

async function advertirCambioSinGuardar() {
    const mensaje = 'Ya marcaste días en el calendario para este estudiante. Presiona «Guardar datos» antes de cambiar de estudiante, docente, materia o sección; de lo contrario esos días no se incluirán en el reporte.';
    if (window.Swal) {
        await Swal.fire({
            icon: 'warning',
            title: 'Guarda los datos primero',
            text: mensaje,
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#0f2d4d',
        });
    } else {
        alert(mensaje);
    }
}

function mostrarAdvertenciaCalendario() {
    document.getElementById('panel-advertencia-calendario').classList.add('visible');
}
function ocultarAdvertenciaCalendario() {
    document.getElementById('panel-advertencia-calendario').classList.remove('visible');
}

// --- Manejo uniforme de errores de red / validación para todas las peticiones ---
async function fetchSeguro(url, opciones = {}) {
    let resp;
    try {
        resp = await fetch(url, opciones);
    } catch (err) {
        if (window.Swal) {
            await Swal.fire({ icon: 'error', title: 'Sin conexión', text: 'No se pudo contactar al servidor. Verifica tu conexión e intenta de nuevo.', confirmButtonColor: '#0f2d4d' });
        } else {
            alert('No se pudo contactar al servidor.');
        }
        return null;
    }

    // Una petición redirigida (ej. enviar reporte) es un éxito, aunque no sea "ok" en el sentido estricto
    if (!resp.ok && !resp.redirected) {
        let mensaje = 'Ocurrió un error al guardar. Intenta de nuevo.';
        try {
            const cuerpo = await resp.clone().json();
            if (cuerpo?.message) mensaje = cuerpo.message;
        } catch (_) { /* no era JSON, se deja el mensaje genérico */ }

        if (window.Swal) {
            await Swal.fire({ icon: 'error', title: 'No se pudo guardar', text: mensaje, confirmButtonColor: '#0f2d4d' });
        } else {
            alert(mensaje);
        }
        return null;
    }

    return resp;
}

['docente_id','materia_id','seccion_id'].forEach(id => {
    document.getElementById(id).addEventListener('change', async (e) => {
        if (bloqueandoCambioSelect) return; // este 'change' lo disparó nuestro propio revert

        if (haySinGuardar()) {
            bloqueandoCambioSelect = true;
            const anterior = valoresPrevios[id] || '';
            const choiceInstancia = { docente_id: choiceDocente, materia_id: choiceMateria, seccion_id: choiceSeccion }[id];
            if (choiceInstancia) {
                choiceInstancia.setChoiceByValue(anterior);
            } else {
                e.target.value = anterior;
            }
            bloqueandoCambioSelect = false;

            await advertirCambioSinGuardar();
            return;
        }

        valoresPrevios[id] = e.target.value;

        // Cambiaron los filtros sin haber marcado días: el estudiante que estaba
        // seleccionado ya no corresponde al nuevo contexto, así que se limpia.
        if (estudianteSeleccionado) {
            resetearSeleccionEstudianteYCalendario();
        }

        actualizarBotonEstudiante();
    });
});

// --- Modal de estudiantes ---
document.getElementById('btn-abrir-estudiantes').addEventListener('click', async () => {
    if (haySinGuardar()) {
        await advertirCambioSinGuardar();
        return;
    }

    const { seccion_id } = idsSeleccionados();
    const lista = todosLosEstudiantes.filter(e => e.seccion_id == seccion_id);
    pintarListaModalEstudiantes(lista);
    document.getElementById('buscar-estudiante').value = '';
    document.getElementById('overlay-estudiantes').classList.add('activo');
});

function pintarListaModalEstudiantes(lista, filtro = '') {
    const cont = document.getElementById('lista-modal-estudiantes');
    cont.innerHTML = '';
    const filtrada = lista.filter(e => e.nombre_completo.toLowerCase().includes(filtro.toLowerCase()));
    filtrada.forEach(e => {
        const div = document.createElement('div');
        div.className = 'item-modal-estudiante';
        div.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0f2d4d" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
            <span>${e.nombre_completo}</span>
            ${agregados[e.id] ? '<span class="agregado-check">agregado ✓</span>' : ''}
        `;
        div.onclick = () => seleccionarEstudiante(e);
        cont.appendChild(div);
    });
}

document.getElementById('buscar-estudiante').addEventListener('input', (e) => {
    const { seccion_id } = idsSeleccionados();
    const lista = todosLosEstudiantes.filter(x => x.seccion_id == seccion_id);
    pintarListaModalEstudiantes(lista, e.target.value);
});

document.getElementById('cerrar-modal-estudiantes').onclick = () => {
    document.getElementById('overlay-estudiantes').classList.remove('activo');
};

async function seleccionarEstudiante(estudiante) {
    estudianteSeleccionado = estudiante;
    document.getElementById('overlay-estudiantes').classList.remove('activo');
    document.getElementById('texto-estudiante-seleccionado').textContent = estudiante.nombre_completo;
    document.getElementById('titulo-estudiante-calendario').textContent = estudiante.nombre_completo;
    document.getElementById('observacion').value = '';
    document.getElementById('btn-guardar-datos').disabled = false;
    mostrarAdvertenciaCalendario();

    const { docente_id, materia_id } = idsSeleccionados();
    const params = new URLSearchParams({ estudiante_id: estudiante.id, docente_id, materia_id });
    const resp = await fetchSeguro(`${urlDatosEstudiante}?${params}`);
    if (!resp) {
        ausenciasGuardadas = {};
        ausenciasBorrador = {};
        pintarCalendario();
        return;
    }
    const datos = await resp.json();
    ausenciasGuardadas = datos.guardadas || {};
    ausenciasBorrador = datos.borrador || {};
    pintarCalendario();
}

// --- Calendario ---
function pintarCalendario() {
    const anio = mesActual.getFullYear();
    const mes = mesActual.getMonth();
    document.getElementById('titulo-mes').textContent = `${meses[mes]} ${anio}`;
    const grid = document.getElementById('grid-dias');
    grid.innerHTML = '';

    const primerDiaSemana = (new Date(anio, mes, 1).getDay() + 6) % 7;
    const diasEnMes = new Date(anio, mes + 1, 0).getDate();
    const hoy = new Date(); hoy.setHours(0,0,0,0);

    for (let i = 0; i < primerDiaSemana; i++) {
        const vacio = document.createElement('div');
        vacio.className = 'dia vacio';
        grid.appendChild(vacio);
    }

    for (let dia = 1; dia <= diasEnMes; dia++) {
    const fechaDia = new Date(anio, mes, dia);
    const iso = fechaISO(anio, mes, dia);
    const celda = document.createElement('div');
    celda.className = 'dia';
    celda.textContent = dia;

    const diaSemana = fechaDia.getDay(); // 0 = domingo, 6 = sábado
    const esFinDeSemana = (diaSemana === 0 || diaSemana === 6);

    if (fechaDia > hoy) {
        celda.classList.add('futuro');
    } else if (esFinDeSemana) {
        celda.classList.add('fin-semana');
    } else if (estudianteSeleccionado) {
        celda.classList.add('seleccionable');
        celda.onclick = () => abrirModalDia(iso, dia, mes, anio);
    } else {
        celda.style.cursor = 'not-allowed';
    }

    const cantidadBorrador = ausenciasBorrador[iso];
    const cantidadGuardada = ausenciasGuardadas[iso];
    if (cantidadBorrador) {
        const badge = document.createElement('span');
        badge.className = 'badge borrador';
        badge.textContent = cantidadBorrador;
        celda.appendChild(badge);
    } else if (cantidadGuardada) {
        const badge = document.createElement('span');
        badge.className = 'badge';
        badge.textContent = cantidadGuardada;
        celda.appendChild(badge);
    }
    grid.appendChild(celda);
}
    document.getElementById('btn-mes-siguiente').disabled = (anio === hoy.getFullYear() && mes === hoy.getMonth());
}

function abrirModalDia(iso, dia, mes, anio) {
    diaSeleccionado = iso;
    document.getElementById('modal-fecha').textContent = `${dia} de ${meses[mes]}, ${anio}`;
    const actual = ausenciasBorrador[iso] || ausenciasGuardadas[iso];
    document.querySelectorAll('.pill').forEach(p => p.classList.toggle('activo', parseInt(p.dataset.n) === actual));
    document.getElementById('modal-eliminar').classList.toggle('visible', !!actual);
    document.getElementById('overlay-dia').classList.add('activo');
}
function cerrarModalDia() {
    document.getElementById('overlay-dia').classList.remove('activo');
    diaSeleccionado = null;
}
document.getElementById('modal-cerrar').onclick = cerrarModalDia;

document.querySelectorAll('.pill').forEach(pill => {
    pill.onclick = async () => {
        const cantidad = parseInt(pill.dataset.n);
        const { docente_id, materia_id } = idsSeleccionados();
        const resp = await fetchSeguro(urlGuardarDia, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ estudiante_id: estudianteSeleccionado.id, docente_id, materia_id, fecha: diaSeleccionado, cantidad }),
        });
        if (!resp) return; // no se guardó: no se toca el estado local ni se cierra el modal

        ausenciasBorrador[diaSeleccionado] = cantidad;
        cerrarModalDia();
        pintarCalendario();
    };
});

document.getElementById('modal-eliminar').onclick = async () => {
    const { docente_id, materia_id } = idsSeleccionados();
    const resp = await fetchSeguro(urlEliminarDia, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ estudiante_id: estudianteSeleccionado.id, docente_id, materia_id, fecha: diaSeleccionado }),
    });
    if (!resp) return;

    delete ausenciasBorrador[diaSeleccionado];
    cerrarModalDia();
    pintarCalendario();
};

document.getElementById('btn-mes-anterior').onclick = () => { mesActual.setMonth(mesActual.getMonth() - 1); pintarCalendario(); };
document.getElementById('btn-mes-siguiente').onclick = () => { mesActual.setMonth(mesActual.getMonth() + 1); pintarCalendario(); };

// --- Reinicia solo estudiante + calendario (selects se mantienen) ---
// Se usa cuando el docente cambia de filtros SIN días marcados: el estudiante
// anterior ya no aplica al nuevo contexto, pero no se perdió nada que guardar.
function resetearSeleccionEstudianteYCalendario() {
    estudianteSeleccionado = null;
    ausenciasGuardadas = {};
    ausenciasBorrador = {};
    document.getElementById('texto-estudiante-seleccionado').textContent = 'Seleccionar estudiante';
    document.getElementById('titulo-estudiante-calendario').textContent = 'Calendario';
    document.getElementById('observacion').value = '';
    document.getElementById('btn-guardar-datos').disabled = true;
    document.getElementById('grid-dias').innerHTML = '';
    ocultarAdvertenciaCalendario();
}

// --- Reinicia TODO el formulario (selects incluidos) tras "Guardar datos" ---
function reiniciarFormularioCompleto() {
    // Selects (vía Choices.js)
    if (choiceDocente) choiceDocente.removeActiveItems();
    if (choiceMateria) choiceMateria.removeActiveItems();
    if (choiceSeccion) choiceSeccion.removeActiveItems();
    valoresPrevios = { docente_id: '', materia_id: '', seccion_id: '' };

    resetearSeleccionEstudianteYCalendario();
    document.getElementById('btn-abrir-estudiantes').disabled = true;
}

// --- Guardar datos (finaliza al estudiante actual y lo agrega a la caja) ---
document.getElementById('btn-guardar-datos').onclick = async () => {
    if (!estudianteSeleccionado) return;
    const { docente_id, materia_id, seccion_id } = idsSeleccionados();
    const observacion = document.getElementById('observacion').value;

    const resp = await fetchSeguro(urlGuardarDatos, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ estudiante_id: estudianteSeleccionado.id, docente_id, materia_id, seccion_id, observacion }),
    });
    if (!resp) return;

    const data = await resp.json();

    agregados[data.id] = { nombre: data.nombre, observacion };
    pintarChips();

    reiniciarFormularioCompleto();

    if (window.Swal) {
        Swal.fire({ icon: 'success', title: `${data.nombre} agregado`, timer: 1400, showConfirmButton: false });
    }
};

// --- Chips de agregados ---
function pintarChips() {
    const cont = document.getElementById('lista-chips-contenido');
    cont.innerHTML = '';
    const ids = Object.keys(agregados);
    document.getElementById('contador-agregados').textContent = ids.length;
    document.getElementById('btn-enviar-reporte').disabled = ids.length === 0;

    ids.forEach(id => {
        const datos = agregados[id];
        const chip = document.createElement('div');
        chip.className = 'chip';
        chip.innerHTML = `
            <div class="chip-nombre">
                <span class="avatar">${datos.nombre.substring(0,2).toUpperCase()}</span>
                ${datos.nombre}
            </div>
            <button data-id="${id}">✕</button>
        `;
        chip.querySelector('button').onclick = () => quitarEstudiante(id);
        cont.appendChild(chip);
    });
}

async function quitarEstudiante(id) {
    const confirmar = window.Swal
        ? (await Swal.fire({ title: '¿Quitar este estudiante del reporte?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Sí, quitar', cancelButtonText: 'Cancelar', confirmButtonColor: '#0f2d4d' })).isConfirmed
        : confirm('¿Quitar este estudiante del reporte?');
    if (!confirmar) return;

    const resp = await fetchSeguro(urlQuitarEstudiante, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ estudiante_id: id }),
    });
    if (!resp) return;

    delete agregados[id];
    pintarChips();
}

// --- Enviar reporte ---
// --- Enviar reporte ---
document.getElementById('btn-enviar-reporte').onclick = async () => {
    const confirmar = window.Swal
        ? (await Swal.fire({ title: '¿Enviar el reporte?', text: 'No podrás editarlo después de enviarlo.', icon: 'question', showCancelButton: true, confirmButtonText: 'Sí, enviar', cancelButtonText: 'Cancelar', confirmButtonColor: '#0f2d4d' })).isConfirmed
        : confirm('¿Enviar el reporte?');
    if (!confirmar) return;

    const botonEnviar = document.getElementById('btn-enviar-reporte');
    botonEnviar.disabled = true;

    const resp = await fetchSeguro(urlEnviarReporte, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    });

    if (!resp) {
        botonEnviar.disabled = false; // se pudo reintentar, no se perdió el reporte
        return;
    }

    if (resp.redirected) {
        if (window.Swal) {
            await Swal.fire({
                icon: 'success',
                title: 'Reporte enviado',
                text: 'El reporte de ausencias se envió correctamente.',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#0f2d4d',
            });
        } else {
            alert('El reporte de ausencias se envió correctamente.');
        }
        window.location.href = resp.url;
    } else {
        botonEnviar.disabled = false;
    }
};

// --- Choices.js en los selects ---
document.addEventListener('DOMContentLoaded', () => {
    choiceDocente = new Choices(document.getElementById('docente_id'), { searchEnabled: true, itemSelectText: '', shouldSort: false });
    choiceMateria = new Choices(document.getElementById('materia_id'), { searchEnabled: true, itemSelectText: '', shouldSort: false });
    choiceSeccion = new Choices(document.getElementById('seccion_id'), { searchEnabled: true, itemSelectText: '', shouldSort: false });
    pintarChips();
});
</script>

</body>
</html>