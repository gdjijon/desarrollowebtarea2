<?php
session_start();
require 'conexion.php';

$mensaje_estado = '';
$tipo_alerta = '';
$habilitar_smtp = false; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['captcha']) || empty($_POST['captcha']) || $_POST['captcha'] !== $_SESSION['captcha']) {
        $mensaje_estado = "Error: El código de seguridad (Captcha) es incorrecto.";
        $tipo_alerta = "danger";
    } else {
        $nombre = htmlspecialchars(trim($_POST['nombre']), ENT_QUOTES, 'UTF-8');
        $correo = filter_var(trim($_POST['correo']), FILTER_SANITIZE_EMAIL);
        $mensaje = htmlspecialchars(trim($_POST['mensaje']), ENT_QUOTES, 'UTF-8');

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $mensaje_estado = "Error: El formato del correo electrónico es inválido.";
            $tipo_alerta = "danger";
        } elseif (strlen($mensaje) > 500) {
            $mensaje_estado = "Error: El mensaje supera el límite de 500 caracteres.";
            $tipo_alerta = "danger";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO mensajes_contacto (nombre, correo, mensaje) VALUES (:nombre, :correo, :mensaje)");
                $stmt->execute(['nombre' => $nombre, 'correo' => $correo, 'mensaje' => $mensaje]);
                
                $mensaje_estado = "Su mensaje ha sido enviado.";
                $tipo_alerta = "success";

                if ($habilitar_smtp) {
                    // Bloque PHPMailer (sin cambios)
                }
            } catch (PDOException $e) {
                $mensaje_estado = "Error de base de datos al guardar el mensaje.";
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
    <title>Contacto | UTPL - Blog personal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root { --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%); --glass-bg: rgba(255, 255, 255, 0.15); --glass-border: rgba(255, 255, 255, 0.2); --accent-red: #e63946; --accent-red-hover: #d90429; }
        body { font-family: 'Poppins', sans-serif; background: var(--bg-gradient); background-attachment: fixed; color: white; min-height: 100vh; display: flex; flex-direction: column; }
        .glass-card { background: var(--glass-bg); backdrop-filter: blur(10px); border: 1px solid var(--glass-border); border-radius: 20px; padding: 30px; }
        .navbar { background: rgba(0, 0, 0, 0.2) !important; backdrop-filter: blur(15px); }
        .form-control { background: rgba(255, 255, 255, 0.9); border: none; border-radius: 10px; }
        .btn-admin { background: var(--accent-red); border: none; color: white; padding: 10px 25px; border-radius: 50px; font-weight: 600; transition: all 0.3s; }
        .btn-admin:hover { background: var(--accent-red-hover); color: white; box-shadow: 0 5px 15px rgba(230, 57, 70, 0.4); }
        .captcha-img { border-radius: 8px; border: 1px solid #ccc; cursor: pointer; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">UTPL - Blog personal</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link active" href="contacto.php">Contacto</a></li>
                    <li class="nav-item ms-lg-2">
                        <a class="nav-link" href="https://github.com/gdjijon/desarrollowebtarea2" target="_blank" rel="noopener noreferrer" title="Ver código en GitHub">
                            <i class="bi bi-github fs-5"></i>
                        </a>
                    </li>
                    <li class="nav-item ms-lg-3"><a href="login.php" class="btn btn-admin"><i class="bi bi-person-lock me-2"></i>Acceso Portal</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container my-5 flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="glass-card">
                    <h2 class="text-center fw-bold mb-4">Contáctame</h2>
                    <?php if(!empty($mensaje_estado)): ?>
                        <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show" role="alert">
                            <?php echo $mensaje_estado; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    <form action="contacto.php" method="POST">
                        <div class="mb-3">
                            <label for="nombre" class="form-label fw-semibold">Nombres Completos</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="correo" class="form-label fw-semibold">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo" required>
                        </div>
                        <div class="mb-3">
                            <label for="mensaje" class="form-label fw-semibold">Mensaje</label>
                            <textarea class="form-control" id="mensaje" name="mensaje" rows="5" maxlength="500" required oninput="contarCaracteres(this)"></textarea>
                            <div class="text-end mt-1 text-light small"><span id="contador">0</span>/500 caracteres</div>
                        </div>
                        <div class="row align-items-end mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="captcha" class="form-label fw-semibold">Validación de seguridad</label>
                                <input type="text" class="form-control" id="captcha" name="captcha" required>
                            </div>
                            <div class="col-md-6 text-md-end text-center">
                                <img src="captcha.php" alt="Captcha" class="captcha-img" id="img-captcha" onclick="recargarCaptcha()">
                            </div>
                        </div>
                        <div class="d-grid"><button type="submit" class="btn btn-admin py-2"><i class="bi bi-send me-2"></i>Enviar Mensaje</button></div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        function contarCaracteres(obj) { document.getElementById('contador').innerHTML = obj.value.length; }
        function recargarCaptcha() { document.getElementById('img-captcha').src = 'captcha.php?' + Math.random(); }
    </script>
</body>
</html>