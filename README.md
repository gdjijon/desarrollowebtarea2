##############################################################################################################################################################
##############################################################################################################################################################
# README

Aprendizaje práctico - experimental (APE) , 
Diseñar y desarrollar una página web que incluya información sobre el usuario y un formulario de contacto funcional con PHP que permita enviar un mensaje. 

# Proyecto de Desarrollo Web - UTPL 

Plataforma web dinámica desarrollada en PHP y MySQL que integra un blog personal de presentación y un sistema de gestión de mensajes de contacto con Control de Acceso Basado en Roles (RBAC).

-----------------------------------------------

**DATOS IMPORTANTES**

**URL de Producción:**
https://blogdesarrolloweb.page.gd/index.php
**Credenciales de usuario con permisos admin para testing/validacion de envio de mensajes y creacion de usuarios*
USUARIO:    datorres@utpl.edu.ec
CONTRASEÑA: Xy82Q3%$

ESTE USUARIO YA FUE REGISTRADO PREVIAMENTE Y POR SENTENCIA SQL SE LE DIO PERMISOS COMO ADMINISTRADOR
TODOS LOS USUARIOS POR DEFECTO SE REGITRAN COMO USUARIOS REGULARES, SOLO PERMISOS DE ADMIN SE DAN DIRECTO POR UPDATE A BASE:
UPDATE usuarios SET rol = 'admin' WHERE correo = 'datorres@utpl.edu.ec';

-----------------------------------------------

## 1. Arquitectura y Páginas del Sistema

El proyecto se compone de las siguientes interfaces principales, diseñadas usando HTML5, CSS3 y el framework Bootstrap 5:



* **`index.php` (Inicio):
** Página de aterrizaje (*landing page*) pública. 
Presenta la información personal, perfil académico y aficiones del autor.


* **`contacto.php` (Contacto):
** Formulario público para el envío de mensajes. 
 validación de longitud, validación de formato de correo electrónico y un sistema Captcha de seguridad anti-bots.


* **`registro.php` (Creación de Cuenta):
** Interfaz para nuevos usuarios. Exige contraseñas de alta seguridad (Regex), validación de coincidencia 
con visibilidad dinámica de caracteres (UI/UX).

* **`login.php` (Acceso Portal
 Punto de autenticación del sistema. Verifica las credenciales cifradas contra la base de datos 
 y genera las variables de sesión del usuario.


* **`admin.php` (Portal de Usuarios):** Tablero de control protegido por sesión. 
Renderiza una tabla de datos interactiva con la información de los mensajes procesados en la base de datos.


* **Navegación Global:** Todas las vistas públicas integran un encabezado estandarizado con un ícono de GitHub 
que redirige directamente al código fuente del repositorio.


-------------------------------------------------

## 2. Flujos de Uso y Usabilidad (Usuario Final)

### Registro de Usuarios y Autenticación

1.  **Creación:** El usuario debe navegar a **Acceso Portal > Crear nueva cuenta**. 
El sistema solicita un correo electrónico válido y una contraseña que debe cumplir estrictamente con: mínimo 8 caracteres, 
una mayúscula, una minúscula, un número y un carácter especial (`%`, `&`, `+`, `-`).

2.  **Seguridad Visual:** Los campos de contraseña incorporan un ícono de "ojo" para visualizar temporalmente el texto introducido. 
Todo registro exige resolver un código alfanumérico en imagen (Captcha).

3.  **Inicio de Sesión:** Tras el registro, el usuario ingresa sus credenciales en `login.php`. 
El sistema valida el hash criptográfico e inicia una sesión segura.

--------------------------------------------------


### Interacciones en el Portal implementadas (`admin.php`)

1. **Control de Acceso:** La información mostrada depende del nivel de privilegios (`rol`) del usuario autenticado:

    * **Usuario Normal (Por defecto):** Visualiza un historial restringido bajo el título "Mensajes Enviados". 
    Solo tiene acceso de lectura a los formularios que él mismo ha enviado desde la página de contacto (filtrado por su correo).

2.  **Administrador:** Visualiza la "Bandeja Global de Mensajes", accediendo a la totalidad de registros ingresados al sistema por cualquier usuario o visitante.

3.  **Manejo de Tabla y Ordenamiento:** * La tabla presenta las columnas: *Fecha del mensaje, Nombre, Correo, Mensaje, Usuario desde* y *Administrador (si/no)*.

4. **Filtro :** En la esquina superior derecha existe un menú desplegable "Ordenar por". Al seleccionar una columna (por ejemplo, "Nombre" o "Correo"), 
    la tabla se recarga y ordena alfabética o cronológicamente la información sin requerir recargar la página manualmente.

5. **Por Defecto:** Al cargar la página, los registros se muestran ordenados por "fecha del mensaje" de forma descendente (los más recientes primero).

6. **Visibilidad:** Al cargar la página, solamente el usuario normal verá sus propios mensajes enviados, los admin verán todos.

7. **Separacion de usuarios" Al ver la información se agregó columna si el usuario que envia el mensaje esta registrado o no en el portal, de no estarlo se indica aquello.

---------------------------------------------

## 3. Estándares de Seguridad y Sintaxis Relevante

El código backend (PHP) está estructurado para mitigar vulnerabilidades comunes :

1.  **Prevención de Inyección SQL (SQLi):** Uso estricto de **PDO (PHP Data Objects)** con consultas preparadas y la sintaxis moderna de arreglos `execute([])`. 
   Emulación de prepares desactivada (`PDO::ATTR_EMULATE_PREPARES = false`).

2. **Criptografía de Credenciales:** Las contraseñas jamás se almacenan en texto plano. Se utiliza `password_hash()` 
   con el algoritmo nativo **Bcrypt** para el almacenamiento y `password_verify()` para la autenticación.

3. **Prevención de Cross-Site Scripting (XSS):** Todo dato proveniente de un formulario (POST) o extraído de la base de datos hacia el frontend es sanitizado 
usando `htmlspecialchars()` (con flags `ENT_QUOTES` y `UTF-8`) o `filter_var()`.

4. **Secuestro de Sesión (Session Hijacking):** Implementación de `session_regenerate_id(true)` tras un inicio de sesión exitoso para mitigar ataques de fijación de sesión.

---------------------------------------------

## 4. Migración a Producción y Configuración de Base de Datos

Durante el pase a producción, se requirió adaptar los parámetros de conexión de `localhost` hacia la arquitectura del servicio de hosting en la nube (InfinityFree). 

Los ajustes realizados en `conexion.php` fueron:

* **Hostname:** Transición de `localhost` al nodo remoto provisto (`sql300.infinityfree.com`).

* **Prefijos de Nomenclatura:** Los paneles de hosting compartido inyectan identificadores a las instancias. 
El usuario `root` y la base genérica pasaron a adoptar el prefijo de suscripción asignado (`if0_42034389`).



### Consolidado de Scripts SQL para la base

Para inicializar el sistema web y sus dos usuarios operativos administradores:

`gdjijon@utpl.edu.ec` `datorres@utpl.edu.ec`

, se debe seguir este flujo en el gestor de base de datos (phpMyAdmin).

--------------------------------------

## 5. Consideraciones al migrar la pagina a la web

#### CREACION DE BASE DE DATOS: 

Entorno 1: Servidor de Desarrollo (Localhost), SQL usado:

CREATE DATABASE IF NOT EXISTS db_dweb_gjijon;
USE db_dweb_gjijon;

-- Tabla para el panel de administración
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    correo VARCHAR(150) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla para capturar los datos del formulario de contacto
CREATE TABLE mensajes_contacto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL,
    mensaje VARCHAR(500) NOT NULL,
    fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


Entorno 2 Hosting Web:Sólo se crearon las tablas ya que la base se creo por wizard del administrador del hosting

-- Tabla para el panel de administración
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    correo VARCHAR(150) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla para capturar los datos del formulario de contacto
CREATE TABLE mensajes_contacto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL,
    mensaje VARCHAR(500) NOT NULL,
    fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

#### Registros agregados posteriormente para mejor presentacion de la tabla

ALTER TABLE usuarios ADD COLUMN rol VARCHAR(20) NOT NULL DEFAULT 'usuario';
ALTER TABLE usuarios ADD COLUMN fecha_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;


#### SQL para dar permisos de admin a usuario creado: 

UPDATE usuarios SET rol = 'admin' WHERE correo = 'gdjijon@utpl.edu.ec';

####Cambios en coexion.php para poder conectarse a la base en el hosting web


<?php
$host = 'sql300.infinityfree.com';
$dbname = 'if0_42034389_db_dweb_gjijon';
$username = 'if0_42034389'; // Ajustar con las credenciales de tu hosting gratuito
$password = 'PbGNMSGvVao';     // Ajustar con las credenciales de tu hosting gratuito


----------------------------------------------------------------

## 5. Consideraciones finales INSTRUCTIVO PARA DOCENTE DE SUGERENCIA DE COMO VALIDAR LOS REQUERIMIENTOS

1. **Tener presente el acceso del iconode github que lleva directo a los archivos del proyecto

2. ** el mensaje de texto de contacto esta limitado a 500 caracteres

3. ** Estimada docente para las pruebas que realice se sugiere:


1.- crear un usuario A
2.- ingresar al portal del usuario A , se vera que tiene 0 mensajes enviados
3.  salir del portal personal
4.- enviar un mensaje con el usuario A
5.- volver a entrar al portal con el usuario A, se verá que tiene su mensaje enviado
6.- ingresar al portal con las credenciales de docente:

    USUARIO:    datorres@utpl.edu.ec
    CONTRASEÑA: Xy82Q3%$

7.- validar que el portal admin muestra el mensaje enviado por el usuario A , cambio en titulos 
    haciendo referencia a portal admin y usuario admin
8.- enviar mensaje con el usuario admin datorres@utpl.edu.ec
9.- entrar de nuevo al portal como usuario A, solamente estará el mensaje del usuario A
10.-salir del portal personal usuario A
11.-entrar al portal como datorres@utpl.edu.ec
12.-verificar que se encuentra el mensaje enviado por el usuario A y por datorres@utpl.edu.ec


Con ello se confirma el flujo de trabajo normal de la página.


*****UTPL-2026-DESARROLLO WEB-GUILLERO JIJON*****
