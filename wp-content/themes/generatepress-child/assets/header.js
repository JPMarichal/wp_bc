(function () {
  'use strict';

  // Search toggle
  var searchToggle = document.querySelector('.bc-search-toggle');
  var searchWrapper = document.querySelector('.bc-search-form-wrapper');
  if (searchToggle && searchWrapper) {
    searchToggle.addEventListener('click', function (e) {
      e.stopPropagation();
      searchWrapper.classList.toggle('is-open');
    });
    document.addEventListener('click', function (e) {
      if (!searchWrapper.contains(e.target) && !searchToggle.contains(e.target)) {
        searchWrapper.classList.remove('is-open');
      }
    });
  }

  // User dropdown
  var userToggle = document.querySelector('.bc-user-toggle');
  var userSubmenu = document.querySelector('.bc-user-submenu');
  if (userToggle && userSubmenu) {
    userToggle.addEventListener('click', function (e) {
      e.stopPropagation();
      userSubmenu.style.display = userSubmenu.style.display === 'block' ? '' : 'block';
    });
    document.addEventListener('click', function () {
      if (userSubmenu) userSubmenu.style.display = '';
    });
    userSubmenu.addEventListener('click', function (e) {
      e.stopPropagation();
    });
  }

  // Off-canvas mobile menu
  var navToggle = document.querySelector('.bc-nav-toggle');
  var offcanvas = document.querySelector('.bc-offcanvas');
  var overlay = document.querySelector('.bc-offcanvas-overlay');
  var closeBtn = document.querySelector('.bc-offcanvas-close');
  var bodyEl = document.body;

  function openOffcanvas() {
    if (!offcanvas || !overlay) return;
    offcanvas.classList.add('is-open');
    overlay.classList.add('is-open');
    bodyEl.style.overflow = 'hidden';
    if (navToggle) navToggle.classList.add('is-open');
    navToggle.setAttribute('aria-label', 'Cerrar menú');
  }

  function closeOffcanvas() {
    if (!offcanvas || !overlay) return;
    offcanvas.classList.remove('is-open');
    overlay.classList.remove('is-open');
    bodyEl.style.overflow = '';
    if (navToggle) navToggle.classList.remove('is-open');
    navToggle.setAttribute('aria-label', 'Abrir menú');
  }

  if (navToggle && offcanvas && overlay) {
    navToggle.addEventListener('click', function () {
      if (offcanvas.classList.contains('is-open')) {
        closeOffcanvas();
      } else {
        openOffcanvas();
      }
    });

    overlay.addEventListener('click', closeOffcanvas);

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && offcanvas.classList.contains('is-open')) {
        closeOffcanvas();
      }
    });
  }

  if (closeBtn) {
    closeBtn.addEventListener('click', closeOffcanvas);
  }
})();
