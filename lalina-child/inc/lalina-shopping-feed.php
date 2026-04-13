<?php
/**
 * Lalina Google Shopping XML Feed
 * Accessible at: lalinabags.com?lalina_feed=1
 * One item per colour variation. Cached 6 hours.
 *
 * @package lalina-child
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'lalina_shopping_feed_init' );

function lalina_shopping_feed_init() {
    if ( empty( $_GET['lalina_feed'] ) ) return;
    $xml = lalina_get_shopping_feed();
    header( 'Content-Type: application/xml; charset=utf-8' );
    header( 'Cache-Control: public, max-age=21600' );
    echo $xml;
    exit;
}

function lalina_get_shopping_feed() {
    $cache_key = 'lalina_shopping_feed_v1';
    $cached    = get_transient( $cache_key );
    if ( $cached ) return $cached;
    $xml = lalina_build_shopping_feed();
    set_transient( $cache_key, $xml, 6 * HOUR_IN_SECONDS );
    return $xml;
}

add_action( 'save_post_product', function() {
    delete_transient( 'lalina_shopping_feed_v1' );
});

function lalina_build_shopping_feed() {
    $shop_url = get_permalink( wc_get_page_id('shop') );
    $currency = get_woocommerce_currency();
    $products = wc_get_products([ 'status' => 'publish', 'type' => [ 'simple', 'variable' ], 'limit' => -1 ]);

    $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">' . "\n";
    $xml .= '  <channel>' . "\n";
    $xml .= '    <title>Lalina Bags</title>' . "\n";
    $xml .= '    <link>' . esc_url( $shop_url ) . '</link>' . "\n";
    $xml .= '    <description>Genuine leather handbags — Where Craft Meets Confidence.</description>' . "\n\n";

    foreach ( $products as $product ) {
        if ( $product->is_type('variable') ) {
            foreach ( $product->get_available_variations() as $vdata ) {
                $v = wc_get_product( $vdata['variation_id'] );
                if ( ! $v || ! $v->is_purchasable() ) continue;
                $colour = '';
                foreach ( $v->get_variation_attributes() as $key => $val ) {
                    if ( stripos( $key, 'color' ) !== false || stripos( $key, 'colour' ) !== false ) { $colour = $val; break; }
                }
                $xml .= lalina_build_feed_item( $product->get_id() . '_' . $v->get_id(), $product->get_name() . ( $colour ? ' — ' . $colour : '' ), $product, $v, $currency, $colour );
            }
        } else {
            $xml .= lalina_build_feed_item( $product->get_id(), $product->get_name(), $product, $product, $currency, '' );
        }
    }

    $xml .= '  </channel>' . "\n" . '</rss>';
    return $xml;
}

function lalina_build_feed_item( $id, $title, $product, $variant, $currency, $colour ) {
    $link         = get_permalink( $product->get_id() );
    $desc         = wp_strip_all_tags( $product->get_description() ?: $product->get_short_description() );
    $price        = number_format( (float) $variant->get_price(), 2, '.', '' );
    $image_id     = $variant->get_image_id() ?: $product->get_image_id();
    $image_url    = $image_id ? wp_get_attachment_url( $image_id ) : '';
    $availability = $variant->is_in_stock() ? 'in stock' : 'out of stock';
    $categories   = wp_get_post_terms( $product->get_id(), 'product_cat', [ 'fields' => 'names' ] );
    $cat_string   = implode( ' > ', (array) $categories );

    $out  = "    <item>\n";
    $out .= "      <g:id>" . htmlspecialchars( (string) $id, ENT_XML1 | ENT_QUOTES, 'UTF-8' ) . "</g:id>\n";
    $out .= "      <g:title><![CDATA[" . $title . "]]></g:title>\n";
    $out .= "      <g:description><![CDATA[" . mb_substr( $desc, 0, 5000 ) . "]]></g:description>\n";
    $out .= "      <g:link>" . esc_url( $link ) . "</g:link>\n";
    if ( $image_url ) $out .= "      <g:image_link>" . esc_url( $image_url ) . "</g:image_link>\n";
    $out .= "      <g:price>" . $price . " " . $currency . "</g:price>\n";
    $out .= "      <g:availability>" . $availability . "</g:availability>\n";
    $out .= "      <g:brand>Lalina Bags</g:brand>\n";
    $out .= "      <g:condition>new</g:condition>\n";
    if ( $cat_string ) $out .= "      <g:product_type><![CDATA[" . $cat_string . "]]></g:product_type>\n";
    if ( $colour )     $out .= "      <g:color><![CDATA[" . $colour . "]]></g:color>\n";
    $out .= "      <g:material>Leather</g:material>\n";
    $out .= "      <g:gender>female</g:gender>\n";
    $out .= "      <g:age_group>adult</g:age_group>\n";
    $out .= "    </item>\n";
    return $out;
}
