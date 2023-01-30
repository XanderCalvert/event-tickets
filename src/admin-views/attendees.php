<?php
/**
 * Attendees template.
 *
 * @var Tribe_Template            $this      Current template object.
 * @var int                       $event_id  The event/post/page id.
 * @var Tribe__Tickets__Attendees $attendees The Attendees object.
 */

?>
<div class="wrap tribe-report-page">
	<?php
	$this->template( 'attendees/attendees' );
	$this->template( 'attendees/attendees-event' );
	?>
</div>
