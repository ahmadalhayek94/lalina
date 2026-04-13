/**
 * Lalina Sticky Mobile Add-to-Cart Bar
 * Slides up when the main ATC button scrolls off screen.
 * Mobile only (max-width: 768px). No jQuery.
 */
( function() {
  'use strict';
  var bar = document.getElementById('lalina-sticky-atc');
  var stickyBtn = document.getElementById('lalina-satc-btn');
  var mainATC = document.querySelector('.single_add_to_cart_button');
  var mainForm = document.querySelector('form.cart');
  if ( ! bar || ! mainATC ) return;
  if ( typeof lalinaProduct !== 'undefined' ) {
    var nameEl = bar.querySelector('.lalina-sticky-atc-name');
    var priceEl = bar.querySelector('.lalina-sticky-atc-price');
    if ( nameEl ) nameEl.textContent = lalinaProduct.name;
    if ( priceEl ) priceEl.innerHTML = lalinaProduct.price;
  }
  function isMobile() { return window.innerWidth <= 768; }
  function getOffset( el ) { var rect = el.getBoundingClientRect(); return rect.top + rect.height; }
  function updateBar() {
    if ( ! isMobile() ) { bar.classList.remove('visible'); bar.setAttribute('aria-hidden','true'); return; }
    if ( getOffset( mainATC ) < 0 ) { bar.classList.add('visible'); bar.setAttribute('aria-hidden','false'); }
    else { bar.classList.remove('visible'); bar.setAttribute('aria-hidden','true'); }
  }
  var ticking = false;
  window.addEventListener('scroll', function() {
    if ( ! ticking ) { requestAnimationFrame( function() { updateBar(); ticking = false; }); ticking = true; }
  });
  window.addEventListener('resize', updateBar);
  stickyBtn && stickyBtn.addEventListener('click', function() {
    var variations = document.querySelector('.variations_form');
    if ( variations ) {
      var selects = variations.querySelectorAll('select');
      var allSelected = Array.from(selects).every( function(s) { return s.value && s.value !== ''; });
      if ( ! allSelected ) {
        mainForm && mainForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
        selects.forEach( function(s) { if ( ! s.value ) { s.style.borderColor = 'var(--lalina-gold)'; } });
        return;
      }
    }
    mainATC.click();
  });
  updateBar();
} )();
