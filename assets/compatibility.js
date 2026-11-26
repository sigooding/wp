/* Avoid duplicate navigation controls and let plugin markup inherit theme styling. */
(function () {
  function markMegaMenu() {
    var hasMega = !!document.querySelector('.mega-menu-wrap, #mega-menu-wrap-primary');
    document.documentElement.classList.toggle('nexora-has-megamenu', hasMega);
    if (hasMega) {
      document.body.classList.add('nexora-has-megamenu');
      var ownToggle = document.querySelector('.menu-toggle');
      if (ownToggle) { ownToggle.hidden = true; ownToggle.setAttribute('aria-hidden', 'true'); }
      var nav = document.querySelector('.navigation-bar');
      if (nav) { nav.classList.add('navigation-open'); }
    }
  }
  if (document.readyState !== 'loading') { markMegaMenu(); } else { document.addEventListener('DOMContentLoaded', markMegaMenu); }
})();
