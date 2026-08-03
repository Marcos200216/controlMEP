@extends('admin.layout')

@section('title', 'Materias')

@section('content')
    <div class="barra-docentes">
        <div class="buscador-wrap">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" id="buscador" placeholder="Buscar materia..." oninput="filtrarMaterias()">
        </div>
        <button class="btn-nuevo" onclick="abrirModalNuevo()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Nueva materia
        </button>
    </div>

    <div class="tarjeta-tabla">
        <div class="tabla-scroll">
            <table class="tabla-docentes">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th class="col-acciones">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-materias">
                    @forelse($materias as $materia)
                        <tr class="fila-materia" data-nombre="{{ strtolower($materia->nombre) }}">
                            <td data-label="Nombre">{{ $materia->nombre }}</td>
                            <td data-label="Acciones" class="col-acciones">
                                <div class="acciones">
                                    <button class="btn-icono-solo btn-editar" onclick="abrirModalEditar('{{ $materia->id }}', {{ json_encode($materia->nombre) }})" title="Editar">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                                        </svg>
                                    </button>
                                    <button class="btn-icono-solo btn-eliminar" onclick="confirmarEliminar('{{ $materia->id }}')" title="Eliminar">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                            <line x1="10" y1="11" x2="10" y2="17"/>
                                            <line x1="14" y1="11" x2="14" y2="17"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="fila-vacia">
                            <td colspan="2" class="fila-vacia-texto">No hay materias registradas todavía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <p id="sin-resultados" class="sin-resultados">No se encontraron materias con ese nombre.</p>

    {{-- Modal: Nueva / Editar materia --}}
    <div id="modal-materia" class="modal-overlay">
        <div class="modal-tarjeta">
            <h2 id="modal-titulo" class="modal-titulo">Nueva materia</h2>
            <form id="form-materia" method="POST">
                @csrf
                <input type="hidden" id="metodo-form" name="_method" value="{{ old('_method') }}">
                <input type="hidden" id="input-materia-id" name="materia_id" value="{{ old('materia_id') }}">
                <label class="modal-label">Nombre de la materia</label>
                <input
                    type="text"
                    id="input-nombre"
                    name="nombre"
                    required
                    class="modal-input @error('nombre') modal-input-error @enderror"
                    value="{{ old('nombre') }}"
                >
                @error('nombre')
                    <p class="modal-error">{{ $message }}</p>
                @enderror
                <div class="modal-acciones">
                    <button type="button" onclick="cerrarModal()" class="btn-cancelar">Cancelar</button>
                    <button type="submit" class="btn-guardar">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <form id="form-eliminar" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    @push('styles')
    <style>
        .barra-docentes {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .buscador-wrap {
            position: relative;
            flex: 1;
            min-width: 200px;
            max-width: 320px;
        }
        .buscador-wrap svg {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
        }
        .buscador-wrap input {
            width: 100%;
            padding: 11px 14px 11px 38px;
            border: 1px solid #e2ddd0;
            border-radius: 10px;
            font-size: 14px;
            box-sizing: border-box;
            background: #fff;
            transition: border-color 0.15s;
        }
        .buscador-wrap input:focus {
            outline: none;
            border-color: var(--dorado-dark);
        }
        .btn-nuevo {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--navy-dark);
            color: var(--dorado);
            padding: 11px 20px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: background 0.15s, transform 0.15s;
        }
        .btn-nuevo:hover { background: var(--navy); transform: translateY(-1px); }

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
        .tabla-docentes .col-acciones { text-align: right; width: 200px; }
        .tabla-docentes tbody tr { border-top: 1px solid #f0ede4; transition: background 0.1s; }
        .tabla-docentes tbody tr:hover { background: #fbfaf6; }
        .tabla-docentes td { padding: 13px 20px; }

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
        }
        .btn-editar { background: #eef4fa; color: var(--navy); }
        .btn-editar:hover { background: var(--navy); color: #fff; transform: translateY(-1px); }
        .btn-eliminar { background: #fbeceb; color: #b3261e; }
        .btn-eliminar:hover { background: #b3261e; color: #fff; transform: translateY(-1px); }

        .fila-vacia-texto { padding: 28px 20px; text-align: center; color: #94a3b8; }
        .sin-resultados { display: none; text-align: center; color: #94a3b8; margin-top: 16px; font-size: 14px; }

        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15,45,77,0.45);
            align-items: center;
            justify-content: center;
            z-index: 50;
            padding: 16px;
        }
        .modal-tarjeta {
            background: #fff;
            border-radius: 16px;
            padding: 28px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 45px rgba(15,45,77,0.3);
        }
        .modal-titulo { font-family: Georgia, serif; color: var(--navy-dark); margin: 0 0 20px; font-size: 20px; }
        .modal-label { display: block; font-size: 13px; font-weight: 600; color: var(--navy-dark); margin-bottom: 6px; }
        .modal-input {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #e2ddd0;
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
            margin-bottom: 22px;
        }
        .modal-input:focus { outline: none; border-color: var(--dorado-dark); }
        .modal-input-error {
            border-color: #b3261e;
            margin-bottom: 6px;
        }
        .modal-error {
            color: #b3261e;
            font-size: 12.5px;
            margin: 0 0 18px;
        }
        .modal-acciones { display: flex; gap: 12px; justify-content: flex-end; }
        .btn-cancelar {
            padding: 11px 18px;
            border-radius: 8px;
            border: 1px solid #e2ddd0;
            background: #fff;
            color: var(--navy-dark);
            cursor: pointer;
            font-size: 14px;
        }
        .btn-guardar {
            padding: 11px 22px;
            border-radius: 8px;
            border: none;
            background: var(--navy-dark);
            color: var(--dorado);
            font-weight: 600;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-guardar:hover { background: var(--navy); }

        @media (max-width: 640px) {
            .barra-docentes { flex-direction: column; align-items: stretch; }
            .buscador-wrap { max-width: none; }
            .btn-nuevo { justify-content: center; }

            .tabla-docentes thead { display: none; }
            .tabla-docentes, .tabla-docentes tbody {
                display: block;
                width: 100%;
            }
            .tabla-docentes tr.fila-materia {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                border: 1px solid #f0ede4;
                border-radius: 12px;
                margin: 10px 12px;
                padding: 14px 16px;
                background: #fff;
            }
            .tabla-docentes td {
                display: block;
                padding: 0;
                border: none;
            }
            .tabla-docentes td[data-label="Nombre"] {
                font-size: 15px;
                font-weight: 600;
                color: var(--navy-dark);
                white-space: normal;
                word-break: break-word;
                flex: 1;
                min-width: 0;
            }
            .tabla-docentes td.col-acciones { flex-shrink: 0; }
            .tabla-docentes td::before { content: none; }
            .acciones { justify-content: flex-end; }
            #fila-vacia { display: block; border: none; margin: 0; padding: 0; }
            .modal-tarjeta { max-width: 100%; }
        }
    </style>
    @endpush

    <script>
        function filtrarMaterias() {
            const texto = document.getElementById('buscador').value.toLowerCase().trim();
            const filas = document.querySelectorAll('.fila-materia');
            let visibles = 0;

            filas.forEach(fila => {
                const coincide = fila.dataset.nombre.includes(texto);
                fila.style.display = coincide ? '' : 'none';
                if (coincide) visibles++;
            });

            document.getElementById('sin-resultados').style.display = (visibles === 0 && filas.length > 0) ? 'block' : 'none';
        }

        function abrirModalNuevo() {
            document.getElementById('modal-titulo').textContent = 'Nueva materia';
            document.getElementById('input-nombre').value = '';
            document.getElementById('metodo-form').value = '';
            document.getElementById('input-materia-id').value = '';
            document.getElementById('form-materia').action = "{{ url('admin/materias') }}";
            document.getElementById('modal-materia').style.display = 'flex';
        }

        function abrirModalEditar(id, nombre) {
            document.getElementById('modal-titulo').textContent = 'Editar materia';
            document.getElementById('input-nombre').value = nombre;
            document.getElementById('metodo-form').value = 'PUT';
            document.getElementById('input-materia-id').value = id;
            document.getElementById('form-materia').action = "{{ url('admin/materias') }}/" + id;
            document.getElementById('modal-materia').style.display = 'flex';
        }

        function cerrarModal() {
            document.getElementById('modal-materia').style.display = 'none';
        }

        function confirmarEliminar(id) {
            Swal.fire({
                title: '¿Eliminar materia?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#b3261e',
                cancelButtonColor: '#64748b',
            }).then((resultado) => {
                if (resultado.isConfirmed) {
                    const form = document.getElementById('form-eliminar');
                    form.action = "{{ url('admin/materias') }}/" + id;
                    form.submit();
                }
            });
        }

        document.getElementById('modal-materia').addEventListener('click', function (e) {
            if (e.target === this) cerrarModal();
        });

        // Si la validación falló en el servidor (ej. nombre duplicado), reabrir
        // el modal con el título y la acción correctos, y foco en el campo.
        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', () => {
                const esEdicion = document.getElementById('metodo-form').value === 'PUT';
                const materiaId = document.getElementById('input-materia-id').value;

                document.getElementById('modal-titulo').textContent = esEdicion ? 'Editar materia' : 'Nueva materia';
                document.getElementById('form-materia').action = esEdicion
                    ? "{{ url('admin/materias') }}/" + materiaId
                    : "{{ url('admin/materias') }}";
                document.getElementById('modal-materia').style.display = 'flex';
                document.getElementById('input-nombre').focus();
            });
        @endif
    </script>
@endsection