<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

<h1>Red Social Interna - Laravel</h1>
<h2>P11 - Migración a Framework de red social + P13 Persistencia en SGBD de la red social interna</h2>

<p>Esta aplicación es una Red Social Interna desarrollada con el framework Laravel, diseñada específicamente para un entorno educativo. Su objetivo principal es facilitar la comunicación entre alumnos y profesores mediante un sistema de mensajería estructurado y supervisado.</p>
<p>La plataforma permite a los usuarios compartir ideas y consultas asociadas a asignaturas específicas, implementando un flujo de moderación estricto: los mensajes enviados por los usuarios entran en un estado de revisión y solo son visibles para la comunidad una vez que un usuario con rol de profesor los aprueba.</p>

<ol>
  <li>
    <h2>Justificación de la elección del framework</h2>
    <p>
      Se ha elegido Laravel como framework para el desarrollo de esta red social debido a que proporciona una arquitectura clara basada en el patrón MVC, facilitando la organización del código. Además Laravel incluye de forma nativa herramientas para la gestión de rutas, controladores, modelos, validación de datos, autenticación.
    </p>
  </li>

  <li>
      <h2>Justificación de la elección del SGBD</h2>
      <p>Para la persistencia de datos se ha utilizado MariaDB 10.4.28 (XAMPP), elegida por su sencilla configuración en entornos locales y su compatibilidad con la mayoría de servicios de hosting.</p>
      <p>Su integración con Laravel es inmediata mediante el driver MySQL y el facade DB, permitiendo además una gestión cómoda de la base de datos a través de herramientas estándar como phpMyAdmin o la terminal de comandos.</p>
  </li>

  <li>
    <h2>Patrones de diseño aplicados</h2>
    <h3>Patrón Repository</h3>
    <p>
      En la P11 la persistencia de la información se realizaba mediante archivos JSON (repositorios
      <code>UserRepositoryJson</code> y <code>MessageRepositoryJson</code>). En la P13 se mantiene la misma arquitectura
      (Repository + interfaces), pero sustituyendo los repositorios JSON por repositorios contra SGBD
      (<code>UserRepositoryDb</code> y <code>MessageRepositoryDb</code>) usando MariaDB y consultas con <code>DB::table()</code>.
      De este modo, los controladores siguen trabajando contra interfaces y la lógica de negocio no cambia.
    </p>
    <h3>Patrón Modelo Vista Controlador (MVC)</h3>
    <p>El proyecto sigue el patrón de arquitectura MVC para garantizar una separación de responsabilidades clara, facilitando el mantenimiento y la escalabilidad del código.</p>
    <ul>
        <li><h4>Modelo</h4>
            <p>Ubicados en <code>app/Models/</code>, estos archivos representan la estructura de los datos.</p>
            <ul>
              <li><b>P11:</b> persistencia en JSON (<code>storage/app/data/</code>).</li>
              <li><b>P13:</b> persistencia en SGBD (MariaDB) mediante repositorios DB (<code>app/Repositories/Db</code>).</li>
            </ul>
        </li>
        <li><h4>Vista</h4>
            <p>Ubicadas en <code>resources/views/</code>, definen la interfaz que el usuario final visualiza.</p>
            <ul>
                <li>Motor Blade: Utilizamos plantillas Blade para inyectar los datos procesados.</li>
                <li>Modularidad: El archivo <code>layout.blade.php</code> en la carpeta <code>partials</code> centraliza la estructura HTML, mientras que carpetas como <code>/auth</code> y <code>/messages</code> contienen las interfaces específicas para el flujo de la red social.</li>
            </ul>
        </li>
        <li><h4>Controlador</h4>
            <p>Ubicados en <code>app/Http/Controllers/</code>, funcionan como el cerebro de la aplicación.</p>
            <ul>
              <li><b>Gestión de solicitudes:</b> reciben las peticiones del usuario (vía <code>routes/web.php</code>).</li>
              <li><b>Persistencia en SGBD:</b> el controlador trabaja contra los repositorios (interfaces) para consultar/guardar datos en la base de datos (tablas <code>users</code> y <code>messages</code>) y, posteriormente, carga la vista pertinente con esos datos.</li>
              <li><b>Controladores clave:</b> <code>AuthController.php</code> para sesiones y autenticación, <code>MessageController.php</code> para publicación, edición, borrado lógico y moderación, y <code>ThemeController.php</code> para la preferencia de tema.</li>
            </ul>
        </li>
    </ul>
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
        <ul>
            <li>PHP + Composer</li>
            <li>Servidor de base de datos MySQL/MariaDB en ejecución</li>
            <li>Cliente <code>mysql</code> (terminal) o phpMyAdmin para ejecutar scripts</li>
        </ul>
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
        <h4>2) Instalación de dependencias</h4>
<p>
Dado que el repositorio no incluye las librerías del núcleo de Laravel ni paquetes de terceros, es necesario instalarlos manualmente. El siguiente comando leerá el archivo <code>composer.json</code> y descargará todas las dependencias requeridas en la carpeta <code>vendor</code>:
</p>
<pre><code>composer install</code></pre>

<h4>3) Configuración del entorno (.env)</h4>
<p>
El proyecto incluye el archivo <code>.env</code> en el directorio raíz. Si por alguna razón no existiera, puedes generarlo copiando el archivo de ejemplo:
</p>
<pre><code>cp .env.example .env</code></pre>

<p>
En esta práctica (P13) la persistencia se realiza en un SGBD. Por tanto, debes configurar los parámetros de conexión a la base de datos en el <code>.env</code>:
</p>
<pre><code>DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=redsocial
DB_USERNAME=redSocialUsuario
DB_PASSWORD=</code></pre>

<h4>4) Crear la base de datos y las tablas (schema.sql)</h4>
<p>
  La estructura de la base de datos (incluye <code>CREATE DATABASE</code> + <code>CREATE TABLE</code>) se crea con el script
  <code>database/schema.sql</code>.
</p>

<p><b>Opción A (recomendada):</b> ejecutar el schema con un usuario con permisos (por ejemplo <code>root</code>)</p>
<pre><code>mysql -h 127.0.0.1 -P 3306 -u root -p &lt; database/schema.sql</code></pre>

<p>
  <b>Nota:</b> Se suele usar <code>root</code> para crear la base de datos y tablas si el usuario normal no tiene permisos de
  <code>CREATE/DROP</code>. La aplicación luego se conecta con <code>DB_USERNAME=redSocialUsuario</code>.
</p>

<p><b>Opción B:</b> ejecutar el schema con tu usuario (si tiene permisos)</p>
<pre><code>mysql -h 127.0.0.1 -P 3306 -u redSocialUsuario -p &lt; database/schema.sql</code></pre>

<h4>5) Insertar datos de prueba (seed.sql)</h4>
<p>
  Para insertar usuarios de prueba (profesor y alumno) y opcionalmente mensajes de ejemplo se utiliza el script
  <code>database/seed.sql</code>:
</p>

<pre><code>mysql -h 127.0.0.1 -P 3306 -u redSocialUsuario -p redsocial &lt; database/seed.sql</code></pre>

<p>
  Si tu usuario no tiene permisos de inserción, ejecuta el seed con <code>root</code>:
</p>
<pre><code>mysql -h 127.0.0.1 -P 3306 -u root -p redsocial &lt; database/seed.sql</code></pre>

<h4>6) (Opcional) Reiniciar completamente la base de datos</h4>
<p>
  Si necesitas borrar y recrear todo desde cero:
</p>

<pre><code>mysql -h 127.0.0.1 -P 3306 -u root -p -e "DROP DATABASE IF EXISTS redsocial;"
mysql -h 127.0.0.1 -P 3306 -u root -p &lt; database/schema.sql
mysql -h 127.0.0.1 -P 3306 -u root -p redsocial &lt; database/seed.sql</code></pre>

<h4>7) Generación de la clave de aplicación</h4>
<p>
  Aunque dispongas del archivo de entorno, Laravel requiere asegurar que exista una clave de encriptación válida configurada
  para gestionar la seguridad de las sesiones. Puedes generar o regenerar esta clave ejecutando:
</p>

<pre><code>/opt/lampp/bin/php artisan key:generate</code></pre>

<h4>8) Limpiar configuración (recomendado tras cambios en .env)</h4>
<p>
  Si has modificado el <code>.env</code>, es recomendable limpiar la configuración/cache:
</p>

<pre><code>/opt/lampp/bin/php artisan config:clear
/opt/lampp/bin/php artisan cache:clear</code></pre>

<h4>9) Permisos de escritura</h4>
<p>
  Laravel necesita permisos de escritura en <code>storage</code> y <code>bootstrap/cache</code> para cache, logs y archivos temporales:
</p>

<pre><code>chmod -R 775 storage bootstrap/cache</code></pre>

<h4>10) Ejecución del servidor</h4>
<p>
  Para iniciar el entorno de desarrollo, se utiliza el servidor interno proporcionado por Artisan (invocando el PHP de XAMPP).
  Se recomienda especificar el host y el puerto para asegurar la accesibilidad externa:
</p>

<pre><code>/opt/lampp/bin/php artisan serve --host=0.0.0.0 --port=8000</code></pre>

<p>
  Una vez ejecutado el comando, la aplicación estará accesible dependiendo de tu método de conexión:
</p>
<ul>
  <li>Si usas VS Code (Remote-SSH) o estás dentro de la VM: Accede a <code>http://localhost:8000</code> (el editor reenviará el puerto automáticamente).</li>
  <li>Si accedes desde Windows/Mac sin reenvío de puertos: Deberás usar la IP de la máquina virtual: <code>http://&lt;IP_DE_TU_VM&gt;:8000</code> (puedes consultar tu IP ejecutando <code>hostname -I</code> en la terminal).</li>
</ul>
  </li>

  <li>
      <h2>Diseño de la base de datos</h2>
    <h3>Entidades y relaciones</h3>
    <p>
      El modelo se centra en dos entidades principales:
    </p>
    <ul>
      <li>
        <strong>users</strong>: almacena usuarios, con rol (<em>alumno/profesor</em>) y preferencia de tema.
      </li>
      <li>
        <strong>messages</strong>: almacena mensajes publicados por un usuario. Cada mensaje pertenece a un usuario
        mediante una <strong>FK</strong> (<code>messages.idUser</code> → <code>users.idUser</code>).
      </li>
    </ul>
    <p>
      Relación principal: <strong>users (1) — (N) messages</strong>.
      Un usuario puede publicar muchos mensajes y cada mensaje pertenece a un único usuario.
    </p>
    <h3>Borrado lógico (especialización)</h3>
    <p>
      Para los mensajes eliminados se usa <strong>borrado lógico</strong> en la tabla <code>messages</code>:
    </p>
    <ul>
      <li><code>isDeleted</code> indica si el mensaje está eliminado (0/1).</li>
      <li><code>deletedAt</code> guarda la fecha/hora en la que se eliminó.</li>
    </ul>
    <h3>Resumen de tablas creadas (columnas principales)</h3>
    <ul>
      <li>
        <b>users</b>:
        <code>idUser</code> (PK),
        <code>name</code>,
        <code>email</code> (UNIQUE),
        <code>password_hash</code>,
        <code>role</code>,
        <code>theme</code>,
        <code>createdAt</code>
      </li>
      <li>
        <b>messages</b>:
        <code>idMessage</code> (PK),
        <code>idUser</code> (FK → <code>users.idUser</code>),
        <code>subject</code>,
        <code>content</code>,
        <code>status</code>,
        <code>createdAt</code>,
        <code>isDeleted</code>,
        <code>deletedAt</code>
      </li>
    </ul>
    <h3>Diagrama (E/R y tablas)</h3>
    <p>
      El diagrama actualizado se encuentra en el proyecto en la carpeta <code>documentacion/P13ERredSocial.jpg</code>.
    </p>
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
              <li>Busca el usuario en la tabla <code>users</code> de la base de datos.</li>
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
              <li>Inserta el nuevo usuario en la tabla <code>users</code> (campo <code>password_hash</code>) con un ID aleatorio y tema por defecto.</li>
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
              <li>Si es válido, inserta el mensaje en la tabla <code>messages</code> con estado <code>pending</code> o <code>rejected</code>.</li>
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
            <p>Actualiza el estado del mensaje seleccionado a published. Actualiza el campo <code>status</code> del registro en la tabla <code>messages</code> y redirige de vuelta a la vista de moderación.</p>
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
              <li>Actualiza el campo <code>theme</code> del usuario en la tabla <code>users</code> para mantener persistencia tras cerrar sesión.</li>
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
          <li>Solo usuarios con <code>role = 'profesor'</code> pueden acceder a la cola de moderación.</li>
          <li>Los mensajes se pueden aprobar (estado <code>published</code>) o rechazar (estado <code>rejected</code>).</li>
          <li>Los cambios se guardan de manera persistente en la base de datos, actualizando el campo <code>status</code> en la tabla <code>messages</code>.</li>
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
          <li>Rotación de ID de sesión:</b> tras un login correcto, se genera un nuevo ID para evitar secuestro de sesión.</li>
          <li>Cookie de tema (claro/oscuro):</b> se guarda durante 30 días, se actualiza cada vez que el usuario cambia el tema y se sincroniza con la sesión y con la base de datos (campo <code>theme</code> en la tabla <code>users</code>).</li>
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
