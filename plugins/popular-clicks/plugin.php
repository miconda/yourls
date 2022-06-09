<?php
/*
Plugin Name: Popular Clicks
Plugin URI: hhttps://github.com/miconda/yourls
Description: Shows an admin page with the top of last clicked links
Version: 1.0
Author: miconda
Author URI: http://miconda.blogspot.com/
*/

yourls_add_action( 'plugins_loaded', 'popularclicks_add_page' );

function popularclicks_add_page() {
	yourls_register_plugin_page( 'popular_clicks', 'Popular Clicks', 'popularclicks_do_page' );
}
// Display popular clicks
function popularclicks_do_page() {
	$nonce = yourls_create_nonce('popular_clickks');
	echo '<h2>Popular Clicks</h2>';

	function show_top($numdays,$numrows) {
		global $ydb;
		$base  = YOURLS_SITE;
		$table_url = YOURLS_DB_TABLE_URL;
		$table_log = YOURLS_DB_TABLE_LOG;
		$outdata 	= '';

		/**
			SELECT a.shorturl AS shorturl, count(*) AS clicks, b.url AS longurl
			  FROM yourls_log a, yourls_url b WHERE a.shorturl=b.keyword AND DATE_SUB(NOW(),
			  INTERVAL 30 DAY)<a.click_time GROUP BY a.shorturl ORDER BY count(*) DESC LIMIT 20;
		 */

		$sql = "SELECT a.shorturl AS shorturl, count(*) AS clicks, b.url AS longurl FROM `$table_log` a, `$table_url` b WHERE a.shorturl=b.keyword AND DATE_SUB(NOW(), INTERVAL $numdays DAY)<a.click_time GROUP BY a.shorturl ORDER BY count(*) DESC LIMIT $numrows";
		$query = $ydb->fetchAll($sql);
	
		if ($query) {
			foreach( $query as $query_result ) {
                $outdata .= '<tr><td>' . $query_result['clicks'] . '</td><td><a href="' .$base .'/' . $query_result['shorturl'] .'+" target="blank">'
                    . $query_result['shorturl'] .'</a>'
					. '</td><td><a href="' . $query_result['longurl'] .'" target="blank">'
					. $query_result['longurl'] . '</td></tr>';
			}
		}
		echo '<h3><b>Popular Clicks in the Last '. $numdays . ' Days:</b></h3><br/>'
			. '<table><tr><th>Clicks</th><th>Short URL</th><th>Long URL</th></tr>' . $outdata . "</table><br>\n\r";
	}

	// update next lines for addjustments on number of days and number of top links
	// example: show_top(1,5) => print the 5 most popular links clicked in the last 1 day
	show_top(1,15);     // last day
	show_top(7,15);     // last week
	show_top(30,15);    // last ~month
	show_top(365,15);   // last ~year
	show_top(1000,15);  // ~alltime
}

