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
})();
