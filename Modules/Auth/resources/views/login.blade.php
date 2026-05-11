<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso unico - Market KM2</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --brand: #f97316;
            --brand-dark: #c2410c;
            --ink: #10151f;
            --muted: #667085;
            --line: #eadfd5;
            --paper: #fffaf5;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            color: var(--ink);
            background: #14100d url('{{ asset('images/marketinterior1.webp') }}') center center / cover no-repeat fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: linear-gradient(115deg, rgba(10, 13, 20, 0.82), rgba(91, 39, 13, 0.58), rgba(10, 13, 20, 0.28));
            pointer-events: none;
        }

        .back-link {
            position: fixed;
            left: 24px;
            top: 22px;
            z-index: 3;
            height: 40px;
            display: inline-flex;
            align-items: center;
            gap: 9px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.22);
            background: rgba(255,255,255,0.10);
            color: #fff;
            padding: 0 14px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 800;
            backdrop-filter: blur(16px);
        }

        .shell {
            position: relative;
            z-index: 2;
            width: min(100%, 1080px);
            display: grid;
            grid-template-columns: 1fr 440px;
            overflow: hidden;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.24);
            background: rgba(255,255,255,0.92);
            box-shadow: 0 36px 90px -44px rgba(0,0,0,0.86);
            backdrop-filter: blur(22px);
        }

        .presentation {
            min-height: 640px;
            padding: 40px;
            color: #fff;
            background:
                linear-gradient(150deg, rgba(16,21,31,0.88), rgba(16,21,31,0.42)),
                url('{{ asset('images/marketinterior1.webp') }}') center center / cover no-repeat;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .logo-wrap {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .logo-wrap img, .logo-fallback {
            width: 68px;
            height: 68px;
            object-fit: contain;
            border-radius: 8px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.18);
        }

        .logo-fallback {
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            color: #fff;
        }

        .presentation h1 {
            max-width: 620px;
            font-size: clamp(38px, 6vw, 70px);
            line-height: 0.96;
            font-weight: 900;
        }

        .presentation p {
            max-width: 560px;
            margin-top: 20px;
            color: rgba(255,255,255,0.84);
            font-size: 16px;
            line-height: 1.8;
            font-weight: 650;
        }

        .badges {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-top: 30px;
        }

        .badge {
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.18);
            background: rgba(255,255,255,0.10);
            padding: 16px;
            backdrop-filter: blur(12px);
        }

        .badge strong {
            display: block;
            font-size: 13px;
            font-weight: 900;
        }

        .badge span {
            display: block;
            margin-top: 6px;
            color: rgba(255,255,255,0.70);
            font-size: 12px;
            font-weight: 700;
            line-height: 1.45;
        }

        .panel {
            padding: 42px;
            background: linear-gradient(180deg, #fff, var(--paper));
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .panel-eyebrow {
            color: var(--brand-dark);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .panel h2 {
            margin-top: 10px;
            font-size: 32px;
            line-height: 1.1;
            font-weight: 900;
        }

        .panel-copy {
            margin-top: 12px;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.7;
            font-weight: 600;
        }

        .alert {
            margin-top: 22px;
            border-radius: 8px;
            padding: 13px 14px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
            font-size: 13px;
            font-weight: 800;
        }

        .alert.error {
            border: 1px solid rgba(220, 38, 38, 0.18);
            background: #fef2f2;
            color: #b91c1c;
        }

        .alert.info {
            border: 1px solid rgba(249, 115, 22, 0.20);
            background: #fff7ed;
            color: #9a3412;
        }

        form {
            margin-top: 26px;
        }

        .field {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #5d6675;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .input {
            position: relative;
        }

        .input i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #b45309;
            font-size: 14px;
        }

        input {
            width: 100%;
            height: 54px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #fff;
            color: var(--ink);
            padding: 0 16px 0 46px;
            font: inherit;
            font-size: 15px;
            font-weight: 700;
            outline: none;
            transition: border-color .18s ease, box-shadow .18s ease;
        }

        input:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.13);
        }

        .form-error {
            margin-top: 7px;
            color: #dc2626;
            font-size: 12px;
            font-weight: 800;
        }

        .submit {
            width: 100%;
            height: 54px;
            border: 0;
            border-radius: 8px;
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
            box-shadow: 0 22px 36px -22px rgba(194, 65, 12, 0.95);
        }

        .actions {
            margin-top: 18px;
            display: grid;
            gap: 10px;
        }

        .secondary-link {
            min-height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 8px;
            border: 1px solid var(--line);
            background: #fff;
            color: #2d3440;
            text-decoration: none;
            font-size: 14px;
            font-weight: 900;
        }

        .footer-note {
            margin-top: 22px;
            color: #8a94a3;
            text-align: center;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.55;
        }

        @media (max-width: 920px) {
            body { padding: 68px 16px 16px; align-items: flex-start; }
            .shell { grid-template-columns: 1fr; }
            .presentation { min-height: 360px; padding: 28px; }
            .badges { grid-template-columns: 1fr; }
            .panel { padding: 30px 22px; }
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

    <main class="shell">
        <section class="presentation" aria-label="Presentacion Market KM2">
            <div class="logo-wrap">
                @if($loginLogoPath)
                    <img src="{{ asset($loginLogoPath) }}" alt="Market KM2">
                @else
                    <div class="logo-fallback">K2</div>
                @endif
                <div>
                    <strong>Market KM2</strong>
                    <p style="margin: 3px 0 0; font-size: 12px; line-height: 1.4;">Minimarket & Cafe</p>
                </div>
            </div>

            <div>
                <h1>Un solo acceso para comprar o gestionar.</h1>
                <p>Clientes, operadores, administradores y superadministradores ingresan por esta pantalla. El sistema reconoce el tipo de cuenta y redirige al flujo correcto.</p>
                <div class="badges">
                    <div class="badge">
                        <strong>Cliente</strong>
                        <span>Compra, pedido web y WhatsApp.</span>
                    </div>
                    <div class="badge">
                        <strong>Operador</strong>
                        <span>Gestion de pedidos y stock.</span>
                    </div>
                    <div class="badge">
                        <strong>Administrador</strong>
                        <span>Catalogo, promociones y auditoria.</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="panel" aria-label="Formulario de acceso">
            <p class="panel-eyebrow">Acceso unico</p>
            <h2>Ingresa a Market KM2</h2>
            <p class="panel-copy">Usa tu correo y contrasena. Si eres cliente nuevo, crea tu cuenta para que tus datos se precarguen al generar pedidos.</p>

            @if(session('error'))
                <div class="alert info">
                    <i class="fas fa-circle-info"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->has('auth'))
                <div class="alert error">
                    <i class="fas fa-circle-exclamation"></i>
                    {{ $errors->first('auth') }}
                </div>
            @endif

            <form method="POST" action="{{ route('auth.login.submit') }}">
                @csrf

                <div class="field">
                    <label for="email">Correo electronico</label>
                    <div class="input">
                        <input type="email" id="email" name="email" placeholder="correo@marketkm2.com" value="{{ old('email') }}" required autofocus>
                        <i class="fas fa-envelope"></i>
                    </div>
                    @error('email')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label for="password">Contrasena</label>
                    <div class="input">
                        <input type="password" id="password" name="password" placeholder="Ingresa tu contrasena" required>
                        <i class="fas fa-lock"></i>
                    </div>
                    @error('password')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="submit">
                    <i class="fas fa-right-to-bracket"></i>
                    Ingresar
                </button>
            </form>

            <div class="actions">
                <a href="{{ route('storefront.cliente.register') }}" class="secondary-link">
                    <i class="fas fa-user-plus"></i>
                    Crear cuenta de cliente
                </a>
            </div>

            <p class="footer-note">La facturacion y el POS se mantienen fuera del alcance del sistema. Este acceso cubre tienda virtual, pedidos, stock y gestion interna.</p>
        </section>
    </main>
</body>
</html>
