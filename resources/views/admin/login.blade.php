<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo · Liceo Laboratorio de Liberia</title>
    <style>
        :root {
            --navy-dark: #0f2d4d;
            --navy: #163d63;
            --dorado-dark: #c9a344;
            --dorado: #e0be6b;
            --verde: #3a6b47;
            --crema: #f7f4ec;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--crema);
            font-family: -apple-system, "Segoe UI", sans-serif;
        }
        .login-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(15,45,77,0.15);
            padding: 40px 36px;
            width: 100%;
            max-width: 380px;
            border-top: 4px solid var(--dorado-dark);
        }
        .login-card h1 {
            font-family: Georgia, serif;
            color: var(--navy-dark);
            font-size: 22px;
            margin: 0 0 4px;
            text-align: center;
        }
        .login-card p.subtitle {
            text-align: center;
            color: #666;
            font-size: 13px;
            margin: 0 0 28px;
        }
        label {
            display: block;
            font-size: 13px;
            color: var(--navy-dark);
            font-weight: 600;
            margin-bottom: 6px;
        }
        input[type=email], input[type=password] {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #d8d3c4;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 14px;
        }
        input:focus { outline: none; border-color: var(--dorado-dark); }
        button {
            width: 100%;
            padding: 12px;
            background: var(--navy-dark);
            color: var(--dorado);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
        }
        button:hover { background: var(--navy); }
        .error { color: #b3261e; font-size: 13px; margin: -10px 0 16px; }
    </style>
</head>
<body>
    <div class="login-card">
        <h1>Panel Administrativo</h1>
        <p class="subtitle">Control de Ausencias · Liceo Laboratorio de Liberia</p>

        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf
            <label for="email">Correo</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>

            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Ingresar</button>
        </form>
    </div>
</body>
</html>