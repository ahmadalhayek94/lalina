<?php
/**
 * Lalina Weekly Revenue Report
 * Fires every Monday at 7am via WP-Cron.
 *
 * @package lalina-child
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_switch_theme', 'lalina_schedule_weekly_report' );
function lalina_schedule_weekly_report() {
    if ( ! wp_next_scheduled('lalina_weekly_report_event') ) {
        wp_schedule_event( strtotime('next monday 07:00:00'), 'weekly', 'lalina_weekly_report_event' );
    }
}

add_action( 'switch_theme', 'lalina_unschedule_weekly_report' );
function lalina_unschedule_weekly_report() {
    $ts = wp_next_scheduled('lalina_weekly_report_event');
    if ( $ts ) wp_unschedule_event( $ts, 'lalina_weekly_report_event' );
}

add_filter( 'cron_schedules', 'lalina_add_weekly_cron' );
function lalina_add_weekly_cron( $schedules ) {
    $schedules['weekly'] = [ 'interval' => 604800, 'display' => 'Once Weekly' ];
    return $schedules;
}

add_action( 'lalina_weekly_report_event', 'lalina_send_weekly_report' );
function lalina_send_weekly_report() {
    $now        = current_time('timestamp');
    $week_start = strtotime('last monday 00:00:00', $now);
    $week_end   = strtotime('last sunday 23:59:59', $now);
    $prev_start = $week_start - WEEK_IN_SECONDS;
    $prev_end   = $week_end   - WEEK_IN_SECONDS;
    $this_week  = lalina_get_week_stats( $week_start, $week_end );
    $prev_week  = lalina_get_week_stats( $prev_start, $prev_end );
    $subject    = 'Lalina Weekly Report — ' . date( 'j M Y', $week_start ) . ' to ' . date( 'j M Y', $week_end );
    wp_mail( get_option('admin_email'), $subject, lalina_weekly_report_html( $this_week, $prev_week, $week_start, $week_end ), [ 'Content-Type: text/html; charset=UTF-8', 'From: Lalina Reports <reports@lalinabags.com>' ] );
}

function lalina_get_week_stats( $start, $end ) {
    $query    = new WC_Order_Query([ 'date_created' => date('Y-m-d',$start).'...'.date('Y-m-d',$end), 'status' => ['wc-completed','wc-processing'], 'limit' => -1, 'return' => 'objects' ]);
    $orders   = $query->get_orders();
    $revenue  = 0; $products = [];
    foreach ( $orders as $order ) {
        $revenue += (float) $order->get_total();
        foreach ( $order->get_items() as $item ) {
            $pid = $item->get_product_id();
            if ( ! isset($products[$pid]) ) $products[$pid] = [ 'name' => $item->get_name(), 'qty' => 0, 'rev' => 0 ];
            $products[$pid]['qty'] += $item->get_quantity();
            $products[$pid]['rev'] += (float) $item->get_subtotal();
        }
    }
    $count = count($orders);
    uasort( $products, function($a,$b){ return $b['rev'] - $a['rev']; });
    return [ 'revenue' => $revenue, 'orders' => $count, 'aov' => $count > 0 ? $revenue/$count : 0, 'top5' => array_slice($products,0,5,true) ];
}

function lalina_pct_change( $new, $old ) {
    if ( $old == 0 ) return [ 'value' => 100, 'dir' => 'up' ];
    $pct = (($new-$old)/$old)*100;
    return [ 'value' => abs(round($pct,1)), 'dir' => $pct >= 0 ? 'up' : 'down' ];
}

function lalina_weekly_report_html( $tw, $pw, $start, $end ) {
    $sym = get_woocommerce_currency_symbol();
    $rc  = lalina_pct_change($tw['revenue'],$pw['revenue']);
    $oc  = lalina_pct_change($tw['orders'],$pw['orders']);
    $ac  = lalina_pct_change($tw['aov'],$pw['aov']);
    function lf( $v, $s ) { return $s.number_format($v,2); }
    function lc( $c ) { $col = $c['dir']==='up' ? '#2e7d32' : '#c62828'; $arr = $c['dir']==='up' ? '&#9650;' : '&#9660;'; return '<span style="color:'.$col.'">'.$arr.' '.$c['value'].'%</span>'; }
    ob_start(); ?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><style>
body{font-family:'Helvetica Neue',Arial,sans-serif;background:#F7F4EF;margin:0;padding:0;color:#4A4540;}
.wrap{max-width:640px;margin:0 auto;padding:40px 20px;}
.hdr{background:#1A1714;padding:32px 40px;text-align:center;}
.hdr h1{color:#B8975A;font-size:22px;font-weight:300;margin:0;letter-spacing:.05em;}
.hdr p{color:#D8C9B0;font-size:12px;margin:6px 0 0;}
.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:1px;background:#D8C9B0;margin-bottom:32px;}
.stat{background:#F7F4EF;padding:24px 20px;text-align:center;}
.sl{font-size:10px;font-weight:500;letter-spacing:.2em;text-transform:uppercase;color:#9a9087;margin-bottom:8px;}
.sv{font-size:26px;font-weight:300;color:#1A1714;margin-bottom:4px;}
.sc{font-size:12px;}
.tw{background:white;border:1px solid #D8C9B0;margin-bottom:32px;}
.tt{background:#1A1714;color:#B8975A;font-size:10px;font-weight:500;letter-spacing:.2em;text-transform:uppercase;padding:12px 20px;}
table{width:100%;border-collapse:collapse;}
th{font-size:10px;letter-spacing:.15em;text-transform:uppercase;color:#9a9087;padding:10px 20px;text-align:left;border-bottom:1px solid #D8C9B0;background:#faf9f7;}
td{padding:12px 20px;font-size:13px;color:#4A4540;border-bottom:1px solid #f0ece6;}
tr:last-child td{border-bottom:none;}
.ft{text-align:center;padding:24px;font-size:11px;color:#9a9087;}
</style></head><body><div class="wrap">
<div class="hdr"><h1>Lalina Bags — Weekly Report</h1><p><?php echo date('j M Y',$start); ?> – <?php echo date('j M Y',$end); ?></p></div>
<div class="stats">
<div class="stat"><div class="sl">Revenue</div><div class="sv"><?php echo lf($tw['revenue'],$sym); ?></div><div class="sc"><?php echo lc($rc); ?> vs last week</div></div>
<div class="stat"><div class="sl">Orders</div><div class="sv"><?php echo (int)$tw['orders']; ?></div><div class="sc"><?php echo lc($oc); ?> vs last week</div></div>
<div class="stat"><div class="sl">Avg Order</div><div class="sv"><?php echo lf($tw['aov'],$sym); ?></div><div class="sc"><?php echo lc($ac); ?> vs last week</div></div>
</div>
<div class="tw"><div class="tt">Top 5 Products</div>
<table><thead><tr><th>#</th><th>Product</th><th>Units</th><th>Revenue</th></tr></thead><tbody>
<?php $i=1; foreach($tw['top5'] as $p): ?>
<tr><td><?php echo $i++; ?></td><td><?php echo esc_html($p['name']); ?></td><td><?php echo (int)$p['qty']; ?></td><td><?php echo lf($p['rev'],$sym); ?></td></tr>
<?php endforeach; ?>
<?php if(empty($tw['top5'])): ?><tr><td colspan="4" style="text-align:center;color:#9a9087;">No orders this week.</td></tr><?php endif; ?>
</tbody></table></div>
<div class="ft">Lalina Bags &mdash; Where Craft Meets Confidence.<br>Sent automatically every Monday at 7am.</div>
</div></body></html>
<?php return ob_get_clean();
}
