<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - SSO</title>
    <style>
        /* =========================================================
           PASTEL THEME + FLUID TYPOGRAPHY LAYER
           โทนสีพาสเทล สบายตา บนพื้นขาว
           ========================================================= */
        :root {
            --pastel-primary: #A7C7E7;      /* ฟ้าพาสเทล */
            --pastel-primary-dark: #89B0D3; /* ฟ้าเข้มขึ้นสำหรับ hover/focus */
            --pastel-accent: #B5EAD7;       /* เขียวมิ้นท์พาสเทล */
            --pastel-bg: #F7F9FC;           /* พื้นหลังขาวอมฟ้าอ่อนมาก */
            --pastel-card: #FFFFFF;         /* การ์ดขาวบริสุทธิ์ */
            --text-main: #4A5568;           /* เทาเข้ม อ่านง่าย ไม่ดำสนิท */
            --text-sub: #A0AEC0;            /* เทาอ่อน สำหรับ subtitle/footer */
            --border-color: #E2E8F0;        /* เส้นขอบสีเทาอ่อนมาก */
            --error-bg: #FFF5F5;
            --error-text: #C53030;
            --error-border: #FED7D7;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background-color: var(--pastel-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(12px, 0.45vw + 7px, 16px) !important;
            color: var(--text-main);
        }

        .login-card {
            background: var(--pastel-card);
            border-radius: 16px;
            padding: clamp(28px, 4vw, 44px);
            width: 100%;
            max-width: 400px;
            /* เงาแบบนุ่มนวล สไตล์พาสเทล */
            box-shadow: 
                0 4px 6px rgba(167, 199, 231, 0.1),
                0 10px 40px rgba(167, 199, 231, 0.15);
            border: 1px solid var(--border-color);
        }

        .login-card h1 { 
            text-align: center; 
            color: var(--text-main); 
            margin-bottom: 8px; 
            font-size: clamp(18px, 1vw + 10px, 24px) !important; 
            font-weight: 700;
        }

        .login-card .subtitle { 
            text-align: center; 
            color: var(--text-sub); 
            margin-bottom: clamp(20px, 3vw, 30px); 
            font-size: clamp(11px, 0.55vw + 5px, 14px) !important; 
        }

        .form-group { 
            margin-bottom: clamp(14px, 2vw, 20px); 
        }

        .form-group label { 
            display: block; 
            margin-bottom: 6px; 
            color: var(--text-main); 
            font-weight: 600; 
            font-size: clamp(11px, 0.55vw + 5px, 14px) !important; 
        }

        .form-group input {
            width: 100%; 
            padding: clamp(10px, 1vw, 12px) 16px; 
            border: 2px solid var(--border-color);
            border-radius: 6px;
            font-size: clamp(12px, 0.55vw + 6px, 16px) !important;
            transition: all 0.3s ease;
            background: #FAFBFD;
            color: var(--text-main);
        }

        .form-group input:focus { 
            outline: none; 
            border-color: var(--pastel-primary); 
            background: white;
            box-shadow: 0 0 0 4px rgba(167, 199, 231, 0.2);
        }

        .btn-login {
            width: 100%; 
            padding: clamp(12px, 1.2vw, 14px);
            background: var(--pastel-primary);
            color: white; 
            border: none; 
            border-radius: 6px;
            font-size: clamp(12px, 0.55vw + 6px, 16px) !important; 
            font-weight: 600; 
            cursor: pointer;
            transition: all 0.2s ease;
            letter-spacing: 0.3px;
        }

        .btn-login:hover { 
            background: var(--pastel-primary-dark);
            transform: translateY(-2px); 
            box-shadow: 0 6px 20px rgba(167, 199, 231, 0.35);
        }

        .error-message {
            background: var(--error-bg); 
            color: var(--error-text); 
            padding: clamp(8px, 1vw, 10px) 14px;
            border-radius: 6px; 
            margin-bottom: clamp(14px, 2vw, 20px); 
            font-size: clamp(11px, 0.55vw + 5px, 14px) !important;
            border: 1px solid var(--error-border);
        }

        .sso-badge {
            text-align: center;
            margin-top: clamp(14px, 2vw, 20px);
            padding-top: clamp(14px, 2vw, 20px);
            border-top: 1px solid var(--border-color);
            color: var(--text-sub);
            font-size: clamp(9px, 0.5vw + 4px, 12px) !important;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h1>เข้าสู่ระบบ</h1>

        @if ($errors->any())
            <div class="error-message">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" 
                    value="{{ old('username') }}" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">รหัสผ่าน</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-login">เข้าสู่ระบบ</button>
        </form>

        <div class="sso-badge">
            หากเข้าสู่ระบบไม่ได้ ติดต่อผู้ดูแลระบบ
        </div>
    </div>
</body>
</html>