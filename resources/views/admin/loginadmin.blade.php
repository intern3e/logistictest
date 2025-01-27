<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-card {
            max-width: 400px;
            margin: auto;
            margin-top: 100px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .login-header {
            background-color: #9999CC;
            padding: 20px;
            border-radius: 10px 10px 0 0;
        }
        .login-button {
            background-color: #ffa951;
            color: white;
        }
        .login-button:hover {
            background-color: #e89542;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-card">
            <div class="login-header text-center">
                <h5 class="mb-0">ระบบจัดเตรียมสินค้า</h5>
            </div>
            <div class="p-4">
                <form method="POST" action="{{ route('admin.loginadmin') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="so_number" class="form-label">ID Admin</label>
                        <input type="text" class="form-control" id="id_admin" name="id_admin" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label></label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn login-button w-100">เข้าสู่ระบบ</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
