<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

<h1>Práctica 13 (P13) — Persistencia en SGBD de la red social interna (BC6)</h1>

<p>
  Esta práctica consiste en extender la P11 (migración a framework) para que la aplicación guarde y lea los datos desde un
  <strong>SGBD relacional</strong>, sustituyendo los ficheros JSON por <strong>tablas en base de datos</strong>.
  La funcionalidad de la app se mantiene (login, roles, mensajes, moderación, tema), pero cambia la capa de persistencia.
</p>

<ol>

  <li>
    <h2>Objetivo</h2>
    <p>
      El objetivo principal es adaptar la aplicación de la P11 para usar un <strong>SGBD</strong>, manteniendo la arquitectura
      basada en <strong>POO</strong> y el patrón <strong>Repository + interfaces</strong>.
    </p>
    <ul>
      <li>Diseñar el esquema relacional mínimo a partir de las clases/casos de uso actuales.</li>
      <li>Crear la base de datos y tablas con sentencias SQL (DDL).</li>
      <li>Implementar el CRUD y acciones de moderación usando la base de datos.</li>
      <li>Conservar la arquitectura de P11 (controladores + repositorios).</li>
    </ul>
  </li>

  <li>
    <h2>Funcionalidades</h2>
    <p>
      La aplicación parte de la red social interna mínima (P8) y la migración a Laravel (P11). En esta P13
      <strong>no cambia la funcionalidad</strong>, cambia el almacenamiento:
      antes JSON, ahora base de datos.
    </p>
    <ul>
      <li><strong>Registro y login</strong> con roles (<em>alumno</em> / <em>profesor</em>).</li>
      <li><strong>Publicación de mensajes</strong> asociados a una asignatura (<code>subject</code>).</li>
      <li><strong>Moderación</strong> de mensajes: <code>pending</code>, <code>published</code>, <code>rejected</code>, <code>deleted</code>.</li>
      <li><strong>Preferencia de tema</strong> claro/oscuro (cookie y persistencia en usuario).</li>
      <li><strong>Listados</strong> en portada con los mensajes publicados.</li>
      <li><strong>Borrado lógico</strong> de mensajes: <code>isDeleted</code> + <code>deletedAt</code>.</li>
    </ul>
  </li>

  <li>
    <h2>SGBD elegido</h2>
    <h3>SGBD y versión</h3>
    <p>
      Se ha utilizado <strong>MariaDB 10.4.28</strong> (incluida en el entorno XAMPP para Linux).
    </p>
    <h3>Motivos de la elección</h3>
    <ul>
      <li><strong>Instalación sencilla</strong> en el entorno de desarrollo (XAMPP en Linux).</li>
      <li><strong>Compatibilidad</strong> con hosting típico (MySQL/MariaDB).</li>
      <li><strong>Integración directa</strong> con Laravel usando el driver <code>mysql</code> y el facade <code>DB</code>.</li>
      <li><strong>Herramientas</strong> disponibles para revisar datos (terminal mysql, phpMyAdmin, etc.).</li>
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
    <h3>Diagrama (E/R y tablas)</h3>
    <p>
      El diagrama actualizado (con claves primarias y foráneas).
    </p>
  </li>

  <li>
    <h2>Scripts SQL (creación e inserción de datos)</h2>
    <h3>5.1 Script de creación (DDL)</h3>
    <p>
      La estructura de la base de datos se crea con el script:
      <code>database/schema.sql</code>
    </p>
    <p>
      Este script:
    </p>
    <ul>
      <li>Crea la base de datos <code>redsocial</code> (si no existe).</li>
      <li>Crea las tablas <code>users</code> y <code>messages</code> con sus claves y restricciones.</li>
      <li>Define la clave foránea <code>messages.idUser</code> → <code>users.idUser</code>.</li>
    </ul>
    <h3>5.2 Script de datos iniciales (seed)</h3>
    <p>
      Los usuarios de prueba se insertan con:
      <code>database/seed.sql</code>
    </p>
    <p>
      Incluye:
    </p>
    <ul>
      <li>1 usuario profesor</li>
      <li>1 usuario alumno</li>
    </ul>
  </li>

  <li>
    <h2>Puesta en marcha del proyecto</h2>
    <h3>6.1 Requisitos</h3>
    <ul>
      <li>Entorno con PHP (en mi caso: XAMPP para Linux).</li>
      <li>Composer.</li>
      <li>MariaDB en ejecución.</li>
    </ul>
    <h3>6.2 Configuración del archivo .env</h3>
    <p>
      Copiar <code>.env.example</code> a <code>.env</code> y configurar los parámetros de conexión.
      En este caso:
    </p>
    <pre><code>DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=redsocial
DB_USERNAME=redSocialUsuario
DB_PASSWORD=</code></pre>
    <p>
      Nota: la aplicación utiliza <strong>Laravel + DB facade</strong> y la conexión depende de estos valores.
    </p>
    <h3>6.3 Crear (o reiniciar) la base de datos</h3>
    <p>
      Para crear la estructura desde el script:
    </p
    <pre><code>mysql -h 127.0.0.1 -P 3306 -u TU_USUARIO -p &lt; database/schema.sql</code></pre>
    <p>
      Para insertar datos iniciales:
    </p>
    <pre><code>mysql -h 127.0.0.1 -P 3306 -u TU_USUARIO -p redsocial &lt; database/seed.sql</code></pre>
    <p>
      Si se quiere reiniciar completamente (borrar y recrear), se puede:
    </p>
    <pre><code>mysql -h 127.0.0.1 -P 3306 -u root -p -e "DROP DATABASE IF EXISTS redsocial;"
mysql -h 127.0.0.1 -P 3306 -u root -p &lt; database/schema.sql
mysql -h 127.0.0.1 -P 3306 -u root -p redsocial &lt; database/seed.sql</code></pre>
    <p>
      (Se usa <code>root</code> solo para crear/borrar la base de datos si el usuario normal no tiene permisos. Para la app se usa un usuario normal).
    </p>
    <h3>6.4 Instalar dependencias y arrancar Laravel</h3>
    <pre><code>composer install
php artisan key:generate
php artisan config:clear
php artisan cache:clear
php artisan serve</code></pre>
    <p>
      Acceso por defecto: <code>http://127.0.0.1:8000</code>
    </p>
  </li>

  <li>
    <h2>Arquitectura y persistencia (Repository + interfaces)</h2>
    <h3>7.1 Persistencia en SGBD manteniendo interfaces</h3>
    <p>
      Para mantener la arquitectura de la práctica anterior, se conservan las interfaces:
    </p>
    <ul>
      <li><code>App\Contracts\IUserRepository</code></li>
      <li><code>App\Contracts\IMessageRepository</code></li>
    </ul>
    <p>
      Los controladores trabajan contra las interfaces, y la implementación concreta es la versión DB:
    </p>
    <ul>
      <li><code>App\Repositories\Db\UserRepositoryDb</code></li>
      <li><code>App\Repositories\Db\MessageRepositoryDb</code></li>
    </ul>
    <h3>7.2 Operaciones implementadas</h3>
    <p>
      Acciones principales soportadas con SQL a través de <code>DB::table()</code>:
    </p>
    <ul>
      <li>
        <strong>Usuarios</strong>:
        <code>findByEmail()</code>, <code>save()</code>, <code>update()</code>.
      </li>
      <li>
        <strong>Mensajes</strong>:
        <code>find()</code>, <code>save()</code>, <code>update()</code>, <code>getPublished()</code>, <code>getPending()</code>, <code>getRejected()</code>,
        <code>approve()</code>, <code>reject()</code>, <code>delete()</code>.
      </li>
    </ul>
    <h3>7.3 Manejo básico de errores</h3>
    <p>
      En los repositorios DB se controla el error básico con <code>try/catch</code> usando
      <code>Illuminate\Database\QueryException</code>, devolviendo arrays vacíos o <code>null</code> cuando falla una consulta,
      para evitar mostrar información sensible de la BD.
    </p>
  </li>

  <li>
    <h2>Usuarios de prueba</h2>
    <p>
      El script <code>database/seed.sql</code> inserta usuarios de demostración:
    </p>
    <ul>
      <li>
        <strong>Profesor</strong><br>
        Email: <code>profe@gmail.com</code><br>
        Rol: <code>profesor</code><br>
        Contraseña: <code>profe123</code>
      </li>
      <li>
        <strong>Alumno</strong><br>
        Email: <code>alumno@gmail.com</code><br>
        Rol: <code>alumno</code><br>
        Contraseña: <code>alumno123</code>
      </li>
    </ul>
    <p>
      Nota: en la base de datos se guarda <code>password_hash</code> (bcrypt). En la aplicación se valida con <code>password_verify()</code>.
    </p>
  </li>
</ol>
<footer>Lucía Soto y Andrea Pollán</footer>
