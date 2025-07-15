<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login ITB STIKOM BALI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #d3d3d3; }
        .login-container {
            width: 350px; background: white; padding: 20px; border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); text-align: center;
        }
        .logo {
            width: 100px; height: 100px; margin: 0 auto 15px;
        }
        .form-control { margin-bottom: 10px; }
        .footer { font-size: 12px; margin-top: 10px; }
    </style>
</head>
<body>

<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="login-container">
        <div class="logo">
            <img height="80" src="./asset/img/itb.png" width="80">
        </div>

        <!-- Form Login -->
        <form action="login.php" method="POST">
            <input type="text" class="form-control" name="username" placeholder="Username" required>
            <input type="password" class="form-control" name="password" placeholder="Password" required>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        <a href="./poin/index.php" class="btn btn-primary w-100 mt-3">Cek Poin SKKM</a>

        <div class="footer">© Agung Tisna</div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
