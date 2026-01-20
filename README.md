<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

<h1>Práctica 11: Migración a framework (BC5)</h1>

<ol>
    <li>Justificación de la elección del framework
        <p>Se ha elegido Laravel como framework para el desarrollo de esta red social debido a que proporciona una arquitectura clara basada en el patrón MVC, facilitando la organización del código. Además Laravel incluye de forma nativa herramientas para la gestión de rutas, controladores, modelos, validación de datos, autenticación.
</p>
    </li>
    <li>Patrones de diseño aplicados
        <h2>Patrón Repository</h2>
        <p>En el proyecto se ha aplicado el patrón Repository, implementado en la carpeta app/Repositories, con el objetivo de abstraer la lógica de acceso a los datos y separarla de la lógica de negocio. En lugar de utilizar una base de datos, la persistencia de la información se realiza mediante archivos JSON, gestionados desde repositorios específicos como UserRepositoryJson y MessageRepositoryJson. Esto nos permite en un futuro, que el sistema de persistencia pueda cambiarse (por ejemplo, a una base de datos) sin necesidad de modificar la lógica principal de la aplicación.</p>
    </li>
    <li>Instrucciones de instalación y arranque en local
        <p>Las siguientes instrucciones tienen como objetivo guiarte en el proceso de obtención del código fuente y la configuración del entorno necesario para ejecutar este proyecto en tu máquina local o en una máquina virtual.</p>
        <h2>Prerrequisitos</h2>
        <p>Este proyecto está desarrollado con el framework Laravel. Se asume que el entorno donde se desplegará cuenta previamente con PHP y el gestor de dependencias Composer instalados (por ejemplo, mediante XAMPP en Linux o una instalación nativa en Ubuntu).
</p>
    <h2>Pasos de Instalación</h2>
    <ol>
        <li>Obtención del código fuente
            <p>Para comenzar, es necesario clonar el repositorio en el directorio de trabajo de tu máquina virtual o servidor local. Utiliza el siguiente comando:</p>
            <code>git clone https://github.com/sotoo6/redSocial.git </code>
            <br>
            <code>cd redSocial</code>
        </li>
        <li>Instalación de dependencias
            <p>Dado que el repositorio no incluye las librerías del núcleo de Laravel ni paquetes de terceros, es necesario instalarlos manualmente. El siguiente comando leerá el archivo <code>composer.json</code> y descargará todas las dependencias requeridas en la carpeta <code>vendor</code>:</p>
            <code>composer install</code>
        </li>
        <li>Configuración del entorno
            <p>El proyecto ya incluye el archivo de configuración principal .env en el directorio raíz, por lo que no es necesario crearlo manualmente.
</p>
            <p>Nota: Si por alguna razón el archivo no existiera, puedes generar uno copiando el archivo de ejemplo: <code>cp .env.example .env.</code></p>
            <p>Esta aplicación ha sido diseñada para utilizar persistencia de datos en archivos JSON (ubicados en <code>storage/app/data/</code>) y drivers de archivo para el manejo de sesiones. Por lo tanto, no es necesario configurar ni arrancar una base de datos relacional (como MySQL) para su funcionamiento.
</p>
        </li>
        <li>Verificación de archivos de datos
            <p>Para evitar errores de lectura al iniciar la aplicación, asegúrate de que existen los archivos de almacenamiento con una estructura válida. Ejecuta estos comandos para crearlos si no existen</p>
            <code>mkdir -p storage/app/data</code>
            <br>
            <code>echo "[]" > storage/app/data/users.json </code>
            <br>
            <code>echo "[]" > storage/app/data/messages.json</code>
        </li>
        <li>Generación de la clave de aplicación
            <p>Aunque dispongas del archivo de entorno, Laravel requiere asegurar que exista una clave de encriptación válida configurada para gestionar la seguridad de las sesiones. Puedes generar o regenerar esta clave ejecutando el comando (usando el PHP de XAMPP para evitar conflictos):</p>
            <code>/opt/lampp/bin/php artisan key:generate</code>
        </li>
        <li>Permisos de escritura
            <p>Debido a que la arquitectura del proyecto almacena la información de usuarios y mensajes en archivos físicos dentro del directorio <code>storage</code>, es indispensable que el servidor web disponga de permisos de escritura en estas carpetas. De no concederse estos permisos, acciones como el registro de usuarios fallarán.</p>
            <p>Ejecuta el siguiente comando para asignar los permisos necesarios:</p>
            <code>chmod -R 775 storage bootstrap/cache</code>
        </li>
        <li>Ejecución del Servidor
            <p>Para iniciar el entorno de desarrollo, se utiliza el servidor interno proporcionado por Artisan (invocando el PHP de XAMPP). Se recomienda especificar el host y el puerto para asegurar la accesibilidad externa:</p>
            <code>/opt/lampp/bin/php artisan serve --host=0.0.0.0 --port=8000</code>
            <p>Una vez ejecutado el comando, la aplicación estará accesible dependiendo de tu método de conexión:</p>
            <ul>
                <li>Si usas VS Code (Remote-SSH) o estás dentro de la VM: Accede a <code>http://localhost:8000</code> (el editor reenviará el puerto automáticamente).</li>
                <li>Si accedes desde Windows/Mac sin reenvío de puertos: Deberás usar la IP de la máquina virtual: <code>http://<IP_DE_TU_VM>:8000</code> (puedes consultar tu IP ejecutando <code>hostname -I</code> en la terminal).</li>
            </ul>
        </li>
    </ol>
    </li>
    <li>Listado de rutas y roles requeridos
        <ol>
            <li>Rutas públicas
                <ul>
                    <li>GET /
                        <p>Ruta raíz de la aplicación.</p>
                        <p>El comportamiento depende del estado de sesión:</p>
                        <ul>
                            <li>Si el usuario NO está autenticado se redirige automáticamente a /login.</li>
                            <li>Si el usuario SÍ está autenticado se redirige a /home, donde se muestran los mensajes publicados.</li>
                        </ul>
                        <p>Esta ruta sirve como punto de entrada general y garantiza que cada usuario sea enviado a la vista adecuada según su estado.</p>
                    </li>
                    <li>GET /login
                        <p>Muestra el formulario de inicio de sesión.</p>
                        <p>Contiene los campos de email y contraseña, la cual se hashea en SHA-256 en el frontend antes de enviarse al servidor.</p>
                    </li>
                    <li>POST /login
                        <p>Procesa los datos enviados desde el formulario de inicio de sesión.</p>
                        <p>Acciones realizadas:</p>
                        <ul>
                            <li>Comprueba que se han introducido email y contraseña.</li>
                            <li>Busca el usuario en users.json.</li>
                            <li>Verifica la contraseña hasheada usando password_verify() sobre el hash almacenado.</li>
                            <li>Ejecuta session_regenerate_id(true) para evitar fijación de sesión.</li>
                            <li>Guarda en $_SESSION['user'] los datos mínimos del usuario (id, nombre, email, rol, tema).</li>
                            <li>Redirige a /home si todo es correcto. En caso de error, vuelve a mostrar la vista /login con un mensaje de error.</li>
                        </ul>
                    </li>
                    <li>POST /logout
                        <p>Método que se usa para cerrar sesión.</p>
                    </li>
                    <li>GET /register
                        <p>Muestra el formulario de registro donde se introducen nombre, email, contraseña y un rol (alumno o profesor). La contraseña se hashea en SHA-256 en el navegador antes de enviarse al servidor.</p>
                    </li>
                    <li>POST /register
                        <p>Procesa el registro de un nuevo usuario. Acciones que realiza:</p>
                        <ul>
                            <li>Comprueba que los campos requeridos no estén vacíos.</li>
                            <li>Recibe la contraseña hasheada en SHA-256 desde el frontend.</li>
                            <li>Vuelve a hashear ese valor con password_hash() para almacenarlo de forma segura.</li>
                            <li>Guarda el nuevo usuario en users.json con un ID aleatorio y tema por defecto.</li>
                            <li>Regenera la sesión para evitar problemas de seguridad.</li>
                            <li>Redirige a /login para que el usuario pueda autenticarse.</li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li>Rutas protegidas (requieren sesión)
                <p>Estas rutas solo pueden usarse si el usuario está autenticado. En caso contrario, se redirige automáticamente a /login.</p>
                <ul>
                    <li>GET /home
                        <p>Vista principal del usuario logueado. Muestra únicamente los mensajes cuyo estado es published, tal y como exige el enunciado.</p>
                        <p>Gestionado por el controlador <code>MessageController::listMessages()</code>.</p>
                    </li>
                    <li>GET /messages/new
                        <p> Formulario para que el usuario cree un nuevo mensaje y seleccione una asignatura. Disponible para alumnos y profesores.</p>
                    </li>
                    <li>POST /messages
                        <p>Procesa el envío de un nuevo mensaje:</p>
                        <ul>
                            <li>Valida que el texto tenga entre 1 y 280 caracteres.</li>
                            <li>Revisa que no haya patrones peligrosos (<code>script</code>, <code>onerror=</code>, <code>drop table</code>...).</li>
                            <li>Comprueba que no haya palabrotas o contenido inapropiado.</li>
                            <li>Si es válido, guarda el mensaje con estado pending en messages.json.</li>
                            <li>El mensaje quedará visible solo después de ser aprobado por un profesor.</li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li>Moderación (solo profesores)
                <p>Estas rutas están reservadas para usuarios con rol profe. Si un alumno intenta acceder, es redirigido al /home.</p>
                <ul>
                    <li>GET /moderation
                        <p>Muestra la cola de mensajes pendientes de moderación. Cada mensaje aparece con su autor, asignatura y texto, junto con botones para aprobar o rechazar.</p>
                    </li>
                    <li>POST /moderation/{id}/approve
                        <p>Actualiza el estado del mensaje seleccionado a published. Tras actualizar el JSON, redirige de vuelta a la vista de moderación.</p>
                    </li>
                    <li>POST /moderation/{id}/reject</li>
                    <li>GET /moderation/invalid</li>
                </ul>
            </li>
            <li>Preferencias</li>
        </ol>
    </li>
    <li>Validación y sanitización implementada</li>
    <li>Usuarios de prueba (al menos 1 alumno y 1 profesor)</li>
</ol>
