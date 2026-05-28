<?php
session_start();
require 'conexion.php';

// Variables de configuración de correo (Modificable)
$habilitar_smtp = true; // <-- CAMBIAR A FALSE SI EL HOSTING BLOQUEA EL ENVÍO

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Validar Captcha
    if (!isset($_POST['captcha']) || $_POST['captcha'] !== $_SESSION['captcha']) {
        die("Error: El captcha ingresado es incorrecto.");
    }

    // 2. Sanitizar entradas con htmlspecialchars (Protección XSS)
    $nombre = htmlspecialchars(trim($_POST['nombre']), ENT_QUOTES, 'UTF-8');
    $correo = filter_var(trim($_POST['correo']), FILTER_SANITIZE_EMAIL);
    $mensaje = htmlspecialchars(trim($_POST['mensaje']), ENT_QUOTES, 'UTF-8');

    // 3. Validar longitud y formato
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        die("Error: Formato de correo inválido.");
    }
    if (strlen($mensaje) > 500) {
        die("Error: El mensaje supera los 500 caracteres permitidos.");
    }

    // 4. Inserción en Base de Datos (Sentencias Preparadas)
    try {
        $stmt = $pdo->prepare("INSERT INTO mensajes_contacto (nombre, correo, mensaje) VALUES (:nombre, :correo, :mensaje)");
        $stmt->execute(['nombre' => $nombre, 'correo' => $correo, 'mensaje' => $mensaje]);
        
        $estado_db = "Su mensaje ha sido enviado y registrado correctamente.";

        // 5. Envío de Correo (Opcional según viabilidad del hosting)
        if ($habilitar_smtp) {
            // Asumiendo que descargas PHPMailer en una carpeta 'PHPMailer'
            require 'PHPMailer/src/Exception.php';
            require 'PHPMailer/src/PHPMailer.php';
            require 'PHPMailer/src/SMTP.php';

            $mail = new PHPMailer\PHPMailer\PHPMailer();
            try {
                // Configuración del servidor SMTP
                $mail->isSMTP();
                $mail->Host       = 'smtp.tuhostinggratuito.com'; // Cambiar por tu servidor SMTP
                $mail->SMTPAuth   = true;
                $mail->Username   = 'tu_correo@tudominio.com';    // Tu correo del hosting
                $mail->Password   = 'tu_password';                // Contraseña del correo
                $mail->SMTPSecure = 'tls'; // o 'ssl'
                $mail->Port       = 587;   // o 465 para ssl

                // Remitente y Destinatario
                $mail->setFrom('tu_correo@tudominio.com', 'Administración Web');
                $mail->addAddress($correo, $nombre); // Correo del usuario que llenó el formulario

                // Contenido
                $mail->isHTML(true);
                $mail->Subject = 'Confirmacion de recepcion de mensaje';
                $mail->Body    = "Hola $nombre, <br><br>Muchas gracias por contactarnos, se confirma el registro de su mensaje.<br><br>Atentamente,<br>El equipo.";

                $mail->send();
            } catch (Exception $e) {
                // Falla silenciosa para el usuario, pero se puede loguear el error
                $estado_db .= " (Nota: El correo de confirmación no pudo enviarse).";
            }
        }

        echo "<h1>Éxito</h1><p>$estado_db</p><a href='contacto.php'>Volver al formulario</a>";

    } catch (PDOException $e) {
        die("Error al guardar el mensaje: " . $e->getMessage());
    }
}
?>