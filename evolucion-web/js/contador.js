/* ============================================
    Contador de visitas del servidor
   Consulta contador.php (PHP + MySQL) y muestra
   el total y las visitas del día en el footer.
   ============================================ */
document.addEventListener('DOMContentLoaded', function () {
  const el = document.getElementById('contador-visitas');
  if (!el) return;

  const yaContado = sessionStorage.getItem('visita_contada');
  const url = 'contador.php' + (yaContado ? '?solo_leer=1' : '');

  fetch(url)
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (data.error) {
        el.textContent = '👁 Contador no disponible';
        return;
      }
      el.textContent = '👁 ' + data.total + ' visitas totales · ' + data.hoy + ' hoy';
      sessionStorage.setItem('visita_contada', '1');
    })
    .catch(function () {
      el.textContent = '👁 Contador no disponible';
    });
});