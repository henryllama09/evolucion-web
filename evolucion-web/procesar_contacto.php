<?php
// ============================================
// procesar_contacto.php
// Recibe el POST del formulario, valida en servidor
// y guarda en la tabla `contactos`.
// ============================================
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contacto.html');
    exit;
}

// --- Sanitizar / recoger datos ---
$nombre      = trim($_POST['nombre']      ?? '');
$correo      = trim($_POST['correo']      ?? '');
$telefono    = trim($_POST['telefono']    ?? '');
$edad        = (int) ($_POST['edad']      ?? 0);
$pais        = trim($_POST['pais']        ?? '');
$genero      = trim($_POST['genero']      ?? '');
$comentarios = trim($_POST['comentarios'] ?? '');

// --- Validación del lado del servidor ---
$errores = [];
if (mb_strlen($nombre) < 3)                                  $errores[] = 'nombre';
if (!filter_var($correo, FILTER_VALIDATE_EMAIL))             $errores[] = 'correo';
if (!preg_match('/^[0-9+\-\s()]{7,20}$/', $telefono))        $errores[] = 'telefono';
if ($edad < 18 || $edad > 99)                                $errores[] = 'edad';
if ($pais === '')                                            $errores[] = 'pais';
if (!in_array($genero, ['masculino','femenino','otro','prefiero-no'], true))
                                                              $errores[] = 'genero';
if (!isset($_POST['terminos']))                              $errores[] = 'terminos';
if (mb_strlen($comentarios) > 200)                           $errores[] = 'comentarios';

if (!empty($errores)) {
    header('Location: contacto.html?error=' . urlencode(implode(',', $errores)));
    exit;
}

// --- Guardar en la base de datos (consulta preparada) ---
try {
    $stmt = $pdo->prepare(
        'INSERT INTO contactos (nombre, correo, telefono, edad, pais, genero, comentarios)
         VALUES (:nombre, :correo, :telefono, :edad, :pais, :genero, :comentarios)'
    );
    $stmt->execute([
        ':nombre'      => $nombre,
        ':correo'      => $correo,
        ':telefono'    => $telefono,
        ':edad'        => $edad,
        ':pais'        => $pais,
        ':genero'      => $genero,
        ':comentarios' => $comentarios !== '' ? $comentarios : null,
    ]);
    header('Location: contacto.html?exito=1');
    exit;
} catch (PDOException $e) {
    header('Location: contacto.html?error=db');
    exit;
}