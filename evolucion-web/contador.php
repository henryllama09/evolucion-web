<?php
// ============================================
// contador.php
// Devuelve JSON con el total de visitas.
// Incrementa una vez por sesión (controlado desde JS).
// ============================================
require_once __DIR__ . '/config.php';
header('Content-Type: application/json; charset=utf-8');

$soloLeer = isset($_GET['solo_leer']);

try {
    if (!$soloLeer) {
        $hoy  = date('Y-m-d');
        $stmt = $pdo->prepare(
            'INSERT INTO visitas (fecha, contador) VALUES (:fecha, 1)
             ON DUPLICATE KEY UPDATE contador = contador + 1'
        );
        $stmt->execute([':fecha' => $hoy]);
    }

    $total   = (int) ($pdo->query('SELECT COALESCE(SUM(contador),0) FROM visitas')->fetchColumn() ?: 0);
    $hoyStmt = $pdo->prepare('SELECT contador FROM visitas WHERE fecha = :fecha');
    $hoyStmt->execute([':fecha' => date('Y-m-d')]);
    $hoy     = (int) ($hoyStmt->fetchColumn() ?: 0);

    echo json_encode(['total' => $total, 'hoy' => $hoy, 'error' => null]);
} catch (PDOException $e) {
    echo json_encode(['total' => 0, 'hoy' => 0, 'error' => $e->getMessage()]);
}