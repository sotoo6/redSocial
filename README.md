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
    <li>Instrucciones de instalación y arranque en local</li>
    <li>Listado de rutas y roles requeridos</li>
    <li>Validación y sanitización implementada</li>
    <li>Usuarios de prueba (al menos 1 alumno y 1 profesor)</li>
</ol>
