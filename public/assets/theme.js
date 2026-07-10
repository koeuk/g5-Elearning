/* Theme persistence + toggle wiring for the student UI.
 * The no-flash snippet (set data-theme before paint) lives inline in each page
 * <head>; this file wires up any [data-theme-toggle] buttons and keeps them in
 * sync. Preference is stored in localStorage under 'eLearnTheme'. */
(function () {
  var KEY = 'eLearnTheme';
  function current() { return document.documentElement.getAttribute('data-theme') || 'light'; }
  function syncButtons(t) {
    document.querySelectorAll('[data-theme-toggle]').forEach(function (b) {
      b.setAttribute('aria-pressed', String(t === 'dark'));
      b.setAttribute('title', t === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
    });
  }
  function set(t) {
    document.documentElement.setAttribute('data-theme', t);
    try { localStorage.setItem(KEY, t); } catch (e) {}
    syncButtons(t);
  }
  window.eLearnToggleTheme = function () { set(current() === 'dark' ? 'light' : 'dark'); };
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-theme-toggle]').forEach(function (b) {
      b.addEventListener('click', window.eLearnToggleTheme);
    });
    syncButtons(current());
  });

  /* Generic dropdown menus: a [data-menu-toggle="menuId"] button toggles the
     matching element; any outside click closes open menus. Used by the navbar
     profile menu. */
  document.addEventListener('click', function (e) {
    var trigger = e.target.closest('[data-menu-toggle]');
    var targetId = trigger ? trigger.getAttribute('data-menu-toggle') : null;
    document.querySelectorAll('.snav__menu.is-open').forEach(function (m) {
      if (m.id !== targetId) m.classList.remove('is-open');
    });
    if (trigger) {
      var menu = document.getElementById(targetId);
      if (menu) menu.classList.toggle('is-open');
    }
  });
})();
