<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

<h1>Práctica 11: Migración a framework (BC5)</h1>

<ol>
  <li>
    <h2>Justificación de la elección del framework</h2>
    <p>
      Se ha elegido Laravel como framework para el desarrollo de esta red social debido a que proporciona una arquitectura clara basada en el patrón MVC, facilitando la organización del código. Además Laravel incluye de forma nativa herramientas para la gestión de rutas, controladores, modelos, validación de datos, autenticación.
    </p>
  </li>

  <li>
    <h2>Patrones de diseño aplicados</h2>
    <h3>Patrón Repository</h3>
    <p>
      En el proyecto se ha aplicado el patrón Repository, implementado en la carpeta app/Repositories, con el objetivo de abstraer la lógica de acceso a los datos y separarla de la lógica de negocio. En lugar de utilizar una base de datos, la persistencia de la información se realiza mediante archivos JSON, gestionados desde repositorios específicos como UserRepositoryJson y MessageRepositoryJson. Esto nos permite en un futuro, que el sistema de persistencia pueda cambiarse (por ejemplo, a una base de datos) sin necesidad de modificar la lógica principal de la aplicación.
    </p>
    <h3>Patrón Modelo Vista Controlador (MVC)</h3>
    <p>En el proyecto se ha aplicado el patrón Modelo Vista Controlador (MVC), propio del framework Laravel, con el objetivo de separar responsabilidades y mejorar la organización del código. Los controladores gestionan las peticiones y la lógica principal de la aplicación, las vistas se encargan de mostrar la información al usuario y los modelos sirven como apoyo para estructurar y manejar los datos, aunque la persistencia se realiza mediante archivos JSON.</p>
  </li>

  <li>
    <h2>Instrucciones de instalación y arranque en local</h2>
    <p>
      Las siguientes instrucciones tienen como objetivo guiarte en el proceso de obtención del código fuente y la configuración del entorno necesario para ejecutar este proyecto en tu máquina local o en una máquina virtual.
    </p>
    <h3>Prerrequisitos</h3>
    <p>
      Este proyecto está desarrollado con el framework Laravel. Se asume que el entorno donde se desplegará cuenta previamente con PHP y el gestor de dependencias Composer instalados (por ejemplo, mediante XAMPP en Linux o una instalación nativa en Ubuntu).
    </p>
    <h3>Pasos de Instalación</h3>
    <ol>
      <li>
        <h4>Obtención del código fuente</h4>
        <p>
          Para comenzar, es necesario clonar el repositorio en el directorio de trabajo de tu máquina virtual o servidor local. Utiliza el siguiente comando:
        </p>
        <code>git clone https://github.com/sotoo6/redSocial.git</code>
        <br>
        <code>cd redSocial</code>
      </li>
      <li>
        <h4>Instalación de dependencias</h4>
        <p>
          Dado que el repositorio no incluye las librerías del núcleo de Laravel ni paquetes de terceros, es necesario instalarlos manualmente. El siguiente comando leerá el archivo <code>composer.json</code> y descargará todas las dependencias requeridas en la carpeta <code>vendor</code>:
        </p>
        <code>composer install</code>
      </li>
      <li>
        <h4>Configuración del entorno</h4>
        <p>
          El proyecto ya incluye el archivo de configuración principal <code>.env</code> en el directorio raíz, por lo que no es necesario crearlo manualmente.
        </p>
        <p>
          Nota: Si por alguna razón el archivo no existiera, puedes generar uno copiando el archivo de ejemplo: <code>cp .env.example .env</code>.
        </p>
        <p>
          Esta aplicación ha sido diseñada para utilizar persistencia de datos en archivos JSON (ubicados en <code>storage/app/data/</code>) y drivers de archivo para el manejo de sesiones. Por lo tanto, no es necesario configurar ni arrancar una base de datos relacional (como MySQL) para su funcionamiento.
        </p>
      </li>
      <li>
        <h4>Verificación de archivos de datos</h4>
        <p>
          Para evitar errores de lectura al iniciar la aplicación, asegúrate de que existen los archivos de almacenamiento con una estructura válida. Ejecuta estos comandos para crearlos si no existen:
        </p>
        <code>mkdir -p storage/app/data</code>
        <br>
        <code>echo "[]" &gt; storage/app/data/users.json</code>
        <br>
        <code>echo "[]" &gt; storage/app/data/messages.json</code>
      </li>
      <li>
        <h4>Generación de la clave de aplicación</h4>
        <p>
          Aunque dispongas del archivo de entorno, Laravel requiere asegurar que exista una clave de encriptación válida configurada para gestionar la seguridad de las sesiones. Puedes generar o regenerar esta clave ejecutando el comando (usando el PHP de XAMPP para evitar conflictos):
        </p>
        <code>/opt/lampp/bin/php artisan key:generate</code>
      </li>
      <li>
        <h4>Permisos de escritura</h4>
        <p>
          Debido a que la arquitectura del proyecto almacena la información de usuarios y mensajes en archivos físicos dentro del directorio <code>storage</code>, es indispensable que el servidor web disponga de permisos de escritura en estas carpetas. De no concederse estos permisos, acciones como el registro de usuarios fallarán.
        </p>
        <p>Ejecuta el siguiente comando para asignar los permisos necesarios:</p>
        <code>chmod -R 775 storage bootstrap/cache</code>
      </li>
      <li>
        <h4>Ejecución del Servidor</h4>
        <p>
          Para iniciar el entorno de desarrollo, se utiliza el servidor interno proporcionado por Artisan (invocando el PHP de XAMPP). Se recomienda especificar el host y el puerto para asegurar la accesibilidad externa:
        </p>
        <code>/opt/lampp/bin/php artisan serve --host=0.0.0.0 --port=8000</code>
        <p>Una vez ejecutado el comando, la aplicación estará accesible dependiendo de tu método de conexión:</p>
        <ul>
          <li>
            Si usas VS Code (Remote-SSH) o estás dentro de la VM: Accede a <code>http://localhost:8000</code> (el editor reenviará el puerto automáticamente).
          </li>
          <li>
            Si accedes desde Windows/Mac sin reenvío de puertos: Deberás usar la IP de la máquina virtual:
            <code>http://&lt;IP_DE_TU_VM&gt;:8000</code> (puedes consultar tu IP ejecutando <code>hostname -I</code> en la terminal).
          </li>
        </ul>
      </li>
    </ol>
  </li>

  <li>
    <h2>Listado de rutas y roles requeridos</h2>
    <ol>
      <li>
        <h3>Rutas públicas</h3>
        <ul>
          <li>
            <h4>GET /</h4>
            <p>Ruta raíz de la aplicación.</p>
            <p>El comportamiento depende del estado de sesión:</p>
            <ul>
              <li>Si el usuario NO está autenticado se redirige automáticamente a /login.</li>
              <li>Si el usuario SÍ está autenticado se redirige a /home, donde se muestran los mensajes publicados.</li>
            </ul>
            <p>
              Esta ruta sirve como punto de entrada general y garantiza que cada usuario sea enviado a la vista adecuada según su estado.
            </p>
          </li>
          <li>
            <h4>GET /login</h4>
            <p>Muestra el formulario de inicio de sesión.</p>
            <p>Contiene los campos de email y contraseña, la cual se hashea en SHA-256 en el frontend antes de enviarse al servidor.</p>
          </li>
          <li>
            <h4>POST /login</h4>
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
          <li>
            <h4>POST /logout</h4>
            <p>Método que se usa para cerrar sesión.</p>
          </li>
          <li>
            <h4>GET /register</h4>
            <p>
              Muestra el formulario de registro donde se introducen nombre, email, contraseña y un rol (alumno o profesor). La contraseña se hashea en SHA-256 en el navegador antes de enviarse al servidor.
            </p>
          </li>
          <li>
            <h4>POST /register</h4>
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
      <li>
        <h3>Rutas protegidas (requieren sesión)</h3>
        <p>Estas rutas solo pueden usarse si el usuario está autenticado. En caso contrario, se redirige automáticamente a /login.</p>
        <ul>
          <li>
            <h4>GET /home</h4>
            <p>Vista principal del usuario logueado. Muestra únicamente los mensajes cuyo estado es published, tal y como exige el enunciado.</p>
            <p>Gestionado por el controlador <code>MessageController::listMessages()</code>.</p>
          </li>
          <li>
            <h4>GET /messages/new</h4>
            <p>Formulario para que el usuario cree un nuevo mensaje y seleccione una asignatura. Disponible para alumnos y profesores.</p>
          </li>
          <li>
            <h4>POST /messages</h4>
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
      <li>
        <h3>Moderación (solo profesores)</h3>
        <p>Estas rutas están reservadas para usuarios con rol profe. Si un alumno intenta acceder, es redirigido al /home.</p>
        <ul>
          <li>
            <h4>GET /moderation</h4>
            <p>Muestra la cola de mensajes pendientes de moderación. Cada mensaje aparece con su autor, asignatura y texto, junto con botones para aprobar o rechazar.</p>
          </li>
          <li>
            <h4>POST /moderation/{id}/approve</h4>
            <p>Actualiza el estado del mensaje seleccionado a published. Tras actualizar el JSON, redirige de vuelta a la vista de moderación.</p>
          </li>
          <li>
            <h4>POST /moderation/{id}/reject</h4>
            <p>Actualiza el estado del mensaje a rejected. También redirige nuevamente a la lista de pendientes.</p>
            <p>El acceso se controla mediante: <code>$_SESSION['user']['role'] === 'profe'</code></p>
          </li>
          <li>
            <h4>GET /moderation/invalid</h4>
            <p>Muestra la cola de mensajes rechazados. Cada mensaje aparece con su autor, asignatura y texto.</p>
          </li>
        </ul>
      </li>
      <li>
        <h3>Preferencias</h3>
        <ul>
          <li>
            <h4>GET /theme/toggle</h4>
            <p>Cambia el tema visual de la aplicación (claro u oscuro). Cada vez que se ejecuta:</p>
            <ul>
              <li>Actualiza el tema guardado en la sesión, aplicándolo de inmediato.</li>
              <li>Guarda la preferencia en la cookie del usuario (30 días).</li>
              <li>Actualiza el tema en users.json para mantener persistencia tras cerrar sesión.</li>
              <li>Redirige a la página desde la que el usuario realizó la acción, usando <code>?return=/ruta</code>.</li>
            </ul>
          </li>
        </ul>
      </li>
    </ol>
  </li>

  <li>
    <h2>Validación y sanitización implementada</h2>
    <ol>
      <li>
        <h3>Registro de Usuario</h3>
        <p>Durante el registro se aplican validaciones para garantizar que los datos almacenados sean coherentes y seguros.</p>
        <ul>
          <li>Nombre obligatorio: evita registros incompletos.</li>
          <li>Email obligatorio y único</li>
          <li>
            Contraseña segura: primero se genera un hash SHA-256 en el frontend, lo que evita que la contraseña viaje en texto plano. Posteriormente, ese SHA-256 se vuelve a proteger con password_hash(), generando un hash seguro adaptado al servidor. Este doble proceso mantiene las contraseñas protegidas tanto en la transmisión como en el almacenamiento.
          </li>
          <li>Rol obligatorio.</li>
        </ul>
      </li>
      <li>
        <h3>Inicio de sesión</h3>
        <p>El inicio de sesión valida credenciales y protege la sesión del usuario:</p>
        <ul>
          <li>Con el <code>$request->validate()</code> comprobamos que el email y la password están presentes y tienen el formato correcto.</li>
          <li>La contraseña enviada (ya en SHA-256) se compara con el hash almacenado mediante <code>password_verify()</code>, asegurando autenticación segura.</li>
          <li>Tras iniciar sesión correctamente, se ejecuta <code>session_regenerate_id(true)</code>, lo que evita ataques de fijación de sesión (session fixation).</li>
        </ul>
      </li>
      <li>
        <h3>Creación de mensajes</h3>
        <p>Durante la creación de mensajes se aplican varias capas de control:</p>
        <ol>
          <li>
            <h4>Validaciones de contenido</h4>
            <p>El texto debe tener entre 1 y 280 caracteres, cumpliendo los requisitos de publicación.</p>
            <p>La asignatura debe seleccionarse de un select cerrado, garantizando que el usuario no envíe categorías inventadas.</p>
          </li>
          <li>
            <h4>Bloqueo de patrones peligrosos</h4>
            <p>Para evitar inyecciones de JavaScript o SQL, se bloquean:</p>
            <ul>
              <li>script</li>
              <li>onerror=</li>
              <li>onload=</li>
              <li>onmouseover=</li>
              <li>javascript:</li>
              <li>eval\(|iframe|embed|object/i)</li>
            </ul>
            <p>Cualquier mensaje que contenga estos patrones se rechaza automáticamente.</p>
          </li>
          <li>
            <h4>Filtro de lenguaje inapropiado</h4>
            <p>El sistema incluye un listado de palabrotas; si el texto contiene alguna, la publicación es rechazada.</p>
          </li>
          <li>
            <h4>Estado inicial</h4>
            <p>Los mensajes válidos se guardan inicialmente como pending, esperando moderación por parte de un profesor.</p>
          </li>
          <li>
            <h4>Sanitización al mostrar</h4>
            <p>Al renderizar los mensajes publicados:</p>
            <ul>
              <li>htmlspecialchars() evita XSS convirtiendo caracteres especiales.</li>
              <li>nl2br() mantiene los saltos de línea del usuario.</li>
            </ul>
          </li>
        </ol>
      </li>
      <li>
        <h3>Moderación</h3>
        <p>La moderación es exclusiva del rol profesor:</p>
        <ul>
          <li>Solo usuarios con role = 'profe' pueden acceder a la cola de moderación.</li>
          <li>Los mensajes se pueden aprobar (estado published) o rechazar (estado rejected).</li>
          <li>Los cambios se guardan de manera persistente en messages.json.</li>
        </ul>
        <p>Este sistema asegura un control básico del flujo de contenido.</p>
      </li>
      <li>
        <h3>Sesiones y cookies</h3>
        <p>El proyecto aplica las medidas recomendadas para protección de sesiones:</p>
        <ul>
          <li>
            Cookie de sesión configurada con:
            <ul>
              <li>HttpOnly: evita acceso desde JavaScript.</li>
              <li>SameSite=Lax: reduce ataques CSRF evitando envío automático de cookies en peticiones externas.</li>
            </ul>
          </li>
          <li>Rotación de ID de sesión: Tras un login correcto, se genera un nuevo ID para evitar secuestro de sesión.</li>
          <li>Cookie de tema (claro/oscuro): Se guarda durante 30 días, se actualiza cada vez que el usuario cambia el tema y se sincroniza con la sesión y con users.json.</li>
        </ul>
        <p>Esto mantiene las preferencias persistentes y protegidas.</p>
      </li>
    </ol>
  </li>
  <li>
    <h2>Usuarios de prueba (al menos 1 alumno y 1 profesor)</h2>
    <h3>Usuario Profesor</h3>
    <ul>
      <li>Nombre: Profesor</li>
      <li>Email: profesor@gmail.com</li>
      <li>Contraseña: profe123</li>
    </ul>
    <h3>Usuario Alumno</h3>
    <ul>
      <li>Nombre: Alumno</li>
      <li>Email: alumno@gmail.com</li>
      <li>Contraseña: alumno321</li>
    </ul>
  </li>
</ol>

<footer>Lucía Soto y Andrea Pollán</footer>
