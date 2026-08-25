<?php
/**
 * Lalina Bags — Custom Functions
 * =========================================================
 * Add ALL of this to your child theme's functions.php
 * OR place this file in your child theme folder and
 * require it from functions.php like this:
 *   require_once get_stylesheet_directory() . '/lalina-custom-functions.php';
 *
 * @package Lalina-Bags
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// =========================================================
// 1. ENQUEUE CUSTOM CSS & JS
// =========================================================
add_action( 'wp_enqueue_scripts', 'lalina_enqueue_assets' );
function lalina_enqueue_assets() {
    wp_enqueue_style(
        'lalina-custom',
        get_stylesheet_directory_uri() . '/lalina-custom.css',
        array(),
        '2.0.0'
    );

    wp_enqueue_script(
        'lalina-custom',
        get_stylesheet_directory_uri() . '/lalina-custom.js',
        array( 'jquery' ),
        '2.0.0',
        true
    );

    wp_localize_script( 'lalina-custom', 'lalinaData', array(
        'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
        'nonce'     => wp_create_nonce( 'lalina_nonce' ),
        'currency'  => get_woocommerce_currency_symbol(),
        'freeShip'  => '60',
    ));
}


// =========================================================
// 2. ANNOUNCEMENT BAR
// =========================================================
add_action( 'wp_body_open', 'lalina_announcement_bar', 1 );
function lalina_announcement_bar() {
    $messages = array(
        '🌿 Free UK shipping on orders over £60 — Use code <strong>WELCOME10</strong> for 10% off your first order',
        '🛍️ New Collection — Premium vegan leather handbags crafted in London',
        '✅ Cruelty-Free · Sustainably Made · 30-Day Returns',
    );
    // Rotate by day of week — use gmdate() for WordPress timezone safety
    $msg = $messages[ gmdate('N') % count($messages) ];
    ?>
    <div id="lalina-announcement-bar" class="lalina-announcement-bar" role="banner">
        <div class="lalina-announcement-inner">
            <p class="lalina-announcement-text"><?php echo wp_kses_post( $msg ); ?></p>
        </div>
        <button class="lalina-announcement-close" aria-label="Close announcement bar" id="lalina-close-bar">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1 1L13 13M13 1L1 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
        </button>
    </div>
    <?php
}


// =========================================================
// 3. TRUST BADGES — Below Add to Cart Button
// =========================================================
add_action( 'woocommerce_single_product_summary', 'lalina_trust_badges', 31 );
function lalina_trust_badges() {
    ?>
    <div class="lalina-trust-badges">
        <div class="lalina-trust-badge">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
            <span>Secure<br>Checkout</span>
        </div>
        <div class="lalina-trust-badge">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.21"/>
            </svg>
            <span>Free<br>Returns</span>
        </div>
        <div class="lalina-trust-badge">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2z"/><path d="M12 8v4l3 3"/>
            </svg>
            <span>Vegan &amp;<br>Cruelty-Free</span>
        </div>
        <div class="lalina-trust-badge">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22C6.5 11 2 7.5 2 5a10 10 0 0 1 20 0c0 2.5-4.5 6-10 17z"/>
            </svg>
            <span>Sustainably<br>Made</span>
        </div>
    </div>
    <?php
}


// =========================================================
// 4. PRODUCT PAGE — Sustainability Story Accordion
// =========================================================
add_action( 'woocommerce_single_product_summary', 'lalina_product_accordion', 40 );
function lalina_product_accordion() {
    global $product;
    $accordion_items = array(
        array(
            'title' => 'Materials & Craftsmanship',
            'icon'  => '✦',
            'content' => 'Each Lalina bag is crafted from premium vegan leather — a plant-based material that replicates the look and feel of traditional leather without any animal harm. Our hardware is nickel-free and tarnish-resistant. Interior linings are made from recycled RPET fabric.',
        ),
        array(
            'title' => 'Sustainability Pledge',
            'icon'  => '🌿',
            'content' => 'We are committed to cruelty-free, sustainable fashion. No animal-derived materials, dyes, or glues are used in any Lalina product. Our packaging is 100% recyclable and our shipping partners are carbon-neutral certified.',
        ),
        array(
            'title' => 'Shipping & Delivery',
            'icon'  => '📦',
            'content' => 'Free UK shipping on orders over £60. Standard delivery 3–5 working days. Express delivery (1–2 working days) available at checkout. International shipping available. Full tracking provided on all orders.',
        ),
        array(
            'title' => 'Returns & Care',
            'icon'  => '↩',
            'content' => 'We offer free returns within 30 days of delivery — no questions asked. To care for your bag, wipe with a soft damp cloth. Avoid direct sunlight for prolonged periods. Store in the included dust bag.',
        ),
    );
    ?>
    <div class="lalina-accordion" id="lalina-product-accordion">
        <?php foreach ( $accordion_items as $i => $item ) : ?>
        <div class="lalina-accordion-item" data-index="<?php echo $i; ?>">
            <button class="lalina-accordion-trigger" aria-expanded="false">
                <span class="lalina-accordion-icon"><?php echo esc_html( $item['icon'] ); ?></span>
                <span class="lalina-accordion-title"><?php echo esc_html( $item['title'] ); ?></span>
                <span class="lalina-accordion-chevron">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </span>
            </button>
            <div class="lalina-accordion-content" hidden>
                <p><?php echo esc_html( $item['content'] ); ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php
}


// =========================================================
// 5. PRODUCT PAGE — Size Guide Modal Trigger
// =========================================================
add_action( 'woocommerce_before_add_to_cart_button', 'lalina_size_guide_trigger' );
function lalina_size_guide_trigger() {
    global $product;
    // Pass the product ID to has_term() so it checks the current product, not the current global post
    if ( ! $product->is_type( 'variable' ) && ! has_term( 'bags', 'product_cat', $product->get_id() ) ) return;
    ?>
    <div class="lalina-size-guide-trigger">
        <button type="button" class="lalina-size-guide-btn" id="lalina-size-guide-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Size Guide
        </button>
    </div>

    <!-- Size Guide Modal -->
    <div id="lalina-size-modal" class="lalina-modal" aria-hidden="true" role="dialog" aria-labelledby="lalina-size-modal-title">
        <div class="lalina-modal-overlay" data-close-modal></div>
        <div class="lalina-modal-content">
            <button class="lalina-modal-close" data-close-modal aria-label="Close size guide">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
            <h3 id="lalina-size-modal-title">Size Guide</h3>
            <p class="lalina-size-intro">All measurements are approximate and taken at the widest point of the bag.</p>
            <table class="lalina-size-table">
                <thead>
                    <tr><th>Size</th><th>Width</th><th>Height</th><th>Depth</th><th>Strap Drop</th></tr>
                </thead>
                <tbody>
                    <tr><td>Mini</td><td>18 cm</td><td>14 cm</td><td>6 cm</td><td>20 cm</td></tr>
                    <tr><td>Small</td><td>24 cm</td><td>18 cm</td><td>8 cm</td><td>55 cm</td></tr>
                    <tr><td>Medium</td><td>30 cm</td><td>22 cm</td><td>10 cm</td><td>60 cm</td></tr>
                    <tr><td>Large</td><td>38 cm</td><td>28 cm</td><td>12 cm</td><td>65 cm</td></tr>
                    <tr><td>Tote</td><td>45 cm</td><td>35 cm</td><td>14 cm</td><td>28 cm</td></tr>
                </tbody>
            </table>
            <p class="lalina-size-note">💡 Tip: For everyday essentials (phone, keys, card holder), a Small or Medium is ideal. For laptop or overnight use, choose Large or Tote.</p>
        </div>
    </div>
    <?php
}


// =========================================================
// 6. CART PAGE — Upsell Row
// =========================================================
add_action( 'woocommerce_after_cart_table', 'lalina_cart_upsell_row' );
function lalina_cart_upsell_row() {
    $upsell_ids = array();
    foreach ( WC()->cart->get_cart() as $cart_item ) {
        $product    = $cart_item['data'];
        $upsell_ids = array_merge( $upsell_ids, $product->get_upsell_ids() );
    }
    $upsell_ids = array_unique( array_filter( $upsell_ids ) );
    if ( empty( $upsell_ids ) ) return;

    $upsell_products = wc_get_products( array(
        'include' => array_slice( $upsell_ids, 0, 4 ),
        'status'  => 'publish',
        'limit'   => 4,
    ));

    if ( empty( $upsell_products ) ) return;
    ?>
    <div class="lalina-cart-upsells">
        <h3 class="lalina-cart-upsells__title">Complete Your Look</h3>
        <div class="lalina-cart-upsells__grid">
            <?php foreach ( $upsell_products as $upsell ) :
                $image = wp_get_attachment_image_url( $upsell->get_image_id(), 'woocommerce_thumbnail' );
                $price = $upsell->get_price_html();
                $link  = get_permalink( $upsell->get_id() );
            ?>
            <div class="lalina-cart-upsells__item">
                <a href="<?php echo esc_url( $link ); ?>" class="lalina-cart-upsells__img-link">
                    <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $upsell->get_name() ); ?>" loading="lazy">
                </a>
                <div class="lalina-cart-upsells__info">
                    <a href="<?php echo esc_url( $link ); ?>" class="lalina-cart-upsells__name"><?php echo esc_html( $upsell->get_name() ); ?></a>
                    <span class="lalina-cart-upsells__price"><?php echo wp_kses_post( $price ); ?></span>
                    <?php
                    $atc_url = add_query_arg( array(
                        'add-to-cart' => $upsell->get_id(),
                        '_wpnonce'    => wp_create_nonce( 'add-to-cart' ),
                    ), wc_get_cart_url() );
                    ?>
                    <a href="<?php echo esc_url( $atc_url ); ?>" class="lalina-cart-upsells__add">+ Add</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}


// =========================================================
// 7. CHECKOUT — Remove Unnecessary Fields
// =========================================================
add_filter( 'woocommerce_checkout_fields', 'lalina_optimize_checkout_fields' );
function lalina_optimize_checkout_fields( $fields ) {
    if ( isset( $fields['billing']['billing_company'] ) ) {
        $fields['billing']['billing_company']['required'] = false;
        $fields['billing']['billing_company']['class'][]  = 'lalina-field-optional';
    }
    if ( isset( $fields['billing']['billing_address_2'] ) ) {
        $fields['billing']['billing_address_2']['placeholder'] = 'Apartment, suite, unit (optional)';
        $fields['billing']['billing_address_2']['label']       = 'Address line 2 (optional)';
    }
    if ( isset( $fields['billing']['billing_phone'] ) ) {
        $fields['billing']['billing_phone']['required'] = false;
    }
    return $fields;
}


// =========================================================
// 8. CHECKOUT — Trust Reassurance Below Order Button
// =========================================================
add_action( 'woocommerce_review_order_after_submit', 'lalina_checkout_trust' );
function lalina_checkout_trust() {
    ?>
    <div class="lalina-checkout-trust">
        <span class="lalina-checkout-trust__item">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            256-bit SSL Secure Checkout
        </span>
        <span class="lalina-checkout-trust__item">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.21"/></svg>
            30-Day Free Returns
        </span>
        <span class="lalina-checkout-trust__item">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22C6.5 11 2 7.5 2 5a10 10 0 0 1 20 0c0 2.5-4.5 6-10 17z"/></svg>
            Cruelty-Free &amp; Vegan
        </span>
    </div>
    <?php
}


// =========================================================
// 9. CHECKOUT — Progress Steps Indicator
// =========================================================
add_action( 'woocommerce_before_checkout_form', 'lalina_checkout_progress', 5 );
function lalina_checkout_progress() {
    ?>
    <div class="lalina-checkout-steps">
        <div class="lalina-checkout-step completed">
            <span class="step-number">✓</span>
            <span class="step-label">Cart</span>
        </div>
        <div class="lalina-checkout-step-line completed"></div>
        <div class="lalina-checkout-step active">
            <span class="step-number">2</span>
            <span class="step-label">Your Details</span>
        </div>
        <div class="lalina-checkout-step-line"></div>
        <div class="lalina-checkout-step">
            <span class="step-number">3</span>
            <span class="step-label">Confirmation</span>
        </div>
    </div>
    <?php
}


// =========================================================
// 10. RELATED PRODUCTS — Custom Heading
// =========================================================
add_filter( 'woocommerce_product_related_posts_heading', function() {
    return 'Complete the Look';
});

add_filter( 'woocommerce_output_related_products_args', function( $args ) {
    $args['posts_per_page'] = 4;
    $args['columns']        = 4;
    return $args;
});


// =========================================================
// 11. SHOP PAGE — Products Per Page
// =========================================================
add_filter( 'loop_shop_per_page', function() { return 12; }, 20 );


// =========================================================
// 12. ORDER BUMP — Simple Add-on at Checkout
// =========================================================
add_action( 'woocommerce_review_order_before_submit', 'lalina_order_bump' );
function lalina_order_bump() {
    $bump_product_id = get_option( 'lalina_order_bump_product_id', 0 );
    if ( ! $bump_product_id ) return;

    $product = wc_get_product( $bump_product_id );
    if ( ! $product || ! $product->is_purchasable() ) return;

    if ( WC()->cart->find_product_in_cart( WC()->cart->generate_cart_id( $bump_product_id ) ) ) return;
    ?>
    <div class="lalina-order-bump" id="lalina-order-bump">
        <label class="lalina-order-bump__inner">
            <input type="checkbox" id="lalina-bump-checkbox" name="lalina_order_bump" value="<?php echo esc_attr( $bump_product_id ); ?>">
            <div class="lalina-order-bump__content">
                <div class="lalina-order-bump__icon">🎁</div>
                <div class="lalina-order-bump__text">
                    <strong>YES! Add a luxury dust bag for <?php echo wp_kses_post( $product->get_price_html() ); ?></strong>
                    <span>Protect your Lalina bag — includes a branded velvet dust bag with drawstring. Usually <?php echo wp_kses_post( $product->get_price_html() ); ?>.</span>
                </div>
            </div>
        </label>
    </div>
    <?php
}

add_action( 'woocommerce_checkout_process', 'lalina_process_order_bump' );
function lalina_process_order_bump() {
    if ( ! empty( $_POST['lalina_order_bump'] ) ) {
        $product_id = absint( $_POST['lalina_order_bump'] );
        if ( $product_id && wc_get_product( $product_id ) ) {
            WC()->cart->add_to_cart( $product_id, 1 );
        }
    }
}


// =========================================================
// 13. EMAIL CAPTURE POPUP — Render HTML
// =========================================================
add_action( 'wp_footer', 'lalina_email_popup' );
function lalina_email_popup() {
    if ( is_admin() ) return;
    if ( is_checkout() || is_account_page() ) return;
    ?>
    <div id="lalina-popup" class="lalina-popup" aria-hidden="true" role="dialog" aria-labelledby="lalina-popup-title">
        <div class="lalina-popup-overlay" id="lalina-popup-overlay"></div>
        <div class="lalina-popup-content">
            <button class="lalina-popup-close" id="lalina-popup-close" aria-label="Close popup">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
            <div class="lalina-popup-image">
                <div class="lalina-popup-badge">Exclusive Offer</div>
            </div>
            <div class="lalina-popup-body">
                <h2 id="lalina-popup-title" class="lalina-popup-title">10% Off Your First Order</h2>
                <p class="lalina-popup-subtitle">Join the Lalina family and receive an exclusive welcome discount on your first purchase.</p>
                <form class="lalina-popup-form" id="lalina-popup-form" novalidate>
                    <div class="lalina-popup-field">
                        <input
                            type="email"
                            name="email"
                            id="lalina-popup-email"
                            placeholder="Your email address"
                            required
                            autocomplete="email"
                        >
                    </div>
                    <button type="submit" class="lalina-popup-submit">
                        Claim My 10% Discount
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>
                    <p class="lalina-popup-privacy">No spam, ever. Unsubscribe anytime.</p>
                </form>
                <div class="lalina-popup-success" id="lalina-popup-success" hidden>
                    <div class="lalina-popup-success-icon">✓</div>
                    <h3>Welcome to Lalina!</h3>
                    <p>Your 10% discount code has been sent to your inbox.</p>
                    <p class="lalina-popup-code">Use code: <strong>WELCOME10</strong></p>
                </div>
            </div>
        </div>
    </div>
    <?php
}

add_action( 'wp_ajax_lalina_email_subscribe', 'lalina_email_subscribe_handler' );
add_action( 'wp_ajax_nopriv_lalina_email_subscribe', 'lalina_email_subscribe_handler' );
function lalina_email_subscribe_handler() {
    check_ajax_referer( 'lalina_nonce', 'nonce' );
    $email = sanitize_email( $_POST['email'] ?? '' );
    if ( ! is_email( $email ) ) {
        wp_send_json_error( array( 'message' => 'Please enter a valid email address.' ) );
    }
    $subscribers = get_option( 'lalina_email_subscribers', array() );
    if ( ! in_array( $email, $subscribers ) ) {
        $subscribers[] = $email;
        update_option( 'lalina_email_subscribers', $subscribers );
        // TODO: Add Klaviyo API call here (see klaviyo-integration.php)
    }
    wp_send_json_success( array( 'message' => 'Subscribed!' ) );
}


// =========================================================
// 14. BREADCRUMBS — Add to Single Product & Shop Pages
// =========================================================
add_action( 'woocommerce_before_main_content', 'lalina_breadcrumbs_wrapper_open', 5 );
function lalina_breadcrumbs_wrapper_open() {
    if ( is_product() || is_shop() || is_product_category() ) {
        echo '<div class="lalina-breadcrumbs-wrap">';
        woocommerce_breadcrumb( array(
            'delimiter'   => ' <span class="lalina-bc-sep">→</span> ',
            'wrap_before' => '<nav class="lalina-breadcrumb" aria-label="Breadcrumb">',
            'wrap_after'  => '</nav>',
            'before'      => '',
            'after'       => '',
            'home'        => 'Home',
        ));
        echo '</div>';
    }
}


// =========================================================
// 15. FOOTER — Payment Icons & Trust Bar
// =========================================================
add_action( 'wp_footer', 'lalina_footer_trust_bar', 5 );
function lalina_footer_trust_bar() {
    ?>
    <div class="lalina-footer-trust">
        <div class="lalina-footer-trust__inner">
            <span class="lalina-footer-trust__label">Accepted Payments:</span>
            <div class="lalina-footer-trust__payments">
                <span class="lalina-payment-icon" title="Visa">VISA</span>
                <span class="lalina-payment-icon" title="Mastercard">MC</span>
                <span class="lalina-payment-icon" title="Apple Pay">AP</span>
                <span class="lalina-payment-icon" title="Google Pay">GP</span>
                <span class="lalina-payment-icon" title="PayPal">PP</span>
            </div>
            <span class="lalina-footer-trust__secure">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                SSL Secured
            </span>
        </div>
    </div>
    <?php
}


// =========================================================
// 16. CUSTOM 404 PAGE — Product Recommendations
// =========================================================
// The actual 404 template lives in 404/404.php


// =========================================================
// 17. RECENTLY VIEWED PRODUCTS
// =========================================================
add_action( 'wp', 'lalina_track_recently_viewed' );
function lalina_track_recently_viewed() {
    if ( ! is_product() ) return;
    global $post;
    $viewed = isset( $_COOKIE['lalina_viewed'] ) ? json_decode( stripslashes( $_COOKIE['lalina_viewed'] ), true ) : array();
    if ( ! is_array( $viewed ) ) $viewed = array();
    $viewed = array_filter( $viewed, fn($id) => $id !== $post->ID );
    array_unshift( $viewed, $post->ID );
    $viewed = array_slice( $viewed, 0, 8 );
    setcookie( 'lalina_viewed', json_encode( $viewed ), time() + ( 7 * DAY_IN_SECONDS ), COOKIEPATH, COOKIE_DOMAIN );
}

add_action( 'woocommerce_after_single_product', 'lalina_recently_viewed_section' );
function lalina_recently_viewed_section() {
    $viewed = isset( $_COOKIE['lalina_viewed'] ) ? json_decode( stripslashes( $_COOKIE['lalina_viewed'] ), true ) : array();
    if ( ! is_array( $viewed ) || count( $viewed ) < 2 ) return;

    global $post;
    $viewed = array_filter( $viewed, fn($id) => $id !== $post->ID );
    if ( empty( $viewed ) ) return;

    $products = wc_get_products( array(
        'include' => array_slice( $viewed, 0, 4 ),
        'status'  => 'publish',
        'limit'   => 4,
    ));

    if ( empty( $products ) ) return;
    ?>
    <section class="lalina-recently-viewed">
        <div class="lalina-recently-viewed__inner">
            <h2>Recently Viewed</h2>
            <div class="lalina-recently-viewed__grid">
                <?php
                // Properly set up post/product globals so the template has full context
                foreach ( $products as $prod ) :
                    $GLOBALS['product'] = $prod;
                    setup_postdata( $GLOBALS['post'] = get_post( $prod->get_id() ) );
                    wc_get_template_part( 'content', 'product' );
                endforeach;
                wp_reset_postdata();
                ?>
            </div>
        </div>
    </section>
    <?php
}


// =========================================================
// 18. SCHEMA MARKUP — Organization (Homepage)
// =========================================================
add_action( 'wp_head', 'lalina_organization_schema' );
function lalina_organization_schema() {
    if ( ! is_front_page() ) return;
    $schema = array(
        '@context'  => 'https://schema.org',
        '@type'     => 'Organization',
        'name'      => 'Lalina Bags',
        'url'       => home_url('/'),
        'logo'      => array(
            '@type' => 'ImageObject',
            'url'   => esc_url( get_site_icon_url( 512 ) ?: get_template_directory_uri() . '/assets/logo.png' ),
        ),
        'description' => 'London-based premium vegan leather handbag brand. Cruelty-free, sustainably crafted luxury bags.',
        'foundingDate' => '2023',
        'address'   => array(
            '@type'           => 'PostalAddress',
            'addressLocality' => 'London',
            'addressCountry'  => 'GB',
        ),
        'sameAs' => array(
            'https://www.instagram.com/lalinabags',
            'https://www.facebook.com/lalinabags',
        ),
    );
    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
}


// =========================================================
// 19. WOO EMAILS — Custom Styling
// =========================================================
add_filter( 'woocommerce_email_styles', 'lalina_woo_email_styles' );
function lalina_woo_email_styles( $css ) {
    $custom = '
        body { background-color: #F5F0EB !important; }
        #wrapper { background-color: #F5F0EB !important; }
        #template_container { border: none !important; box-shadow: 0 4px 24px rgba(0,0,0,0.08) !important; }
        h1, h2, h3 { color: #1A1A1A !important; font-family: Georgia, serif !important; }
        a { color: #B5924C !important; }
        .button { background-color: #1A1A1A !important; border-radius: 0 !important; letter-spacing: 0.05em !important; text-transform: uppercase !important; }
        #header_wrapper { background-color: #1A1A1A !important; padding: 32px 48px !important; }
        #body_content_inner { padding: 40px 48px !important; }
    ';
    return $css . $custom;
}


// =========================================================
// 20. UI/UX — Product card second-image data attribute
// =========================================================
add_action( 'woocommerce_before_shop_loop_item', 'lalina_product_card_data', 5 );
function lalina_product_card_data() {
    global $product;
    if ( ! $product ) return;

    $gallery_ids = $product->get_gallery_image_ids();
    if ( empty( $gallery_ids ) ) return;

    $secondary_url = wp_get_attachment_image_url( $gallery_ids[0], 'woocommerce_thumbnail' );
    if ( ! $secondary_url ) return;

    echo '<script>
    (function(){
        var s = document.currentScript;
        if (!s) return;
        var li = s.closest("li.product");
        if (li) li.setAttribute("data-secondary-img", ' . wp_json_encode( $secondary_url ) . ');
    })();
    </script>';
}


// =========================================================
// 21. UI/UX — Scroll-reveal classes on WooCommerce sections
// =========================================================
add_filter( 'woocommerce_product_loop_start', function( $html ) {
    return str_replace( '<ul class="products', '<ul class="products lalina-reveal', $html );
});


// =========================================================
// 22. UI/UX — Product Schema (JSON-LD) on single product pages
// =========================================================
add_action( 'wp_head', 'lalina_product_schema' );
function lalina_product_schema() {
    if ( ! is_product() ) return;
    global $product;
    if ( ! $product ) return;

    $image_id  = $product->get_image_id();
    $image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';
    $rating    = $product->get_average_rating();
    $count     = $product->get_review_count();

    $schema = array(
        '@context'    => 'https://schema.org/',
        '@type'       => 'Product',
        'name'        => $product->get_name(),
        'description' => wp_strip_all_tags( $product->get_short_description() ?: $product->get_description() ),
        'sku'         => $product->get_sku(),
        'brand'       => array(
            '@type' => 'Brand',
            'name'  => 'Lalina Bags',
        ),
        'offers' => array(
            '@type'           => 'Offer',
            'url'             => get_permalink( $product->get_id() ),
            'priceCurrency'   => get_woocommerce_currency(),
            'price'           => $product->get_price(),
            'availability'    => $product->is_in_stock()
                ? 'https://schema.org/InStock'
                : 'https://schema.org/OutOfStock',
            'seller'          => array(
                '@type' => 'Organization',
                'name'  => 'Lalina Bags',
            ),
        ),
    );

    if ( $image_url ) {
        $schema['image'] = $image_url;
    }

    if ( $rating > 0 && $count > 0 ) {
        $schema['aggregateRating'] = array(
            '@type'       => 'AggregateRating',
            'ratingValue' => $rating,
            'reviewCount' => $count,
        );
    }

    echo '<script type="application/ld+json">' .
         wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) .
         '</script>' . "\n";
}


// =========================================================
// 23. UI/UX — Quick View Modal skeleton
// =========================================================
add_action( 'wp_footer', 'lalina_quick_view_modal', 15 );
function lalina_quick_view_modal() {
    if ( ! ( is_shop() || is_product_category() || is_product_tag() ) ) return;
    ?>
    <div id="lalina-qv-modal" class="lalina-modal lalina-qv-modal" aria-hidden="true" role="dialog" aria-labelledby="lalina-qv-title">
        <div class="lalina-modal-overlay" data-close-modal></div>
        <div class="lalina-modal-content">
            <button class="lalina-modal-close" data-close-modal aria-label="Close quick view">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
            <div class="lalina-qv-body" id="lalina-qv-body">
                <div class="lalina-qv-image lalina-skeleton lalina-skeleton-img" id="lalina-qv-img-wrap">
                    <img id="lalina-qv-img" src="" alt="" style="display:none;">
                </div>
                <div class="lalina-qv-details">
                    <p id="lalina-qv-cat" style="font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:var(--lalina-gold);margin:0 0 8px;"></p>
                    <h3 id="lalina-qv-title"></h3>
                    <div class="lalina-qv-price" id="lalina-qv-price"></div>
                    <p class="lalina-qv-desc" id="lalina-qv-desc"></p>
                    <a id="lalina-qv-atc-link" href="#" class="lalina-qv-atc">View Product</a>
                    <a id="lalina-qv-link" class="lalina-qv-link" href="#">See full details →</a>
                </div>
            </div>
        </div>
    </div>
    <?php
}


// =========================================================
// 24. UI/UX — AJAX endpoint for Quick View content
// =========================================================
add_action( 'wp_ajax_lalina_quick_view', 'lalina_quick_view_ajax' );
add_action( 'wp_ajax_nopriv_lalina_quick_view', 'lalina_quick_view_ajax' );
function lalina_quick_view_ajax() {
    check_ajax_referer( 'lalina_nonce', 'nonce' );

    $product_id = absint( $_POST['product_id'] ?? 0 );
    if ( ! $product_id ) {
        wp_send_json_error( array( 'message' => 'Invalid product.' ) );
    }

    $product = wc_get_product( $product_id );
    if ( ! $product ) {
        wp_send_json_error( array( 'message' => 'Product not found.' ) );
    }

    $image_url = wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_single' );
    $cats      = wp_get_post_terms( $product_id, 'product_cat', array( 'fields' => 'names' ) );

    wp_send_json_success( array(
        'id'          => $product_id,
        'name'        => $product->get_name(),
        'price_html'  => $product->get_price_html(),
        'description' => wp_strip_all_tags( $product->get_short_description() ?: $product->get_description() ),
        'permalink'   => get_permalink( $product_id ),
        'image'       => $image_url ?: '',
        'category'    => ! is_wp_error( $cats ) && $cats ? implode( ', ', $cats ) : '',
        'in_stock'    => $product->is_in_stock(),
        'atc_url'     => add_query_arg( 'add-to-cart', $product_id, wc_get_cart_url() ),
    ));
}


// =========================================================
// 25. UI/UX — Inject lalina-reveal on key sections
// =========================================================
add_action( 'woocommerce_after_shop_loop', function() {
    echo '<script>
    document.querySelectorAll(".woocommerce ul.products li.product")
      .forEach(function(el, i) {
        el.classList.add("lalina-reveal");
        el.setAttribute("data-delay", Math.min(i % 4 + 1, 4));
      });
    if (window.IntersectionObserver) {
      document.body.dispatchEvent(new CustomEvent("lalina_content_loaded"));
    }
    </script>';
}, 15);


// =========================================================
// 26. ADMIN — Order Bump Product Setting
// =========================================================
add_action( 'admin_menu', 'lalina_admin_menu' );
function lalina_admin_menu() {
    add_submenu_page(
        'woocommerce',
        'Lalina Settings',
        'Lalina Settings',
        'manage_options',
        'lalina-settings',
        'lalina_settings_page'
    );
}

function lalina_settings_page() {
    if ( isset( $_POST['lalina_save_settings'] ) && check_admin_referer( 'lalina_settings' ) ) {
        update_option( 'lalina_order_bump_product_id', absint( $_POST['lalina_order_bump_product_id'] ) );
        echo '<div class="notice notice-success"><p>Settings saved.</p></div>';
    }
    $bump_id = get_option( 'lalina_order_bump_product_id', 0 );
    ?>
    <div class="wrap">
        <h1>Lalina Bags — Custom Settings</h1>
        <form method="post">
            <?php wp_nonce_field( 'lalina_settings' ); ?>
            <table class="form-table">
                <tr>
                    <th><label for="lalina_order_bump_product_id">Order Bump Product ID</label></th>
                    <td>
                        <input type="number" name="lalina_order_bump_product_id" id="lalina_order_bump_product_id" value="<?php echo esc_attr( $bump_id ); ?>" class="regular-text">
                        <p class="description">Enter the Product ID of the item to show as an order bump at checkout (e.g., dust bag). Enter 0 to disable.</p>
                    </td>
                </tr>
            </table>
            <p><input type="submit" name="lalina_save_settings" class="button button-primary" value="Save Settings"></p>
        </form>
    </div>
    <?php
}
