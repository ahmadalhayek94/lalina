/**
 * Lalina Image Fix
 * Forces WooCommerce product images to load correctly
 * by resolving lazy-load JS conflicts on shop/product pages.
 */
( function() {
  'use strict';

  var SELECTORS = [
    'img[data-src]',
    'img[data-lazy-src]',
    'img[data-original]',
    '.woocommerce-product-gallery img',
    'ul.products li.product img',
    '.attachment-woocommerce_thumbnail',
    '.attachment-shop_catalog',
    'img.lazy',
    'img.lazyload',
    'img.not-loaded'
  ].join(', ');

  function forceLoadImages( root ) {
    root = root || document;
    var imgs = root.querySelectorAll( SELECTORS );
    imgs.forEach( function( img ) {
      var dataSrc = img.getAttribute('data-src')
                 || img.getAttribute('data-lazy-src')
                 || img.getAttribute('data-original')
                 || img.getAttribute('data-srcset');
      if ( dataSrc && ! img.src.includes('wp-content') ) {
        img.src = dataSrc.split(',')[0].trim().split(' ')[0];
      }
      if ( img.getAttribute('data-srcset') && ! img.srcset ) {
        img.srcset = img.getAttribute('data-srcset');
      }
      img.removeAttribute('loading');
      img.setAttribute('loading', 'eager');
      img.classList.remove('lazy', 'lazyload', 'not-loaded', 'wp-lazy');
      if ( ! img.complete ) {
        img.parentElement && img.parentElement.classList.add('lalina-loading');
        img.addEventListener('load', function() {
          img.parentElement && img.parentElement.classList.remove('lalina-loading');
        }, { once: true });
      }
    });
  }

  function fixSvgPlaceholders() {
    var imgs = document.querySelectorAll('img[src*="svg+xml"]');
    imgs.forEach( function( img ) {
      var dataSrc = img.getAttribute('data-src')
                 || img.getAttribute('data-lazy-src')
                 || img.getAttribute('data-original');
      if ( dataSrc ) {
        img.src = dataSrc;
        img.removeAttribute('loading');
        img.setAttribute('loading', 'eager');
      }
    });
  }

  function init() {
    forceLoadImages();
    fixSvgPlaceholders();
    if ( 'MutationObserver' in window ) {
      var observer = new MutationObserver( function( mutations ) {
        mutations.forEach( function( mutation ) {
          if ( mutation.addedNodes.length ) {
            mutation.addedNodes.forEach( function( node ) {
              if ( node.nodeType === 1 ) { forceLoadImages( node ); fixSvgPlaceholders(); }
            });
          }
        });
      });
      observer.observe( document.body, { childList: true, subtree: true });
    }
    document.body.addEventListener( 'wc_fragments_loaded', forceLoadImages );
    document.body.addEventListener( 'wc_fragments_refreshed', forceLoadImages );
    document.body.addEventListener( 'updated_wc_div', forceLoadImages );
  }

  if ( document.readyState === 'loading' ) {
    document.addEventListener( 'DOMContentLoaded', init );
  } else { init(); }

} )();
