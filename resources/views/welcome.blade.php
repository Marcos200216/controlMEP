<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de ausencias</title>
    <style>
        * { box-sizing: border-box; }

        :root {
            --navy: #0f2d4d;
            --navy-light: #163d63;
            --gold: #c9a344;
            --gold-light: #e0be6b;
            --green: #3a6b47;
            --cream: #f7f4ec;
            --text-dark: #1f2937;
            --text-muted: #64748b;
        }

        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(180deg, var(--navy) 0%, var(--navy-light) 45%, var(--cream) 45%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .contenedor {
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        .tarjeta {
            background: #fff;
            border-radius: 20px;
            padding: 2.5rem 2rem 2rem;
            box-shadow: 0 20px 40px -12px rgba(15, 45, 77, 0.35);
            border-top: 4px solid var(--gold);
        }

        .escudo-wrap {
            width: 110px;
            height: 110px;
            margin: -4.5rem auto 1.25rem;
            background: #fff;
            border-radius: 50%;
            padding: 8px;
            box-shadow: 0 8px 20px rgba(15, 45, 77, 0.25), 0 0 0 3px var(--gold);
        }

        .escudo-wrap img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }

        h1 {
            font-family: Georgia, "Times New Roman", serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--navy);
            margin: 0 0 0.35rem;
            letter-spacing: 0.2px;
        }

        .linea-dorada {
            width: 48px;
            height: 3px;
            background: var(--gold);
            border-radius: 2px;
            margin: 0.6rem auto 0.9rem;
        }

        .subtitulo {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 2rem;
            letter-spacing: 0.3px;
        }

        .opciones {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .boton {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 1rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
        }

        .boton-primario {
            background: var(--navy);
            color: #fff;
            border: none;
            box-shadow: 0 6px 16px -4px rgba(15, 45, 77, 0.45);
        }
        .boton-primario:hover {
            background: var(--navy-light);
            transform: translateY(-1px);
        }

        .boton-secundario {
            background: #fff;
            color: var(--navy);
            border: 1.5px solid var(--gold-light);
        }
        .boton-secundario:hover {
            background: #fdf9ef;
            border-color: var(--gold);
            transform: translateY(-1px);
        }

        .pie {
            margin-top: 1.75rem;
            font-size: 0.7rem;
            color: #94a3b8;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    <div class="contenedor">
        <div class="tarjeta">
            <div class="escudo-wrap">
                <img src="{{ asset('images/escudo.png') }}" alt="Escudo Liceo Laboratorio de Liberia">
            </div>

            <h1>Control de ausencias</h1>
            <div class="linea-dorada"></div>
            <p class="subtitulo">Liceo Laboratorio de Liberia</p>

            <div class="opciones">
                <a href="{{ route('ausencias.index') }}" class="boton boton-primario">
                    Soy docente - Registrar ausencias
                </a>

                <a href="{{ route('admin.login') }}" class="boton boton-secundario">
                    Panel administrativo
                </a>
            </div>

            
        </div>
    </div>

</body>
</html>