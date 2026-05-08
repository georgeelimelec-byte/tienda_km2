<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesion - Market KM2</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --brand: #f97316;
            --brand-dark: #c2410c;
            --ink: #111827;
            --muted: #64748b;
            --line: #e8ded5;
            --surface: rgba(255, 255, 255, 0.94);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            color: var(--ink);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 18px;
            background: #111827 url('{{ asset('images/marketinterior1.webp') }}') center center / cover no-repeat fixed;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                linear-gradient(115deg, rgba(17, 24, 39, 0.58), rgba(67, 20, 7, 0.34)),
                radial-gradient(circle at 50% 16%, rgba(255, 237, 213, 0.20), transparent 30%);
            pointer-events: none;
            z-index: 0;
        }

        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.14);
            backdrop-filter: blur(1px);
            pointer-events: none;
            z-index: 1;
        }

        .back-link {
            position: fixed;
            top: 20px;
            left: 22px;
            z-index: 3;
            height: 36px;
            padding: 0 13px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.36);
            background: rgba(17, 24, 39, 0.42);
            color: #fff;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 800;
            box-shadow: 0 12px 30px -18px rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(12px);
            transition: background 0.2s ease, transform 0.2s ease, border-color 0.2s ease;
        }

        .back-link:hover {
            background: rgba(17, 24, 39, 0.62);
            border-color: rgba(255, 255, 255, 0.58);
            transform: translateY(-1px);
        }

        .login-page {
            position: relative;
            z-index: 2;
            width: min(100%, 440px);
        }

        .login-card {
            position: relative;
            overflow: hidden;
            border-radius: 22px;
            border: 1px solid rgba(255, 255, 255, 0.68);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(255, 250, 245, 0.92));
            box-shadow: 0 28px 70px -36px rgba(0, 0, 0, 0.82);
            backdrop-filter: blur(24px);
            padding: 32px;
        }

        .login-card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 3px;
            background: linear-gradient(90deg, var(--brand), #fb923c, var(--brand-dark));
        }

        .brand {
            text-align: center;
            margin-bottom: 30px;
        }

        .brand-logo {
            width: 108px;
            height: 108px;
            margin: 0 auto 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: logoLight 3.2s ease-in-out infinite;
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 12px 18px rgba(92, 43, 16, 0.30));
        }

        @keyframes logoLight {
            0%, 100% {
                transform: translateY(0) scale(1);
                filter: drop-shadow(0 0 0 rgba(249, 115, 22, 0));
            }
            45% {
                transform: translateY(-3px) scale(1.025);
                filter: drop-shadow(0 0 18px rgba(249, 115, 22, 0.55));
            }
            58% {
                transform: translateY(-3px) scale(1.02);
                filter: drop-shadow(0 0 28px rgba(251, 146, 60, 0.42));
            }
        }

        .brand-logo-fallback {
            width: 76px;
            height: 76px;
            border-radius: 18px;
            background: linear-gradient(135deg, var(--brand), #fbbf24);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 25px;
        }

        .brand h1 {
            color: #172033;
            font-size: 31px;
            line-height: 1;
            font-weight: 900;
            letter-spacing: -0.045em;
        }

        .brand p {
            color: #7c6f68;
            font-size: 14px;
            font-weight: 700;
            margin-top: 8px;
        }

        .form-header {
            margin-bottom: 22px;
        }

        .form-header h2 {
            color: #172033;
            font-size: 24px;
            line-height: 1.12;
            font-weight: 900;
            letter-spacing: -0.035em;
            margin-bottom: 8px;
        }

        .form-header p {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.6;
        }

        .login-error {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            padding: 13px 14px;
            border-radius: 14px;
            border: 1px solid rgba(220, 38, 38, 0.18);
            background: #fef2f2;
            color: #b91c1c;
            font-size: 13px;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #7c6f68;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #b45309;
            font-size: 14px;
            transition: color 0.2s ease;
        }

        .form-input {
            width: 100%;
            height: 52px;
            border: 1px solid var(--line);
            border-radius: 13px;
            background: #fff;
            color: #172033;
            padding: 0 16px 0 46px;
            font: inherit;
            font-size: 15px;
            font-weight: 650;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .form-input::placeholder {
            color: #9aa7b7;
            font-weight: 600;
        }

        .form-input:focus {
            border-color: var(--brand);
            background: #fffdfb;
            box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.13);
        }

        .input-wrapper:focus-within i {
            color: var(--brand);
        }

        .form-error {
            margin-top: 7px;
            display: flex;
            align-items: center;
            gap: 6px;
            color: #dc2626;
            font-size: 12px;
            font-weight: 700;
        }

        .btn-login {
            width: 100%;
            height: 54px;
            border: 0;
            border-radius: 13px;
            background: linear-gradient(135deg, var(--brand), var(--brand-dark));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font: inherit;
            font-size: 15px;
            font-weight: 900;
            cursor: pointer;
            box-shadow: 0 20px 34px -20px rgba(194, 65, 12, 0.92);
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            filter: saturate(1.05);
            box-shadow: 0 24px 40px -22px rgba(194, 65, 12, 1);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .footer-text {
            margin-top: 18px;
            text-align: center;
            color: rgba(255, 255, 255, 0.84);
            font-size: 12px;
            font-weight: 700;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
        }

        @media (max-width: 520px) {
            body {
                padding: 64px 16px 16px;
                align-items: flex-start;
            }

            .back-link {
                top: 14px;
                left: 14px;
            }

            .login-card {
                border-radius: 20px;
                padding: 28px 22px;
            }

            .brand-logo {
                width: 88px;
                height: 88px;
            }

            .brand h1 {
                font-size: 28px;
            }

            .form-header h2 {
                font-size: 23px;
            }
        }
    </style>
</head>
<body>
    @php
        $loginLogoPath = collect([
            'images/logo-marketkm2.webp',
            'images/logomarket.webp',
            'images/company-logo.svg',
            'images/company-logo.png',
            'images/company-logo.webp',
            'images/company-logo.jpg',
            'images/company-logo.jpeg',
        ])->first(fn ($path) => file_exists(public_path($path)));
    @endphp

    <a href="{{ route('storefront.index') }}" class="back-link">
        <i class="fas fa-arrow-left"></i>
        Tienda
    </a>

    <main class="login-page">
        <section class="login-card" aria-label="Inicio de sesion">
            <div class="brand">
                @if($loginLogoPath)
                    <div class="brand-logo">
                        <img src="{{ asset($loginLogoPath) }}" alt="Market KM2">
                    </div>
                @else
                    <div class="brand-logo brand-logo-fallback">K2</div>
                @endif

                <h1>Market KM2</h1>
                <p>Minimarket & Cafe</p>
            </div>

            <div class="form-header">
                <h2>Hola de nuevo</h2>
                <p>Ingresa tus credenciales para continuar.</p>
            </div>

            @if($errors->has('auth'))
                <div class="login-error">
                    <i class="fas fa-circle-exclamation"></i>
                    {{ $errors->first('auth') }}
                </div>
            @endif

            <form method="POST" action="{{ route('auth.login.submit') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="email">Correo electronico</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" class="form-input"
                               placeholder="admin@ponteready.com" value="{{ old('email') }}" required autofocus>
                        <i class="fas fa-envelope"></i>
                    </div>
                    @error('email')
                        <div class="form-error"><i class="fas fa-xmark"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Contrasena</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" class="form-input"
                               placeholder="Ingresa tu contrasena" required>
                        <i class="fas fa-lock"></i>
                    </div>
                    @error('password')
                        <div class="form-error"><i class="fas fa-xmark"></i> {{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-right-to-bracket"></i>
                    Ingresar
                </button>
            </form>
        </section>

        <p class="footer-text">Market KM2 &copy; {{ date('Y') }}</p>
    </main>
</body>
</html>
