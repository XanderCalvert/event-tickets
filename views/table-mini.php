<?php
/**
* Copy and paste this to events/table-mini.php in your template to customize
*/

// Don't load directly
if ( !defined('ABSPATH') ) { die('-1'); }

$tribe_ecp = Events_Calendar_Pro::instance(); 
$eventPosts = tribe_get_events();
$daysInMonth = isset($date) ? date("t", $date) : date("t");
$startOfWeek = get_option( 'start_of_week', 0 );
list( $year, $month ) = split( '-', $tribe_ecp->date );
$date = mktime(12, 0, 0, $month, 1, $year); // 1st day of month as unix stamp
$rawOffset = date("w", $date) - $startOfWeek;
$offset = ( $rawOffset < 0 ) ? $rawOffset + 7 : $rawOffset; // month begins on day x
$rows = 1;
$monthView = tribe_sort_by_month( $eventPosts, $tribe_ecp->date );


?>
<h4 class="cal-header"><?php echo $tribe_ecp->monthsShort[date('M',$date)]; echo date(' Y',$date); ?> <a class="sp-view-all-events" href="<?php echo tribe_get_events_link(); ?>"><?php _e('View all &raquo;', $tribe_ecp->pluginDomain); ?></a></h4>
<table class="tec-calendar tec-calendar-widget" id="small">
	<thead>
			<tr>
				<?php

				for( $n = $startOfWeek; $n < count($tribe_ecp->daysOfWeekMin) + $startOfWeek; $n++ ) {
					$dayOfWeek = ( $n >= 7 ) ? $n - 7 : $n;
					echo '<th id="tec-' . strtolower($tribe_ecp->daysOfWeekMin[$dayOfWeek]) . '" title="' . $tribe_ecp->daysOfWeek[$dayOfWeek] . '">' . $tribe_ecp->daysOfWeekMin[$dayOfWeek] . '</th>';
				}
				?>
			</tr>
	</thead>

	<tbody>
		<tr>
		<?php
			// skip last month
			for( $i = 1; $i <= $offset; $i++ ){ 
				echo "<td class='tec-othermonth'></td>";
			}
			// output this month
			for( $day = 1; $day <= date("t", $date); $day++ ) {
			    if( ($day + $offset - 1) % 7 == 0 && $day != 1) {
			        echo "</tr>\n\t<tr>";
			        $rows++;
			    }

				// Var'ng up days, months and years
				$current_day = date_i18n( 'd' );
				$current_month = date_i18n( 'm' );
				$current_year = date_i18n( 'Y' );
				
				if ( $current_month == $month && $current_year == $year) {
					// Past, Present, Future class
					if ($current_day == $day ) {
						$ppf = ' tec-present';
					} elseif ($current_day > $day) {
						$ppf = ' tec-past';
					} elseif ($current_day < $day) {
						$ppf = ' tec-future';
					}
				} elseif ( $current_month > $month && $current_year == $year || $current_year > $year ) {
					$ppf = ' tec-past';
				} elseif ( $current_month < $month && $current_year == $year || $current_year < $year ) {
					$ppf = ' tec-future';
				} else { $ppf = false; }
			    echo "<td class='tec-thismonth" . $ppf . "'>" . tribe_mini_display_day( $day, $monthView ) . "\n";
				echo "</td>";
			}
			// skip next month
			while( ($day + $offset) <= $rows * 7)
			{
			    echo "<td class='tec-othermonth'></td>";
			    $day++;
			}
		?>
		</tr>
	</tbody>
</table>
<?php

function tribe_mini_display_day( $day, $monthView ) {
	$return = "<div class='daynum tec-event' id='daynum_$day'>";

	$return .= ( count($monthView[$day]) ) ? "<a class='tec-mini-has-event'>$day</a>" : $day;
	$return .= "<div id='tooltip_day_$day' class='tec-tooltip' style='display:none;'>";
	for( $i = 0; $i < count( $monthView[$day] ); $i++ ) {
		$post = $monthView[$day][$i];
		setup_postdata( $post );
		$return .= '<h5 class="tec-event-title-mini"><a href="'. get_permalink($post->ID) .'">' . $post->post_title . '</a></h5>';
	}
	$return .= '<span class="tec-arrow"></span>';
	$return .= '</div>';

	$return .= "</div>";
	return $return;
}