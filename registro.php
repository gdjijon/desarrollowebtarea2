<?php
session_start();
require 'conexion.php';

$mensaje_estado = '';
$tipo_alerta = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = filter_var(trim($_POST['correo']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if (!isset($_POST['captcha']) || empty($_POST['captcha']) || $_POST['captcha'] !== $_SESSION['captcha']) {
        $mensaje_estado = "Error: El código de seguridad (Captcha) es incorrecto.";
        $tipo_alerta = "danger";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje_estado = "Error: Formato de correo electrónico inválido.";
        $tipo_alerta = "danger";
    } elseif ($password !== $password_confirm) {
        $mensaje_estado = "Error: Las contraseñas no coinciden.";
        $tipo_alerta = "danger";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[%&+\-]).{8,}$/', $password)) {
        $mensaje_estado = "Error: La contraseña no cumple con los criterios de seguridad requeridos.";
        $tipo_alerta = "danger";
    } else {
        $stmt_check = $pdo->prepare("SELECT id FROM usuarios WHERE correo = :correo");
        $stmt_check->execute(['correo' => $correo]);
        
        if ($stmt_check->rowCount() > 0) {
            $mensaje_estado = "Error: El correo ya está registrado en el sistema.";
            $tipo_alerta = "danger";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            try {
                $stmt = $pdo->prepare("INSERT INTO usuarios (correo, password_hash) VALUES (:correo, :hash)");
                $stmt->execute(['correo' => $correo, 'hash' => $password_hash]);
                $mensaje_estado = "Cuenta creada exitosamente. Ya puede iniciar sesión.";
                $tipo_alerta = "success";
            } catch (PDOException $e) {
                $mensaje_estado = "Error en la base de datos al crear la cuenta.";
                $tipo_alerta = "danger";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | UTPL - Blog personal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root { --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%); --glass-bg: rgba(255, 255, 255, 0.15); --glass-border: rgba(255, 255, 255, 0.2); --accent-red: #e63946; --accent-red-hover: #d90429; }
        body { font-family: 'Poppins', sans-serif; background: var(--bg-gradient); background-attachment: fixed; color: white; min-height: 100vh; display: flex; flex-direction: column; }
        .glass-card { background: var(--glass-bg); backdrop-filter: blur(10px); border: 1px solid var(--glass-border); border-radius: 20px; padding: 30px; }
        .navbar { background: rgba(0, 0, 0, 0.2) !important; backdrop-filter: blur(15px); }
        .form-control { background: rgba(255, 255, 255, 0.9); border: none; border-radius: 10px; }
        .input-group .form-control { border-top-right-radius: 0; border-bottom-right-radius: 0; }
        .input-group .btn { border-top-right-radius: 10px; border-bottom-right-radius: 10px; background: rgba(255, 255, 255, 0.9); border: none; color: #333; }
        .btn-admin { background: var(--accent-red); border: none; color: white; padding: 10px 25px; border-radius: 50px; font-weight: 600; transition: all 0.3s; }
        .btn-admin:hover { background: var(--accent-red-hover); color: white; box-shadow: 0 5px 15px rgba(230, 57, 70, 0.4); }
        .captcha-img { border-radius: 8px; border: 1px solid #ccc; cursor: pointer; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">UTPL - Blog personal</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="contacto.php">Contacto</a></li>
                    <li class="nav-item ms-lg-2">
                        <a class="nav-link" href="https://github.com/gdjijon/desarrollowebtarea2" target="_blank" rel="noopener noreferrer" title="Ver código en GitHub">
                            <i class="bi bi-github fs-5"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container my-5 flex-grow-1 d-flex align-items-center justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="glass-card">
                <h2 class="text-center fw-bold mb-4">Crear Cuenta</h2>
                
                <?php if(!empty($mensaje_estado)): ?>
                    <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show" role="alert">
                        <?php echo $mensaje_estado; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form id="formRegistro" action="registro.php" method="POST">
                    <div class="mb-3">
                        <label for="correo" class="form-label fw-semibold">Correo Electrónico</label>
                        <input type="email" class="form-control" id="correo" name="correo" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Contraseña</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required 
                                   pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[%&+\-]).{8,}$" 
                                   title="Mínimo 8 caracteres, 1 mayúscula, 1 minúscula, 1 número y 1 carácter especial (% & + -)">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', 'eye-icon-1')">
                                <i class="bi bi-eye" id="eye-icon-1"></i>
                            </button>
                        </div>
                        <div class="form-text text-light opacity-75 small mt-1">Mínimo 8 caracteres, 1 mayúscula, 1 minúscula, 1 número y un especial (% & + -)</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirm" class="form-label fw-semibold">Confirmar Contraseña</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirm', 'eye-icon-2')">
                                <i class="bi bi-eye" id="eye-icon-2"></i>
                            </button>
                        </div>
                        <div id="error-pass" class="text-warning small d-none mt-1">Las contraseñas no coinciden.</div>
                    </div>

                    <div class="row align-items-end mb-4 mt-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="captcha" class="form-label fw-semibold">Código de seguridad</label>
                            <input type="text" class="form-control" id="captcha" name="captcha" required placeholder="Ingresa el código">
                        </div>
                        <div class="col-md-6 text-md-end text-center">
                            <img src="captcha.php" alt="Captcha" class="captcha-img" id="img-captcha" onclick="recargarCaptcha()" title="Clic para recargar">
                            <div class="small mt-1 text-light" style="cursor: pointer;" onclick="recargarCaptcha()"><i class="bi bi-arrow-clockwise"></i> Recargar</div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-admin py-2">Registrarse</button>
                        <a href="login.php" class="btn btn-outline-light py-2">Ya tengo cuenta</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        document.getElementById('formRegistro').addEventListener('submit', function(e) {
            let pass1 = document.getElementById('password').value;
            let pass2 = document.getElementById('password_confirm').value;
            let errorMsg = document.getElementById('error-pass');
            if (pass1 !== pass2) { e.preventDefault(); errorMsg.classList.remove('d-none'); } 
            else { errorMsg.classList.add('d-none'); }
        });
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            }
        }
        function recargarCaptcha() { 
            document.getElementById('img-captcha').src = 'captcha.php?' + Math.random(); 
        }
    </script>
</body>
</html>