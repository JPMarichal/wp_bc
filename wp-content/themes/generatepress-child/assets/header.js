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

  // Mobile nav toggle
  var navToggle = document.querySelector('.bc-nav-toggle');
  var navMenu = document.querySelector('.bc-nav-menu');
  if (navToggle && navMenu) {
    navToggle.addEventListener('click', function () {
      navMenu.classList.toggle('is-open');
    });
  }
})();
