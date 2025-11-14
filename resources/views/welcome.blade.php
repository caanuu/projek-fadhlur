<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #007bff, #00c6ff);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
        }

        .login-card {
            width: 380px;
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            background: #fff;
            animation: fadeIn 0.5s ease-in-out;
        }

        .login-header {
            background: linear-gradient(135deg, #007bff, #00c6ff);
            color: #fff;
            text-align: center;
            padding: 30px 20px;
        }

        .login-header img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
            border: 3px solid rgba(255, 255, 255, 0.8);
        }

        .login-body {
            padding: 30px;
        }

        .form-control {
            border-radius: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff, #00c6ff);
            border: none;
            border-radius: 10px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="login-card">
        <div class="login-header">
            <img src="https://i.ibb.co.com/FNckrp1/image.jpg" alt="Admin Logo">
            <h4 class="fw-bold mb-0">Login Admin</h4>
        </div>
        <div class="login-body">
            <form method="POST" action="/">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2">Login</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>