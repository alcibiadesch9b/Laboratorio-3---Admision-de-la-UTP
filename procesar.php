<?php
// procesar.php - Backend: valida, estandariza, guarda la foto de forma segura y muestra el resultado.
include 'includes/header.php';

$errores = [];
$datos = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Saneamiento y seguridad (previene XSS y descarta etiquetas HTML/PHP sueltas)
    $nombre           = trim(strip_tags(htmlspecialchars($_POST['nombre'] ?? '')));
    $apellido         = trim(strip_tags(htmlspecialchars($_POST['apellido'] ?? '')));
    $identificacion   = trim(strip_tags(htmlspecialchars($_POST['identificacion'] ?? '')));
    $fecha_nacimiento = trim($_POST['fecha_nacimiento'] ?? '');
    $sexo             = trim(strip_tags(htmlspecialchars($_POST['sexo'] ?? '')));

    // 2. Validar que los campos obligatorios no estén vacíos
    if ($nombre === '' || $apellido === '' || $identificacion === '' || $fecha_nacimiento === '' || $sexo === '') {
        $errores[] = 'Todos los campos son obligatorios.';
    }

    // 3. Normalización: Nombre/Apellido a formato tipo título, Identificación en mayúsculas
    $nombre         = ucwords(strtolower($nombre));
    $apellido       = ucwords(strtolower($apellido));
    $identificacion = strtoupper($identificacion);

    // 4. Calcular la edad y validar que esté entre 18 y 70 años
    $edad = null;
    if ($fecha_nacimiento !== '') {
        try {
            $nacimiento = new DateTime($fecha_nacimiento);
            $hoy = new DateTime();
            $edad = $hoy->diff($nacimiento)->y;

            if ($edad < 18 || $edad > 70) {
                $errores[] = 'El aspirante debe tener entre 18 y 70 años (edad calculada: ' . $edad . ' años).';
            }
        } catch (Exception $e) {
            $errores[] = 'La fecha de nacimiento no es válida.';
        }
    }

    // 5. Validar extensión de la foto y guardarla de forma segura
    $nombreArchivoGuardado = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $nombreOriginal = $_FILES['foto']['name'];
        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

        if (!in_array($extension, $extensionesPermitidas)) {
            $errores[] = 'Formato de imagen no permitido. Usa jpg, jpeg, png, gif o webp.';
        } else {
            $carpetaDestino = __DIR__ . '/uploaded_files/';
            // Nombre único para evitar sobrescribir fotos de otros aspirantes o exponer el nombre original
            $nombreArchivoGuardado = uniqid('aspirante_') . '.' . $extension;
            $rutaDestino = $carpetaDestino . $nombreArchivoGuardado;

            if (!move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
                $errores[] = 'Hubo un error al guardar la fotografía.';
                $nombreArchivoGuardado = '';
            }
        }
    } else {
        $errores[] = 'Debes seleccionar una fotografía.';
    }

    $datos = [
        'nombre'           => $nombre,
        'apellido'         => $apellido,
        'identificacion'   => $identificacion,
        'fecha_nacimiento' => $fecha_nacimiento,
        'edad'             => $edad,
        'sexo'             => $sexo,
        'foto'             => $nombreArchivoGuardado,
    ];

} else {
    $errores[] = 'Debes enviar el formulario desde la página de registro.';
}
?>

<main class="flex-grow-1">
    <section class="py-5">
        <div class="container" style="max-width: 600px;">

            <?php if (!empty($errores)): ?>

                <div class="alert alert-danger">
                    <h5 class="alert-heading">No se pudo completar el registro</h5>
                    <ul class="mb-0">
                        <?php foreach ($errores as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <a href="index.php" class="btn btn-outline-primary">Volver al formulario</a>

            <?php else: ?>

                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">Aspirante registrado con éxito</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 text-center mb-3 mb-md-0">
                                <?php if ($datos['foto']): ?>
                                    <!-- La carpeta uploaded_files/ está bloqueada al navegador; la foto se sirve vía mostrar_foto.php -->
                                    <img src="mostrar_foto.php?f=<?php echo urlencode($datos['foto']); ?>"
                                         class="img-fluid rounded" alt="Foto del aspirante">
                                <?php endif; ?>
                            </div>
                            <div class="col-md-8">
                                <p class="mb-1"><strong>Nombre:</strong> <?php echo htmlspecialchars($datos['nombre']); ?></p>
                                <p class="mb-1"><strong>Apellido:</strong> <?php echo htmlspecialchars($datos['apellido']); ?></p>
                                <p class="mb-1"><strong>Identificación:</strong> <?php echo htmlspecialchars($datos['identificacion']); ?></p>
                                <p class="mb-1"><strong>Fecha de nacimiento:</strong> <?php echo htmlspecialchars($datos['fecha_nacimiento']); ?></p>
                                <p class="mb-1"><strong>Edad:</strong> <?php echo htmlspecialchars((string) $datos['edad']); ?> años</p>
                                <p class="mb-0"><strong>Sexo:</strong> <?php echo htmlspecialchars($datos['sexo']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="index.php" class="btn btn-outline-primary mt-3">Registrar otro aspirante</a>

            <?php endif; ?>

        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
