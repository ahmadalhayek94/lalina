<?php
/**
 * Lalina Child Theme — functions.php
 * Brand: "Where Craft Meets Confidence."
 *
 * @package lalina-child
 */

defined( 'ABSPATH' ) || exit;

/* ============================================================
   1. ENQUEUE PARENT + CHILD STYLES & SCRIPTS
   ============================================================ */
add_action( 'wp_enqueue_scripts', 'lalina_child_enqueue', 20 );
function lalina_child_enqueue() {
    wp_enqueue_style( 'flatsome-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'lalina-child-style', get_stylesheet_directory_uri() . '/style.css', [ 'flatsome-style' ], '1.0.0' );
    wp_enqueue_script( 'lalina-image-fix', get_stylesheet_directory_uri() . '/js/lalina-image-fix.js', [], '1.0.0', true );
    wp_enqueue_script( 'lalina-currency', get_stylesheet_directory_uri() . '/js/lalina-currency.js', [], '1.0.0', true );
    if ( is_product() ) {
        wp_enqueue_script( 'lalina-sticky-atc', get_stylesheet_directory_uri() . '/js/lalina-sticky-atc.js', [], '1.0.0', true );
        wp_localize_script( 'lalina-sticky-atc', 'lalinaProduct', [
            'name'  => get_the_title(),
            'price' => strip_tags( wc_price( wc_get_product( get_the_ID() )->get_price() ) ),
        ] );
    }
    if ( ! is_cart() && ! is_checkout() && ! is_account_page() ) {
        wp_enqueue_script( 'lalina-exit-intent', get_stylesheet_directory_uri() . '/js/lalina-exit-intent.js', [], '1.0.0', true );
    }
    if ( is_shop() || is_product() || is_product_category() || is_product_tag() ) {
        add_filter( 'wp_lazy_loading_enabled', '__return_false' );
    }
}

/* ============================================================
   2. TRUST BADGE STRIP
   ============================================================ */
add_action( 'flatsome_after_header', 'lalina_trust_strip', 10 );
function lalina_trust_strip() {
    echo '<div class="lalina-trust-strip" role="region" aria-label="Trust signals">';
    $items = [ 'Genuine Leather', 'Worldwide Shipping', '30-Day Returns', 'Secure Checkout' ];
    foreach ( $items as $item ) {
        echo '<div class="trust-item"><span class="trust-star">✦</span><span>' . esc_html( $item ) . '</span></div>';
    }
    echo '</div>';
}

/* ============================================================
   3. SOCIAL PROOF BAR — homepage only
   ============================================================ */
add_action( 'flatsome_after_header', 'lalina_proof_bar', 15 );
function lalina_proof_bar() {
    if ( ! is_front_page() ) return;
    echo '<div class="lalina-proof-bar"><div class="proof-inner">';
    echo '<span class="stars">★★★★★</span>';
    echo '<span class="proof-text">Rated <span class="proof-count">4.9 / 5</span> — Loved by <span class="proof-count">2,000+ women</span> worldwide</span>';
    echo '</div></div>';
}

/* ============================================================
   4. STICKY MOBILE ATC BAR
   ============================================================ */
add_action( 'woocommerce_after_single_product', 'lalina_sticky_atc_html' );
function lalina_sticky_atc_html() {
    global $product;
    if ( ! $product ) return;
    $name  = esc_html( mb_substr( $product->get_name(), 0, 28 ) );
    $price = $product->get_price_html();
    echo '<div id="lalina-sticky-atc" aria-hidden="true" role="complementary">';
    echo '<div class="lalina-sticky-atc-info">';
    echo '<div class="lalina-sticky-atc-name">' . $name . '</div>';
    echo '<div class="lalina-sticky-atc-price">' . $price . '</div>';
    echo '</div>';
    echo '<button class="lalina-sticky-atc-btn" id="lalina-satc-btn" type="button">Add to Bag</button>';
    echo '</div>';
}

/* ============================================================
   5. STOCK SCARCITY INDICATOR
   ============================================================ */
add_action( 'woocommerce_single_product_summary', 'lalina_scarcity_indicator', 25 );
function lalina_scarcity_indicator() {
    global $product;
    if ( ! $product || ! $product->managing_stock() ) return;
    $stock = $product->get_stock_quantity();
    if ( $stock > 0 && $stock <= 5 ) {
        $colour = '';
        if ( $product->is_type('variable') ) {
            foreach ( $product->get_variation_attributes() as $key => $values ) {
                if ( stripos($key,'color') !== false || stripos($key,'colour') !== false ) {
                    $colour = ' in ' . implode(', ', $values); break;
                }
            }
        }
        printf( '<div class="lalina-scarcity">⚡ Only %d left%s — order soon</div>', (int) $stock, esc_html($colour) );
    }
}

/* ============================================================
   6. IN-STOCK TRUST LINE
   ============================================================ */
add_action( 'woocommerce_single_product_summary', 'lalina_stock_trust_line', 26 );
function lalina_stock_trust_line() {
    global $product;
    if ( $product && $product->is_in_stock() ) {
        echo '<div class="lalina-stock-trust"><span class="dot"></span><span>In stock &mdash; Ships within 2 business days</span></div>';
    }
}

/* ============================================================
   7. SIZE GUIDE TAB
   ============================================================ */
add_filter( 'woocommerce_product_tabs', 'lalina_add_size_guide_tab' );
function lalina_add_size_guide_tab( $tabs ) {
    $tabs['lalina_size_guide'] = [ 'title' => 'Size Guide', 'priority' => 25, 'callback' => 'lalina_size_guide_tab_content' ];
    return $tabs;
}
function lalina_size_guide_tab_content() {
    echo '<div class="lalina-size-guide"><table>';
    echo '<thead><tr><th>Size</th><th>Width</th><th>Height</th><th>Depth</th><th>Strap Drop</th></tr></thead><tbody>';
    $sizes = [
        [ 'Mini', '22 cm / 8.7"', '16 cm / 6.3"', '8 cm / 3.1"',  '50 cm / 19.7"' ],
        [ 'Midi', '28 cm / 11"',  '20 cm / 7.9"', '10 cm / 3.9"', '55 cm / 21.7"' ],
        [ 'Maxi', '34 cm / 13.4"','25 cm / 9.8"', '12 cm / 4.7"', '60 cm / 23.6"' ],
    ];
    foreach ( $sizes as $s ) {
        echo '<tr><td><strong>' . $s[0] . '</strong></td><td>' . $s[1] . '</td><td>' . $s[2] . '</td><td>' . $s[3] . '</td><td>' . $s[4] . '</td></tr>';
    }
    echo '</tbody></table><p style="font-size:12px;color:#9a9087;margin-top:12px;">All measurements are approximate. Leather is a natural material — slight variations add to its character.</p></div>';
}

/* ============================================================
   8. LEATHER CARE GUIDE TAB
   ============================================================ */
add_filter( 'woocommerce_product_tabs', 'lalina_add_care_guide_tab' );
function lalina_add_care_guide_tab( $tabs ) {
    $tabs['lalina_care_guide'] = [ 'title' => 'Leather Care', 'priority' => 30, 'callback' => 'lalina_care_guide_tab_content' ];
    return $tabs;
}
function lalina_care_guide_tab_content() {
    $care = [
        'Store in the provided cotton dust bag when not in use — away from direct sunlight',
        'Wipe clean with a soft, dry cloth after use to remove surface dust',
        'Apply a leather conditioner every 3–6 months to maintain suppleness and depth of colour',
        'Avoid prolonged exposure to moisture — if caught in rain, pat dry gently and allow to air-dry naturally',
        'Do not use chemical cleaners, acetone, or bleach on any leather surface',
        'Stuff with tissue paper when storing to maintain the bag’s architectural silhouette',
    ];
    $suede = [
        'Brush gently with a soft suede brush to restore the nap after wear',
        'Use a suede protector spray before first use and reapply each season',
        'Address marks promptly — dried stains are significantly harder to remove',
    ];
    echo '<div class="lalina-care-guide">';
    echo '<h4>Caring For Your Lalina</h4><ul>';
    foreach ( $care as $c ) echo '<li>' . esc_html($c) . '</li>';
    echo '</ul><h4>Suede Care</h4><ul>';
    foreach ( $suede as $s ) echo '<li>' . esc_html($s) . '</li>';
    echo '</ul><p style="font-size:13px;color:#4A4540;margin-top:16px;line-height:1.7;">Genuine leather develops a beautiful patina over time — a mark of real quality and lived experience. <em>Your Lalina is designed to age with you.</em></p></div>';
}

/* ============================================================
   9. YOU MAY ALSO LOVE — CROSS-SELL
   ============================================================ */
add_action( 'woocommerce_after_single_product_summary', 'lalina_cross_sell_section', 15 );
function lalina_cross_sell_section() {
    global $product;
    if ( ! $product ) return;
    $related_ids = wc_get_related_products( $product->get_id(), 3 );
    if ( empty($related_ids) ) return;
    echo '<div class="lalina-cross-sell"><h3>You May Also <em>Love</em></h3><ul class="products">';
    foreach ( $related_ids as $id ) {
        $r = wc_get_product($id);
        if ( ! $r ) continue;
        $link = get_permalink($id);
        echo '<li class="product">';
        echo '<a href="' . esc_url($link) . '">' . $r->get_image('woocommerce_thumbnail') . '</a>';
        echo '<h2 class="woocommerce-loop-product__title"><a href="' . esc_url($link) . '">' . esc_html($r->get_name()) . '</a></h2>';
        echo '<span class="price">' . $r->get_price_html() . '</span>';
        echo '</li>';
    }
    echo '</ul></div>';
}

/* ============================================================
   10. EXIT-INTENT POPUP HTML
   ============================================================ */
add_action( 'wp_footer', 'lalina_exit_popup_html' );
function lalina_exit_popup_html() {
    if ( is_cart() || is_checkout() || is_account_page() ) return;
    if ( isset($_COOKIE['lalina_popup_shown']) ) return;
    echo '<div id="lalina-exit-popup" role="dialog" aria-modal="true" aria-labelledby="lalina-popup-heading">';
    echo '<div class="lalina-popup-box">';
    echo '<button class="lalina-popup-close" id="lalina-popup-close" aria-label="Close">&times;</button>';
    echo '<div class="lalina-popup-eyebrow">A Gift, Before You Go</div>';
    echo '<h2 id="lalina-popup-heading">10% Off<br>Your First Order</h2>';
    echo '<p>Join the Lalina community and receive a private discount for your first bag — plus early access to new arrivals.</p>';
    echo '<form class="lalina-popup-form" id="lalina-popup-form" novalidate>';
    echo '<input type="email" name="email" placeholder="Your email address" required autocomplete="email" />';
    echo '<button type="submit">Claim 10%</button></form>';
    echo '<button class="lalina-popup-skip" id="lalina-popup-skip" type="button">No thank you, I\'ll pay full price</button>';
    echo '</div></div>';
}

/* ============================================================
   11. AGGREGATE RATING NEAR PRICE
   ============================================================ */
add_action( 'woocommerce_single_product_summary', 'lalina_rating_near_price', 12 );
function lalina_rating_near_price() {
    global $product;
    if ( ! $product ) return;
    $avg = $product->get_average_rating();
    $cnt = $product->get_review_count();
    if ( $avg > 0 && $cnt > 0 ) {
        printf(
            '<div style="display:flex;align-items:center;gap:8px;margin:0 0 12px;font-family:var(--lalina-font-sans);font-size:13px;"><span style="color:var(--lalina-gold);letter-spacing:2px;">★★★★★</span><span style="color:var(--lalina-taupe);">%s &mdash; %d review%s</span></div>',
            esc_html( number_format((float)$avg,1) ), (int)$cnt, $cnt !== 1 ? 's' : ''
        );
    }
}

/* ============================================================
   12. BEST SELLER BADGE
   ============================================================ */
add_action( 'woocommerce_before_shop_loop_item_title', 'lalina_bestseller_badge', 5 );
function lalina_bestseller_badge() {
    global $product;
    if ( $product && (int)$product->get_total_sales() >= 10 ) {
        echo '<span class="lalina-badge-bestseller">Best Seller</span>';
    }
}

/* ============================================================
   13. HOMEPAGE EMAIL CAPTURE SECTION
   ============================================================ */
add_action( 'woocommerce_after_main_content', 'lalina_email_section_homepage', 5 );
function lalina_email_section_homepage() {
    if ( ! is_front_page() && ! is_home() ) return;
    echo '<section class="lalina-email-section">';
    echo '<h2>Join <em>Lalina</em></h2>';
    echo '<p>Get 10% off your first order — plus early access to new arrivals and exclusive member offers.</p>';
    echo '<form class="lalina-email-form" id="lalina-footer-email-form" novalidate>';
    echo '<input type="email" name="email" placeholder="Enter your email address" required autocomplete="email" />';
    echo '<button type="submit">Join Now</button></form></section>';
}

/* ============================================================
   14. PRODUCT SCHEMA (SEO)
   ============================================================ */
add_action( 'woocommerce_single_product_summary', 'lalina_product_schema', 100 );
function lalina_product_schema() {
    global $product;
    if ( ! $product ) return;
    $schema = [
        '@context' => 'https://schema.org', '@type' => 'Product',
        'name'     => $product->get_name(),
        'description' => wp_strip_all_tags( $product->get_description() ),
        'brand'    => [ '@type' => 'Brand', 'name' => 'Lalina Bags' ],
        'offers'   => [
            '@type' => 'Offer', 'price' => $product->get_price(),
            'priceCurrency' => get_woocommerce_currency(),
            'availability'  => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'url'           => get_permalink(),
        ],
    ];
    $avg = $product->get_average_rating();
    if ( $avg > 0 ) {
        $schema['aggregateRating'] = [ '@type' => 'AggregateRating', 'ratingValue' => $avg, 'reviewCount' => $product->get_review_count() ];
    }
    echo '<script type="application/ld+json">' . wp_json_encode($schema) . '</script>';
}

/* ============================================================
   15. OPEN GRAPH META TAGS
   ============================================================ */
add_action( 'wp_head', 'lalina_og_meta', 5 );
function lalina_og_meta() {
    global $product;
    if ( ! is_singular() && ! is_product() ) return;
    $title = get_the_title();
    $desc  = has_excerpt() ? get_the_excerpt() : get_bloginfo('description');
    $url   = get_permalink();
    $image = '';
    if ( is_product() && $product ) {
        $iid = $product->get_image_id();
        if ( $iid ) $image = wp_get_attachment_image_url($iid,'large');
    } elseif ( has_post_thumbnail() ) {
        $image = get_the_post_thumbnail_url(null,'large');
    }
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($desc) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
    echo '<meta property="og:type" content="' . (is_product() ? 'product' : 'website') . '">' . "\n";
    if ( $image ) echo '<meta property="og:image" content="' . esc_url($image) . '">' . "\n";
    echo '<meta property="og:site_name" content="Lalina Bags">' . "\n";
}

/* ============================================================
   16. LOAD ADDITIONAL FUNCTIONALITY
   ============================================================ */
require_once get_stylesheet_directory() . '/inc/lalina-shopping-feed.php';
require_once get_stylesheet_directory() . '/inc/lalina-weekly-report.php';
