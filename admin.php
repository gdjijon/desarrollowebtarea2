<?php
session_start();
require 'conexion.php';

// Verificar autenticación
if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
    header("Location: login.php");
    exit;
}

// Mapas de ordenamiento válidos (Columna seleccionada => Sentencia SQL de orden)
$orden_permitido = [
    'fecha_mensaje' => 'm.fecha_envio DESC',
    'nombre'        => 'm.nombre ASC',
    'correo'        => 'm.correo ASC',
    'mensaje'       => 'm.mensaje ASC',
    'usuario_desde' => 'u.fecha_registro ASC',
    'administrador' => 'u.rol ASC'
];

// Definir el criterio de ordenamiento por defecto o el seleccionado por el combo
$ordenar_por = 'fecha_mensaje';
if (isset($_GET['ordenar_por']) && array_key_exists($_GET['ordenar_por'], $orden_permitido)) {
    $ordenar_por = $_GET['ordenar_por'];
}

$sort_sql = $orden_permitido[$ordenar_por];

try {
    if ($_SESSION['rol'] === 'admin') {
        // El administrador ve los mensajes de todos los usuarios
        $sql = "SELECT m.nombre, m.correo, m.mensaje, m.fecha_envio, u.rol, u.fecha_registro 
                FROM mensajes_contacto m 
                LEFT JOIN usuarios u ON m.correo = u.correo 
                ORDER BY $sort_sql";
        $stmt = $pdo->query($sql);
        $mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // El usuario normal solo ve sus propios formularios enviados
        $sql = "SELECT m.nombre, m.correo, m.mensaje, m.fecha_envio, u.rol, u.fecha_registro 
                FROM mensajes_contacto m 
                LEFT JOIN usuarios u ON m.correo = u.correo 
                WHERE m.correo = :correo 
                ORDER BY $sort_sql";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['correo' => $_SESSION['usuario_correo']]);
        $mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    die("Error técnico al consultar la base de datos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Usuarios | UTPL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root { --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%); --glass-bg: rgba(255, 255, 255, 0.15); --glass-border: rgba(255, 255, 255, 0.2); --accent-red: #e63946; }
        body { font-family: 'Poppins', sans-serif; background: var(--bg-gradient); background-attachment: fixed; color: white; min-height: 100vh; display: flex; flex-direction: column; }
        .glass-card { background: var(--glass-bg); backdrop-filter: blur(10px); border: 1px solid var(--glass-border); border-radius: 20px; padding: 30px; }
        .navbar { background: rgba(0, 0, 0, 0.2) !important; backdrop-filter: blur(15px); }
        .table-glass { color: white; --bs-table-bg: transparent; --bs-table-color: white; }
        .table-glass th { border-bottom: 2px solid var(--accent-red); white-space: nowrap; }
        .table-glass td { border-bottom: 1px solid rgba(255,255,255,0.1); vertical-align: middle; }
        .form-select-custom { width: auto; display: inline-block; background-color: rgba(255, 255, 255, 0.9); color: #333; border-radius: 8px; border: none; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Portal Privado de Usuarios</a>
            <div class="d-flex align-items-center">
                <span class="navbar-text me-3 d-none d-md-block text-white">
                    <i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['usuario_correo']); ?> 
                    <span class="badge bg-<?php echo ($_SESSION['rol'] === 'admin') ? 'danger' : 'primary'; ?> ms-1">
                        <?php echo strtoupper($_SESSION['rol']); ?>
                    </span>
                </span>
                <a href="logout.php" class="btn btn-sm btn-outline-light d-flex align-items-center">
                    <i class="bi bi-box-arrow-right me-2"></i> Salir
                </a>
            </div>
        </div>
    </nav>

    <main class="container my-5 flex-grow-1">
        <div class="glass-card">
            
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <h2 class="fw-bold mb-0">
                    <i class="bi bi-inbox me-2"></i>
                    <?php echo ($_SESSION['rol'] === 'admin') ? 'Bandeja Global de Mensajes Enviados' : 'Mis Mensajes Enviados'; ?>
                </h2>
                
                <form method="GET" action="admin.php" class="d-flex align-items-center gap-2">
                    <label for="ordenar_por" class="fw-semibold small text-nowrap mb-0">Ordenar por</label>
                    <select name="ordenar_por" id="ordenar_por" class="form-select form-select-sm form-select-custom" onchange="this.form.submit()">
                        <option value="fecha_mensaje" <?php echo $ordenar_por === 'fecha_mensaje' ? 'selected' : ''; ?>>fecha del mensaje</option>
                        <option value="nombre" <?php echo $ordenar_por === 'nombre' ? 'selected' : ''; ?>>nombre</option>
                        <option value="correo" <?php echo $ordenar_por === 'correo' ? 'selected' : ''; ?>>correo</option>
                        <option value="mensaje" <?php echo $ordenar_por === 'mensaje' ? 'selected' : ''; ?>>mensaje</option>
                        <option value="usuario_desde" <?php echo $ordenar_por === 'usuario_desde' ? 'selected' : ''; ?>>usuario desde</option>
                        <option value="administrador" <?php echo $ordenar_por === 'administrador' ? 'selected' : ''; ?>>administrador</option>
                    </select>
                </form>
            </div>
            
            <div class="table-responsive">
                <table class="table table-glass table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Fecha del mensaje</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Correo</th>
                            <th scope="col" style="min-width: 250px;">Mensaje</th>
                            <th scope="col">Usuario desde</th>
                            <th scope="col">Administrador</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($mensajes) > 0): ?>
                            <?php foreach ($mensajes as $fila): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i', strtotime($fila['fecha_envio'])); ?></td>
                                    
                                    <td><?php echo htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    
                                    <td><?php echo htmlspecialchars($fila['correo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    
                                    <td><?php echo nl2br(htmlspecialchars($fila['mensaje'], ENT_QUOTES, 'UTF-8')); ?></td>
                                    
                                    <td>
                                        <?php 
                                            echo !empty($fila['fecha_registro']) 
                                                ? date('d/m/Y', strtotime($fila['fecha_registro'])) 
                                                : '<span class="text-white-50 small">No registrado</span>'; 
                                        ?>
                                    </td>
                                    
                                    <td class="text-center fw-bold text-<?php echo ($fila['rol'] === 'admin') ? 'warning' : 'light'; ?>">
                                        <?php echo ($fila['rol'] === 'admin') ? 'si' : 'no'; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-white-50">No hay registros disponibles bajo este criterio.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>