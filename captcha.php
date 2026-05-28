<?php
session_start();

// Generar una cadena aleatoria de 5 caracteres
$caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
$captcha_texto = substr(str_shuffle($caracteres), 0, 5);

// Guardar el texto en la sesión
$_SESSION['captcha'] = $captcha_texto;

// Crear la imagen (ancho, alto)
$imagen = imagecreate(120, 40);

// Colores (Fondo blanco, texto negro, líneas grises)
$color_fondo = imagecolorallocate($imagen, 255, 255, 255);
$color_texto = imagecolorallocate($imagen, 0, 0, 0);
$color_lineas = imagecolorallocate($imagen, 200, 200, 200);

// Añadir algo de ruido visual (líneas) para seguridad básica
for ($i = 0; $i < 5; $i++) {
    imageline($imagen, 0, rand() % 40, 120, rand() % 40, $color_lineas);
}

// Añadir el texto a la imagen
imagestring($imagen, 5, 35, 12, $captcha_texto, $color_texto);

// Enviar las cabeceras HTTP para renderizar la imagen
header('Content-type: image/png');
imagepng($imagen);

// Liberar memoria
imagedestroy($imagen);
?>