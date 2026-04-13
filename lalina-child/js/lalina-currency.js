/**
 * Lalina Currency Auto-Detect
 * Detects visitor country via IP and switches WooCommerce currency.
 * GBP, EUR, USD, AED, CAD, AUD, SEK, NOK, CHF, SAR
 */
( function() {
  'use strict';
  var MAP = { GB:'GBP',US:'USD',CA:'CAD',AU:'AUD',AE:'AED',SA:'SAR',DE:'EUR',FR:'EUR',IT:'EUR',ES:'EUR',NL:'EUR',BE:'EUR',AT:'EUR',PT:'EUR',IE:'EUR',FI:'EUR',SE:'SEK',NO:'NOK',CH:'CHF' };
  if ( sessionStorage.getItem('lalina_currency_manual') === '1' ) return;
  var cached = sessionStorage.getItem('lalina_currency');
  function setCurrency( code ) {
    if ( ! code ) return;
    if ( typeof WOOCS !== 'undefined' && WOOCS.current_currency !== code ) {
      WOOCS.current_currency = code;
      WOOCS.storage.set('WOOCS_CURRENCY', code);
      var url = new URL(window.location.href); url.searchParams.set('currency', code);
      window.history.replaceState({}, '', url.toString()); window.location.reload(); return;
    }
    if ( window.location.search.indexOf('currency=') === -1 ) {
      if ( sessionStorage.getItem('lalina_currency_set') !== code ) {
        sessionStorage.setItem('lalina_currency_set', code);
        window.location.href = window.location.href + (window.location.search ? '&' : '?') + 'currency=' + code;
      }
    }
  }
  if ( cached ) { setCurrency(cached); return; }
  document.addEventListener('click', function(e) {
    if ( e.target.closest('[data-currency],.currency-switcher a,.woocs_current_currency') ) {
      sessionStorage.setItem('lalina_currency_manual','1');
    }
  });
  function detect() {
    fetch('https://ip-api.com/json/?fields=countryCode', { cache: 'no-store' })
      .then(function(r){return r.json();})
      .then(function(d){
        var code = MAP[d.countryCode] || 'USD';
        sessionStorage.setItem('lalina_currency', code);
        setCurrency(code);
      }).catch(function(){});
  }
  if ( document.readyState === 'complete' ) { detect(); } else { window.addEventListener('load', detect); }
} )();
