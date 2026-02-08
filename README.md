<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

<h1>Práctica 13: Persistencia en SGBD de la red social interna</h1>

<li>
  <h2>SGBD elegido y configuración (BC6)</h2>

  <h3>SGBD elegido y versión</h3>
  <p>
    Para la persistencia en SGBD se ha elegido <strong>MariaDB</strong> (compatible con MySQL).
    La versión utilizada en el entorno de desarrollo es <strong>MariaDB 10.4.28</strong> (incluida en XAMPP para Linux).
  </p>

  <h3>Motivos de la elección</h3>
  <ul>
    <li>
      <strong>Instalación y puesta en marcha sencilla:</strong> al trabajar con XAMPP en Linux, MariaDB ya viene incluida y se
      puede iniciar/parar el servicio rápidamente.
    </li>
    <li>
      <strong>Compatibilidad con Laravel y PHP:</strong> soporte directo mediante el Query Builder/DB de Laravel y configuración estándar
      por variables de entorno en <code>.env</code>.
    </li>
    <li>
      <strong>Compatibilidad con hosting:</strong> MySQL/MariaDB es una de las opciones más habituales en servidores y hosting,
      facilitando un posible despliegue.
    </li>
    <li>
      <strong>Herramientas disponibles:</strong> administración tanto por terminal (<code>mysql</code>) como mediante herramientas gráficas
      como <strong>phpMyAdmin</strong> (incluida en XAMPP).
    </li>
  </ul>

  <h3>Instrucciones de creación de la base de datos</h3>
  <p>
    La base de datos se crea mediante un script SQL incluido en el proyecto:
    <code>database/schema.sql</code>.
  </p>

  <h4>1) Acceder a MariaDB</h4>
  <pre><code>/opt/lampp/bin/mysql -h 127.0.0.1 -P 3306 -u root</code></pre>

  <h4>2) Ejecutar el script del esquema</h4>
  <pre><code>SOURCE database/schema.sql;</code></pre>

  <h4>3) Crear usuario de la aplicación (recomendado, sin usar root)</h4>
  <pre><code>CREATE USER IF NOT EXISTS 'redsocial_user'@'127.0.0.1' IDENTIFIED BY 'maquinas1';
CREATE USER IF NOT EXISTS 'redsocial_user'@'localhost'  IDENTIFIED BY 'maquinas1';

GRANT ALL PRIVILEGES ON redsocial.* TO 'redsocial_user'@'127.0.0.1';
GRANT ALL PRIVILEGES ON redsocial.* TO 'redsocial_user'@'localhost';

FLUSH PRIVILEGES;</code></pre>

  <h4>4) Configurar Laravel (.env)</h4>
  <pre><code>DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=redsocial
DB_USERNAME=redsocial_user
DB_PASSWORD=maquinas1</code></pre>

  <h4>5) Limpiar caché de configuración</h4>
  <pre><code>/opt/lampp/bin/php artisan config:clear
/opt/lampp/bin/php artisan cache:clear</code></pre>
</li>

<footer>Lucía Soto y Andrea Pollán</footer>
