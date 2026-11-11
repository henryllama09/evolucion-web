<?php
require_once __DIR__ . '/config.php';

$mensajes = $pdo->query('SELECT * FROM contactos ORDER BY fecha_registro DESC')->fetchAll();
$visitas  = $pdo->query('SELECT COALESCE(SUM(contador),0) AS total,
                                COUNT(DISTINCT fecha) AS dias
                         FROM visitas')->fetch();

$totalMsg  = count($mensajes);
$porPais   = [];
foreach ($mensajes as $m) {
    $porPais[$m['pais']] = ($porPais[$m['pais']] ?? 0) + 1;
}
arsort($porPais);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mensajes recibidos | Evolución de la Web</title>
  <link rel="stylesheet" href="estilos.css">
</head>
<body>
  <header class="cabecera">
    <div class="barra">
      <a class="logo" href="index.html"><span aria-hidden="true">🌐</span> Evolución de la Web</a>
      <input type="checkbox" id="menu-toggle" class="menu-toggle">
      <label for="menu-toggle" class="menu-icono" aria-label="Abrir menú">☰</label>
      <nav class="nav">
        <ul class="menu">
          <li><a href="index.html">Inicio</a></li>
          <li><a href="acerca.html">Acerca de</a></li>
          <li><a href="linea-tiempo.html">Línea del tiempo</a></li>
          <li><a href="recursos.html">Recursos</a></li>
          <li><a href="contacto.html">Contacto</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main class="contenedor">
    <h1>Mensajes guardados en la base de datos</h1>
    <p>Esta página se genera <em>dinámicamente</em> desde el servidor con PHP leyendo la tabla
       <code>contactos</code> de MySQL.</p>

    <div class="rejilla" style="margin-bottom:1.5rem;">
      <article class="tarjeta"><h3><?= $totalMsg ?></h3><p>Mensajes registrados</p></article>
      <article class="tarjeta"><h3><?= (int)$visitas['total'] ?></h3><p>Visitas totales</p></article>
      <article class="tarjeta"><h3><?= (int)$visitas['dias'] ?></h3><p>Días con actividad</p></article>
      <article class="tarjeta">
        <h3><?= $porPais ? array_key_first($porPais) : '—' ?></h3>
        <p>País más frecuente</p>
      </article>
    </div>

    <div class="tabla-envoltura">
      <table>
        <caption>Registros de la tabla <code>contactos</code></caption>
        <thead>
          <tr>
            <th>ID</th><th>Nombre</th><th>Correo</th><th>Teléfono</th>
            <th>Edad</th><th>País</th><th>Género</th><th>Comentarios</th><th>Fecha</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($mensajes)): ?>
            <tr><td colspan="9">Aún no hay mensajes registrados.</td></tr>
          <?php else: foreach ($mensajes as $m): ?>
            <tr>
              <td><?= (int)$m['id'] ?></td>
              <td><?= htmlspecialchars($m['nombre']) ?></td>
              <td><?= htmlspecialchars($m['correo']) ?></td>
              <td><?= htmlspecialchars($m['telefono']) ?></td>
              <td><?= (int)$m['edad'] ?></td>
              <td><?= htmlspecialchars($m['pais']) ?></td>
              <td><?= htmlspecialchars($m['genero']) ?></td>
              <td><?= htmlspecialchars($m['comentarios'] ?? '—') ?></td>
              <td><?= htmlspecialchars($m['fecha_registro']) ?></td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
        <tfoot>
          <tr><td colspan="9">Total: <?= $totalMsg ?> registro(s)</td></tr>
        </tfoot>
      </table>
    </div>

    <p><a class="boton" href="contacto.html">← Volver al formulario</a></p>
  </main>

  <footer class="pie">
    <div class="contenedor">
      <div class="creditos"><p>ADA 4 · Henry Javier Llamá Dzib · UADY</p></div>
    </div>
  </footer>
</body>
</html>