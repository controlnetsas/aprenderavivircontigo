// Agrega un botón de ojo a todos los campos de contraseña y valida que la confirmación coincida.
(function () {
  var css = '.pw-wrap{position:relative}.pw-wrap input{padding-right:46px!important}' +
    '.pw-ojo{position:absolute;right:6px;top:50%;transform:translateY(-50%);background:none;border:0;cursor:pointer;' +
    'padding:8px;font-size:18px;line-height:1;color:#211A33;opacity:.7}.pw-ojo:hover{opacity:1}';
  var st = document.createElement('style'); st.textContent = css; document.head.appendChild(st);

  document.querySelectorAll('input[type="password"]').forEach(function (input) {
    var wrap = document.createElement('span');
    wrap.className = 'pw-wrap';
    wrap.style.display = 'block';
    input.parentNode.insertBefore(wrap, input);
    wrap.appendChild(input);
    var b = document.createElement('button');
    b.type = 'button'; b.className = 'pw-ojo';
    b.setAttribute('aria-label', 'Mostrar contraseña'); b.textContent = '👁';
    b.addEventListener('click', function () {
      var ver = input.type === 'password';
      input.type = ver ? 'text' : 'password';
      b.textContent = ver ? '🙈' : '👁';
      b.setAttribute('aria-label', ver ? 'Ocultar contraseña' : 'Mostrar contraseña');
    });
    wrap.appendChild(b);
  });

  // Confirmación: cualquier formulario con password y password2
  document.querySelectorAll('form').forEach(function (f) {
    var p1 = f.querySelector('[name="password"]'), p2 = f.querySelector('[name="password2"]');
    if (!p1 || !p2) return;
    function revisar() {
      p2.setCustomValidity(p2.value && p1.value !== p2.value ? 'Las contraseñas no coinciden' : '');
    }
    p1.addEventListener('input', revisar); p2.addEventListener('input', revisar);
    f.addEventListener('submit', function (e) {
      revisar();
      if (p1.value !== p2.value) {
        e.preventDefault();
        var s = f.querySelector('.form-status');
        if (s) { s.textContent = 'Las contraseñas no coinciden. Vuelve a escribirlas.'; s.className = 'form-status error'; }
        p2.focus();
      }
    });
  });
})();