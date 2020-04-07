
(function () {
  if (typeof ($) === 'undefined') {
    window.$ = jQuery;
  }

  window.adminQuickbarInstance = new AdminQuickbar();
})();