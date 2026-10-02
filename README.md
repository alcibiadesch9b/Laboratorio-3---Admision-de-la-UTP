# Taller: Registro de Aspirantes

Laboratorio #3 — Desarrollo Web (Módulo II: Diseño Web con HTML5 y CSS3 / Módulo III: Programación de Aplicaciones Web). Universidad Tecnológica de Panamá.

- **Profesora:** Ing. Irina Fong
- **Fecha de asignación:** 18 de septiembre de 2026
- **Fecha de entrega:** 02 de octubre de 2026

## Descripción

Sistema web que permite registrar aspirantes mediante un formulario (nombre, apellido, identificación, fecha de nacimiento, sexo y fotografía). El backend en PHP valida, sanitiza y estandariza los datos, calcula la edad del aspirante y guarda la fotografía de forma segura, sin usar base de datos.

## Estructura de carpetas

```
Taller-Aspirantes/
├── includes/
│   ├── header.php        (Metadatos, navbar y breadcrumb dinámico)
│   └── footer.php        (Pie de página con año dinámico)
├── uploaded_files/
│   ├── .htaccess          (Bloquea el acceso directo desde el navegador)
│   └── .gitkeep
├── index.php              (Formulario de registro)
├── procesar.php           (Backend: valida, procesa y muestra el resultado)
├── mostrar_foto.php       (Sirve las fotos de forma controlada)
└── README.md
```

## Tecnologías usadas

- HTML5 y PHP
- Bootstrap 5.3.8 (vía CDN jsdelivr)
- Elementos semánticos: `<header>`, `<main>`, `<section>`, `<footer>`

## Funcionalidades implementadas

- Metadatos completos en `<head>` (charset, viewport, description, author, robots, theme-color).
- Navbar y breadcrumb dinámico modularizados con `include` (`includes/header.php`), usando `basename($_SERVER['PHP_SELF'])` para detectar la página actual.
- Formulario (`index.php`) con campos requeridos, tipo `date` para la fecha de nacimiento y `placeholder` en los campos de texto. Envío por `POST` con `enctype="multipart/form-data"`.
- Backend (`procesar.php`) que:
  - Sanea los datos con `htmlspecialchars()` y `strip_tags()` para prevenir XSS.
  - Limpia espacios con `trim()`.
  - Normaliza nombre y apellido a formato tipo título con `ucwords(strtolower())`.
  - Convierte la identificación a mayúsculas con `strtoupper()`.
  - Calcula la edad a partir de la fecha de nacimiento y valida que esté entre 18 y 70 años.
  - Valida la extensión de la imagen (jpg, jpeg, png, gif, webp) antes de guardarla.
  - Guarda la foto con un nombre único (`uniqid()`) en `uploaded_files/`.
- Seguridad de la carpeta de fotos: `uploaded_files/` tiene un `.htaccess` con `Require all denied`, por lo que no es accesible directamente desde el navegador. Las fotos se muestran únicamente a través de `mostrar_foto.php`, que valida el nombre de archivo antes de servirlo.
- Pie de página (`includes/footer.php`) con eslogan institucional, enlaces rápidos (Soporte Técnico, GitHub Institucional, Contacto) y copyright con año dinámico (`echo date('Y')`).

## Cómo ejecutarlo

1. Copia la carpeta `Taller-Aspirantes/` dentro de tu servidor local (ej. `C:\wamp64\www\`).
2. Inicia WAMP/XAMPP.
3. Abre `http://localhost/Taller-Aspirantes/index.php` en el navegador.
4. Llena el formulario y presiona **Registrar Aspirante**.
