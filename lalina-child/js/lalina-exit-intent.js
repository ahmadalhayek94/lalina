/**
 * Lalina Exit-Intent Popup
 * Triggers when mouse leaves document (desktop)
 * or after 40s / 60% scroll depth on mobile.
 */
( function() {
  'use strict';
  var popup = document.getElementById('lalina-exit-popup');
  var closeBtn = document.getElementById('lalina-popup-close');
  var skipBtn = document.getElementById('lalina-popup-skip');
  var form = document.getElementById('lalina-popup-form');
  if ( ! popup ) return;
  if ( sessionStorage.getItem('lalina_popup_shown') ) return;
  function getCookie(name) {
    var matches = document.cookie.match( new RegExp('(?:^|; )' + name + '=([^;]*)') );
    return matches ? decodeURIComponent(matches[1]) : null;
  }
  if ( getCookie('lalina_popup_shown') ) return;
  var triggered = false;
  function showPopup() {
    if ( triggered ) return; triggered = true;
    popup.classList.add('active'); popup.setAttribute('aria-hidden','false');
    document.body.style.overflow = 'hidden';
    sessionStorage.setItem('lalina_popup_shown','1');
    var expires = new Date( Date.now() + 7 * 864e5 ).toUTCString();
    document.cookie = 'lalina_popup_shown=1; expires=' + expires + '; path=/; SameSite=Lax';
  }
  function closePopup() {
    popup.classList.remove('active'); popup.setAttribute('aria-hidden','true');
    document.body.style.overflow = '';
  }
  document.addEventListener('mouseleave', function(e) { if ( e.clientY <= 0 ) showPopup(); });
  setTimeout( function() { if ( 'ontouchstart' in window ) showPopup(); }, 40000 );
  window.addEventListener('scroll', function() {
    if ( ! ('ontouchstart' in window) ) return;
    var pct = ( window.scrollY / ( document.body.scrollHeight - window.innerHeight ) ) * 100;
    if ( pct >= 60 ) showPopup();
  });
  closeBtn && closeBtn.addEventListener('click', closePopup);
  skipBtn && skipBtn.addEventListener('click', closePopup);
  popup.addEventListener('click', function(e) { if ( e.target === popup ) closePopup(); });
  document.addEventListener('keydown', function(e) { if ( e.key === 'Escape' ) closePopup(); });
  form && form.addEventListener('submit', function(e) {
    e.preventDefault();
    var emailInput = form.querySelector('input[type="email"]');
    var email = emailInput ? emailInput.value.trim() : '';
    if ( ! email || ! /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) ) { emailInput && emailInput.focus(); return; }
    fetch( '/wp-admin/admin-ajax.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'action=lalina_popup_subscribe&email=' + encodeURIComponent(email)
    }).then( function() {
      form.innerHTML = '<p style="font-family:var(--lalina-font-serif);font-size:20px;font-style:italic;color:var(--lalina-noir);">Your code is on its way. ✦</p><p style="font-size:13px;margin-top:8px;">Use <strong>WELCOME10</strong> at checkout.</p>';
    }).catch( function() { closePopup(); });
  });
} )();
