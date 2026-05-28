<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UTPL - Blog personal | Guillermo Jijón</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --glass-bg: rgba(255, 255, 255, 0.15);
            --glass-border: rgba(255, 255, 255, 0.2);
            --accent-red: #e63946; 
            --accent-red-hover: #d90429;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-gradient);
            background-attachment: fixed;
            color: white;
            min-height: 100vh;
        }

        /* Efecto de cristal para los contenedores */
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 30px;
            transition: transform 0.3s ease;
        }

        .glass-card:hover {
            transform: translateY(-5px);
        }

        .navbar {
            background: rgba(0, 0, 0, 0.2) !important;
            backdrop-filter: blur(15px);
        }

        /* Estilo para la foto de perfil */
        .profile-img {
            width: 250px; 
            height: 250px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid var(--glass-border);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .hobby-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: #EDEDED; /* Color actualizado a gris claro */
        }

        .btn-admin {
            background: var(--accent-red);
            border: none;
            color: white;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-admin:hover {
            background: var(--accent-red-hover);
            color: white;
            box-shadow: 0 5px 15px rgba(230, 57, 70, 0.4);
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">UTPL - Blog personal</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="contacto.php">Contacto</a></li>
                    <li class="nav-item ms-lg-2">
                        <a class="nav-link" href="https://github.com/gdjijon/desarrollowebtarea2" target="_blank" rel="noopener noreferrer" title="Ver código en GitHub">
                            <i class="bi bi-github fs-5"></i>
                        </a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <a href="login.php" class="btn btn-admin">
                            <i class="bi bi-person-lock me-2"></i>Ingresar Portal
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="container mt-5 pt-5">
        <div class="row align-items-center">
            <div class="col-lg-5 text-center mb-4 mb-lg-0">
                <img src="images/mifoto.jpg" alt="Foto de Guillermo Jijón" class="profile-img">
            </div>
            <div class="col-lg-7">
                <div class="glass-card">
                    <h1 class="fw-bold mb-3">¡Hola! Soy Guillermo Jijón</h1>
                    <p class="lead mb-4">
                        Bienvenido a mi sitio personal, curso la carrera de Tecnologías de la Información, una persona apasionada por la tecnología y el conocimiento. En este lugar podrás conocer más sobre mí.
                    </p>
                    <p>
                        Mi objetivo es aprender cada día más y emplear mis conocimientos para aportar con soluciones, tanto en el aspecto laboral y personal. Me gusta enfrentar retos nuevos y que mis fuerzas y resultados siempre se encuentren fundamentados en mi fe en Dios, a quien lo debo todo.
                    </p>
                </div>
            </div>
        </div>
    </header>

    <section class="container my-5">
        <h2 class="text-start fw-bold mb-5">Mis Hobbies</h2>
        <div class="row g-4 text-center">
            
            <div class="col-md-4">
                <div class="glass-card h-100">
                    <i class="bi bi-music-note-beamed hobby-icon"></i>
                    <h3>Música</h3>
                    <p>La música representa un gran aspecto de mis aficiones, soy graduado en música imparto clases y practico piano, canto y flauta traversa.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="glass-card h-100">
                    <i class="bi bi-sliders hobby-icon"></i>
                    <h3>Programar / Audio / Video</h3>
                    <p>Me gusta todo lo relacionado a la programación y al manejo de sistemas de producción musical DAW, de grabación de audio y edición de video.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="glass-card h-100">
                    <i class="bi bi-controller hobby-icon"></i>
                    <h3>Videojuegos</h3>
                    <p>Disfruto de la estrategia y la narrativa competitiva en mis ratos libres, tanto en juegos shooter como de rol.</p>
                </div>
            </div>

        </div>
    </section>

    <footer class="container text-center py-5 mt-5">
        <hr class="mb-4 opacity-25">
        <p>© 2026 Guillermo Jijon - Desarrollo Web UTPL</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="https://github.com/gdjijon/desarrollowebtarea2" target="_blank" rel="noopener noreferrer" class="text-white fs-4">
                <i class="bi bi-github"></i>
            </a>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>