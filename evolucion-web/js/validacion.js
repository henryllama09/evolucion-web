document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('formulario-contacto');
  if (!form) return;

  const mensajeExito = document.getElementById('mensaje-exito');

  /* ---------- Helper: marca un campo como válido o inválido ---------- */
  function setEstado(campoId, valido, mensaje) {
    const campo = document.getElementById(campoId);
    if (!campo) return valido;

    const error = campo.querySelector('.error');
    campo.classList.remove('invalido', 'valido');
    campo.classList.add(valido ? 'valido' : 'invalido');

    if (error) error.textContent = valido ? '' : mensaje;
    return valido;
  }

  /* ---------- Funciones de validación ---------- */
  function validarNombre() {
    const el = document.getElementById('nombre');
    const ok = el.value.trim().length >= 3;
    return setEstado('campo-nombre', ok, 'Ingresa al menos 3 caracteres.');
  }

  function validarCorreo() {
    const el = document.getElementById('correo');
    const ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(el.value.trim());
    return setEstado('campo-correo', ok, 'Ingresa un correo válido.');
  }

  function validarTelefono() {
    const el = document.getElementById('telefono');
    const ok = /^[0-9+\-\s()]{7,20}$/.test(el.value.trim());
    return setEstado('campo-telefono', ok, 'Ingresa un teléfono válido (7-20 caracteres).');
  }

  function validarEdad() {
    const el = document.getElementById('edad');
    const n = parseInt(el.value, 10);
    const ok = !isNaN(n) && n >= 18 && n <= 99;
    return setEstado('campo-edad', ok, 'Ingresa una edad entre 18 y 99.');
  }

  function validarPais() {
    const el = document.getElementById('pais');
    const ok = el.value !== '';
    return setEstado('campo-pais', ok, 'Selecciona un país.');
  }

  function validarGenero() {
    const ok = !!document.querySelector('input[name="genero"]:checked');
    return setEstado('campo-genero', ok, 'Selecciona una opción.');
  }

  function validarTerminos() {
    const ok = document.getElementById('terminos').checked;
    return setEstado('campo-terminos', ok, 'Debes aceptar los términos y condiciones.');
  }

  function validarComentarios() {
    const el = document.getElementById('comentarios');
    const ok = el.value.length <= 200;
    return setEstado('campo-comentarios', ok, 'Máximo 200 caracteres.');
  }

  /* ---------- Validación en vivo ---------- */
  document.getElementById('nombre').addEventListener('blur', validarNombre);
  document.getElementById('correo').addEventListener('blur', validarCorreo);
  document.getElementById('telefono').addEventListener('blur', validarTelefono);
  document.getElementById('edad').addEventListener('blur', validarEdad);
  document.getElementById('pais').addEventListener('change', validarPais);

  document.querySelectorAll('input[name="genero"]').forEach(function (r) {
    r.addEventListener('change', validarGenero);
  });

  document.getElementById('terminos').addEventListener('change', validarTerminos);
  document.getElementById('comentarios').addEventListener('input', validarComentarios);

  /* =========================================================
     ENVÍO DEL FORMULARIO CORREGIDO
     ========================================================= */
  form.addEventListener('submit', function (e) {
    const resultados = [
      validarNombre(),
      validarCorreo(),
      validarTelefono(),
      validarEdad(),
      validarPais(),
      validarGenero(),
      validarTerminos(),
      validarComentarios()
    ];

    // SI HAY ERRORES, DETENEMOS EL ENVÍO
    if (!resultados.every(Boolean)) {
      e.preventDefault(); 
      mensajeExito.classList.remove('visible');
      const primerInvalido = form.querySelector(
        '.campo.invalido input, .campo.invalido select, .campo.invalido textarea'
      );
      if (primerInvalido) primerInvalido.focus();
    }
    // SI TODO ESTÁ BIEN, NO DETENEMOS EL ENVÍO. 
    // El formulario viaja a procesar_contacto.php automáticamente.
  });

  /* =========================================================
     MENSAJES DE ÉXITO O ERROR AL VOLVER DEL SERVIDOR (PHP)
     ========================================================= */
  (function mostrarMensajesServidor() {
    const params = new URLSearchParams(window.location.search);
    const error  = params.get('error');

    if (params.get('exito') === '1') {
      mensajeExito.textContent = '¡Gracias! Tu mensaje se guardó correctamente en la base de datos.';
      mensajeExito.style.background = '';
      mensajeExito.style.borderLeftColor = '';
      mensajeExito.style.color = '';
      mensajeExito.classList.add('visible');
      window.history.replaceState({}, '', 'contacto.html');
      setTimeout(function () { mensajeExito.classList.remove('visible'); }, 6000);
    }

    if (error) {
      mensajeExito.style.background = '#fee2e2';
      mensajeExito.style.borderLeftColor = '#dc2626';
      mensajeExito.style.color = '#7f1d1d';
      mensajeExito.textContent = 'No se pudo guardar: revisa los campos (' + error + ').';
      mensajeExito.classList.add('visible');
      window.history.replaceState({}, '', 'contacto.html');
    }
  })();
});