<?php
/**
 * The list table for the All Tickets screen.
 *
 * @since  TBD
 *
 * @package TEC\Tickets\Admin
 */

namespace TEC\Tickets\Admin\All_Tickets;

use Tribe__Tickets__Tickets;
use Tribe__Tickets__Ticket_Object;
use WP_List_Table;
use DateTime;
use Tribe__Template;
use WP_Query;

/**
 * Class List_Table.
 *
 * @since  TBD
 *
 * @package TEC\Tickets\Admin
 */
class List_Table extends WP_List_Table {
	/**
	 * The user option that will store how many attendees should be shown per page.
	 *
	 * @var string
	 */
	protected $per_page_option;


	/**
	 * The template object.
	 *
	 * @var Tribe__Template
	 */
	protected $template;

	/**
	 * The provider filter query key.
	 *
	 * @var string
	 */
	public static $provider_key = 'provider-filter';

	/**
	 * The status filter query key.
	 *
	 * @var string
	 */
	public static $status_key = 'status-filter';

	/**
	 * Default status filter.
	 *
	 * @var string
	 */
	public static $default_status = 'active';

	/**
	 * Default Sort By.
	 *
	 * @var string
	 */
	public static $default_sort_by = 'end';

	/**
	 * Default Sort Order.
	 *
	 * @var string
	 */
	public static $default_sort_order = 'desc';

	/**
	 * The query for the tickets.
	 *
	 * @var WP_Query $query
	 */
	protected $query;

	/**
	 * Get the template object.
	 *
	 * @since TBD
	 *
	 * @return Tribe__Template
	 */
	protected function get_template() {
		if ( ! empty( $this->template ) ) {
			return $this->template;
		}

		$this->template = tribe( 'tickets.admin.views' );

		return $this->template;
	}

	/**
	 * The constructor.
	 *
	 * @since  TBD
	 */
	public function __construct() {
		$screen = get_current_screen();

		parent::__construct(
			[
				'singular' => 'ticket',
				'plural'   => 'tickets',
				'ajax'     => false,
				'screen'   => $screen,
			]
		);

		$this->per_page_option = Screen_Options::$per_page_user_option;

		if ( ! is_null( $screen ) ) {
			$screen->add_option(
				'per_page',
				[
					'label'  => __( 'Number of tickets per page:', 'event-tickets' ),
					'option' => $this->per_page_option,
				]
			);
		}
	}

	/**
	 * Returns the columns for the list table.
	 *
	 * @since  TBD
	 *
	 * @return array
	 */
	public function get_table_columns(): array {
		$table_columns = [
			'name'      => esc_html__( 'Ticket Name', 'event-tickets' ),
			'id'        => esc_html__( 'Ticket ID', 'event-tickets' ),
			'event'     => esc_html__( 'Event', 'event-tickets' ),
			'start'     => esc_html__( 'Sale Starts', 'event-tickets' ),
			'end'       => esc_html__( 'Sale Ends', 'event-tickets' ),
			'days_left' => esc_html__( 'Days Remaining', 'event-tickets' ),
			'price'     => esc_html__( 'Price', 'event-tickets' ),
			'sold'      => esc_html__( 'Sold', 'event-tickets' ),
			'remaining' => esc_html__( 'Remaining', 'event-tickets' ),
			'sales'     => esc_html__( 'Sales', 'event-tickets' ),
		];

		/**
		 * Filters the columns for the All Tickets Table.
		 *
		 * @since TBD
		 *
		 * @param array $table_columns The columns for the All Tickets Table.
		 *
		 * @return array
		 */
		return apply_filters( 'tec_tickets_all_tickets_table_columns', $table_columns );
	}

	/**
	 * Get a list of columns. The format is:
	 * 'internal-name' => 'Title'
	 *
	 * @since  TBD
	 *
	 * @return array
	 */
	public function get_columns(): array {
		return $this->get_table_columns();
	}

	/**
	 * Get primary column for the list table.
	 *
	 * @since TBD
	 *
	 * @return string
	 */
	protected function get_primary_column_name(): string {
		return 'name';
	}

	/**
	 * Get hidden columns for the list table.
	 *
	 * @since TBD
	 *
	 * @return array
	 */
	public function get_hidden_columns(): array {
		$screen = get_current_screen();

		if ( is_null( $screen ) ) {
			return $this->get_default_hidden_columns();
		}

		return get_hidden_columns( $screen );
	}

	/**
	 * Returns the columns for the list table.
	 *
	 * @since  TBD
	 *
	 * @return array
	 */
	public static function get_default_hidden_columns(): array {
		$default_hidden_columns = [
			'id',
			'start',
			'days_left',
			'sales',
		];

		/**
		 * Filter the default hidden columns for the All Tickets Table.
		 *
		 * @since TBD
		 *
		 * @param array $default_hidden_columns The default hidden columns for the All Tickets Table.
		 *
		 * @return array
		 */
		return apply_filters( 'tec_tickets_all_tickets_table_default_hidden_columns', $default_hidden_columns );
	}

	/**
	 * Returns the sortable columns for the list table.
	 *
	 * @since  TBD
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = [
			'name'      => [ 'name', true ],
			'id'        => [ 'id', true ],
			'start'     => [ 'start', true ],
			'end'       => [ 'end', 'desc' ], // Start with DESC order.
			'days_left' => [ 'days_left', true ],
			'price'     => [ 'price', true ],
			'sold'      => [ 'sold', true ],
		];

		/**
		 * Filters the sortable columns for the All Tickets Table.
		 *
		 * @since TBD
		 *
		 * @param array $sortable_columns The sortable columns for the All Tickets Table.
		 *
		 * @return array
		 */
		return apply_filters( 'tec_tickets_all_tickets_table_sortable_columns', $sortable_columns );
	}

	/**
	 * Get the ticket type icon.
	 *
	 * @since TBD
	 *
	 * @param Tribe__Tickets__Ticket_Object $item The current item.
	 *
	 * @return string HTML for the ticket type icon.
	 */
	protected function get_ticket_type_icon( $item ) {
		ob_start();
		do_action( 'tec_tickets_editor_list_table_title_icon_' . $item->type() );
		return ob_get_clean();
	}

	/**
	 * Get the default column value.
	 *
	 * @since TBD
	 *
	 * @param Tribe__Tickets__Ticket_Object $item        The current item.
	 * @param string                        $column_name The column name.
	 *
	 * @return string
	 */
	public function column_default( $item, $column_name ): string {
		// If the column name is empty or the item does not have the column name, return an empty string.
		if ( empty( $column_name ) || ! isset( $item->$column_name ) ) {
			return '';
		}

		// If value is not a string or a number, return an empty string.
		if ( ! is_string( $item->$column_name ) && ! is_numeric( $item->$column_name ) ) {
			return '';
		}

		return esc_html( $item->$column_name );
	}

	/**
	 * Get the column name value.
	 *
	 * @since TBD
	 *
	 * @param Tribe__Tickets__Ticket_Object $item The current item.
	 *
	 * @return string
	 */
	public function column_name( $item ): string {
		$event = $item->get_event();
		if ( ! $event ) {
			return '-';
		}

		$edit_post_url  = get_edit_post_link( $event );
		$edit_post_link = sprintf(
			'<a href="%s" class="tec-tickets-all-tickets-table-event-link" target="_blank" rel="nofollow noopener">%s</a>',
			esc_url( $edit_post_url ),
			esc_html( $item->name )
		);

		$template = $this->get_template();
		$context  = [
			'icon_html'   => $this->get_ticket_type_icon( $item ),
			'ticket_link' => $edit_post_link,
		];

		return $template->template( 'all-tickets/table-column-name', $context, false );
	}

	/**
	 * Get the column ID value.
	 *
	 * @since TBD
	 *
	 * @param Tribe__Tickets__Ticket_Object $item The current item.
	 *
	 * @return string
	 */
	public function column_id( $item ): string {
		return (string) $item->ID;
	}

	/**
	 * Get the column event value.
	 *
	 * @since TBD
	 *
	 * @param Tribe__Tickets__Ticket_Object $item The current item.
	 *
	 * @return string
	 */
	public function column_event( $item ): string {
		$event = $item->get_event();
		if ( ! $event ) {
			return '-';
		}

		$edit_post_url  = get_edit_post_link( $event );
		$edit_post_link = sprintf(
			'<a href="%s" class="tec-tickets-all-tickets-table-event-link" target="_blank" rel="nofollow noopener">%s</a>',
			esc_url( $edit_post_url ),
			get_the_title( $event )
		);

		$orders_report_url  = add_query_arg(
			[
				'post_type' => $event->post_type,
				'page'      => 'tickets-orders',
				'event_id'  => $event->ID,
			],
			admin_url( 'edit.php' )
		);
		$orders_report_link = sprintf(
			'<a href="%s" class="tec-tickets-all-tickets-table-event-link" target="_blank" rel="nofollow noopener">%s</a>',
			esc_url( $orders_report_url ),
			esc_html__( 'Orders', 'event-tickets' )
		);

		$attendees_report_url  = add_query_arg(
			[
				'post_type' => $event->post_type,
				'page'      => 'tickets-attendees',
				'event_id'  => $event->ID,
			],
			admin_url( 'edit.php' )
		);
		$attendees_report_link = sprintf(
			'<a href="%s" class="tec-tickets-all-tickets-table-event-link" target="_blank" rel="nofollow noopener">%s</a>',
			esc_url( $attendees_report_url ),
			esc_html__( 'Attendees', 'event-tickets' )
		);

		$actions = [
			'orders'    => $orders_report_link,
			'attendees' => $attendees_report_link,
		];

		/**
		 * Filters the actions for the event in the All Tickets Table.
		 *
		 * @since TBD
		 *
		 * @param array $actions The actions for the event in the All Tickets Table.
		 */
		$actions = apply_filters( 'tec_tickets_all_tickets_table_event_actions', $actions, $event, $item );

		return sprintf( '%1$s %2$s', $edit_post_link, $this->row_actions( $actions ) );
	}

	/**
	 * Get the column start date value.
	 *
	 * @since TBD
	 *
	 * @param Tribe__Tickets__Ticket_Object $item The current item.
	 *
	 * @return string
	 */
	public function column_start( $item ): string {
		$date_format = tribe_get_date_format( true );
		$ts          = $item->start_date();

		return sprintf(
			'<time datetime="%1$s" title="%2$s">%3$s</time>',
			esc_attr( \Tribe__Date_Utils::reformat( $ts, 'c' ) ),
			esc_html( \Tribe__Date_Utils::reformat( $ts, get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ) ),
			esc_html( \Tribe__Date_Utils::reformat( $ts, $date_format ) )
		);
	}

	/**
	 * Get the column end date value.
	 *
	 * @since TBD
	 *
	 * @param Tribe__Tickets__Ticket_Object $item The current item.
	 *
	 * @return string
	 */
	public function column_end( $item ): string {
		$date_format = tribe_get_date_format( true );
		$ts          = $item->end_date();

		return sprintf(
			'<time datetime="%1$s" title="%2$s">%3$s</time>',
			esc_attr( \Tribe__Date_Utils::reformat( $ts, 'c' ) ),
			esc_html( \Tribe__Date_Utils::reformat( $ts, get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ) ),
			esc_html( \Tribe__Date_Utils::reformat( $ts, $date_format ) )
		);
	}

	/**
	 * Get the column days_left value.
	 *
	 * @since TBD
	 *
	 * @param Tribe__Tickets__Ticket_Object $item The current item.
	 *
	 * @return string
	 */
	public function column_days_left( $item ): string {
		$datetime = $item->end_date( false );
		$now      = new DateTime();
		$interval = $now->diff( $datetime );

		if ( $interval->invert ) {
			return '-';
		}

		return (string) $interval->days;
	}

	/**
	 * Get the column price value.
	 *
	 * @since TBD
	 *
	 * @param Tribe__Tickets__Ticket_Object $item The current item.
	 *
	 * @return string
	 */
	public function column_price( $item ): string {
		return tribe_format_currency( number_format( $item->price, 2 ), $item->ID );
	}

	/**
	 * Get the column sold value.
	 *
	 * @since TBD
	 *
	 * @param Tribe__Tickets__Ticket_Object $item The current item.
	 *
	 * @return string
	 */
	public function column_sold( $item ): string {
		return (string) $item->qty_sold();
	}

	/**
	 * Get the column remaining value.
	 *
	 * @since TBD
	 *
	 * @param Tribe__Tickets__Ticket_Object $item The current item.
	 *
	 * @return string
	 */
	public function column_remaining( $item ): string {
		$available = $item->available();
		return $available < 0 ? '-' : (string) $available;
	}

	/**
	 * Get the column sales value.
	 *
	 * @since TBD
	 *
	 * @param Tribe__Tickets__Ticket_Object $item The current item.
	 *
	 * @return string
	 */
	public function column_sales( $item ): string {
		return tribe_format_currency( number_format( $item->qty_sold() * $item->price, 2 ), $item->ID );
	}

	/**
	 * Modify the sort arguments.
	 *
	 * @since TBD
	 *
	 * @param array $args The arguments used to query the tickets for the All Tickets Table.
	 *
	 * @return array
	 */
	public function modify_sort_args( $args ): array {
		$orderby = tribe_get_request_var( 'orderby', self::$default_sort_by );
		switch ( $orderby ) {
			case 'name':
				$args['orderby'] = 'post_title';
				break;
			case 'id':
				$args['orderby'] = 'ID';
				break;
			case 'start':
				$args['orderby']   = 'meta_value';
				$args['meta_key']  = '_ticket_start_date';
				$args['meta_type'] = 'DATE';
				break;
			case 'end':
				$args['orderby']   = 'meta_value';
				$args['meta_key']  = '_ticket_end_date';
				$args['meta_type'] = 'DATE';
				break;
			case 'days_left':
				$args['orderby']   = 'meta_value';
				$args['meta_key']  = '_ticket_end_date';
				$args['meta_type'] = 'DATE';
				break;
			case 'price':
				$args['orderby']  = 'meta_value_num';
				$args['meta_key'] = '_price';
				break;
			case 'sold':
				$args['orderby']  = 'meta_value_num';
				$args['meta_key'] = 'total_sales';
				break;
		}

		$args['order'] = tribe_get_request_var( 'order', self::$default_sort_order );

		return $args;
	}

	/**
	 * Modify the filter arguments.
	 *
	 * @since TBD
	 *
	 * @param array $args The arguments used to query the tickets for the All Tickets Table.
	 *
	 * @return array
	 */
	public function modify_filter_args( $args ) {
		$filter = tribe_get_request_var( self::$status_key, self::$default_status );

		if ( 'all' === $filter ) {
			return $args;
		}

		switch ( $filter ) {
			case 'active':
				$args['meta_query'][] = [
					'key'     => '_ticket_start_date',
					'value'   => current_time( 'mysql' ),
					'compare' => '<=',
					'type'    => 'DATETIME',
				];
				$args['meta_query'][] = [
					'key'     => '_ticket_end_date',
					'value'   => current_time( 'mysql' ),
					'compare' => '>',
					'type'    => 'DATETIME',
				];
				break;
			case 'past':
				$args['meta_query'][] = [
					'key'     => '_ticket_end_date',
					'value'   => current_time( 'mysql' ),
					'compare' => '<=',
					'type'    => 'DATETIME',
				];
				break;
			case 'upcoming':
				$args['meta_query'][] = [
					'key'     => '_ticket_start_date',
					'value'   => current_time( 'mysql' ),
					'compare' => '>',
					'type'    => 'DATETIME',
				];
				break;
			case 'discounted':
				$args['meta_query'][] = [
					'key'     => '_sale_price',
					'compare' => 'EXISTS',
				];
				$args['meta_query'][] = [
					'key'     => '_sale_price_start_date',
					'value'   => current_time( 'mysql' ),
					'compare' => '<',
					'type'    => 'DATETIME',
				];
				$args['meta_query'][] = [
					'key'     => '_sale_price_end_date',
					'value'   => current_time( 'mysql' ),
					'compare' => '>',
					'type'    => 'DATETIME',
				];
				break;
		}

		return $args;
	}

	/**
	 * Get the query args for the list table.
	 *
	 * @since TBD
	 *
	 * @return array|bool
	 */
	public function get_query_args() {
		$current_page = $this->get_pagenum();
		$per_page     = $this->get_items_per_page( $this->per_page_option );

		$provider_options = $this->get_provider_options();
		$default_provider = empty( $provider_options ) ? '' : key( $provider_options );
		$current_provider = tribe_get_request_var( self::$provider_key, $default_provider );

		$args = [
			'all_tickets_list_table' => true,
			'offset'                 => ( $current_page - 1 ) * $per_page,
			'posts_per_page'         => $per_page,
			'return_total_found'     => true,
			'post_type'              => $current_provider,
		];

		$args = $this->modify_filter_args( $args );
		$args = $this->modify_sort_args( $args );

		if ( isset( $args['meta_query'] ) && count( $args['meta_query'] ) > 1 ) {
			$args['meta_query']['relation'] = 'AND';
		}

		/**
		 * Filters the arguments used to query the tickets for the All Tickets Table.
		 *
		 * @since TBD
		 *
		 * @param array $args The arguments used to query the tickets for the All Tickets Table.
		 *
		 * @return array
		 */
		return apply_filters( 'tec_tickets_all_tickets_table_query_args', $args );
	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 * @since TBD
	 */
	public function prepare_items() {

		add_filter( 'posts_clauses', [ $this, 'filter_query_clauses' ], 10, 2 );

		$args        = $this->get_query_args();
		$this->query = new WP_Query( $args );
		$total_items = $this->query->found_posts;
		$items       = $this->query->posts;

		remove_filter( 'posts_clauses', [ $this, 'filter_query_clauses' ], 10 );

		foreach ( $items as $i => $item ) {
			$ticket = Tribe__Tickets__Tickets::load_ticket_object( $item->ID );
			if ( ! empty( $ticket ) ) {
				$ticket->raw_data = $item;
			}
			$this->items[] = $ticket;
		}

		$pagination_args = [
			'total_items' => $total_items,
			'per_page'    => $this->get_items_per_page( $this->per_page_option ),
		];

		$this->_column_headers = [
			$this->get_table_columns(),
			$this->get_hidden_columns(),
			$this->get_sortable_columns(),
		];

		$this->set_pagination_args( $pagination_args );
	}

	/**
	 * Filter the query clauses.
	 *
	 * @since TBD
	 *
	 * @param array    $clauses The query clauses.
	 * @param WP_Query $query   The WP_Query object.
	 *
	 * @return array
	 */
	public function filter_query_clauses( $clauses, $query ) {
		// Only modify if not the main query and is the All Tickets Table query.
		if ( $query->is_main_query() || empty( $query->query_vars['all_tickets_list_table'] ) ) {
			return $clauses;
		}

		global $wpdb;

		$event_meta_key = $this->get_event_meta_key();

		// Add join clauses to retrieve the event title.
		$clauses['join'] .= $wpdb->prepare(
			" LEFT JOIN {$wpdb->postmeta} AS ticket_event ON ( {$wpdb->posts}.ID = ticket_event.post_id ) AND ticket_event.meta_key = '%s' ",
			$event_meta_key
		);
		$clauses['join'] .= " LEFT JOIN {$wpdb->posts} AS event_data ON event_data.ID = ticket_event.meta_value ";

		// Add the event title to the fields.
		$clauses['fields'] .= ", event_data.post_title AS event_title";

		// Add the search clause.
		$search = tribe_get_request_var( 's' );
		if ( ! empty( $search ) ) {
			$clauses['where'] .= $wpdb->prepare(
				" AND ( {$wpdb->posts}.post_title LIKE %s OR event_data.post_title LIKE %s )",
				'%' . $wpdb->esc_like( $search ) . '%',
				'%' . $wpdb->esc_like( $search ) . '%'
			);
		}

		return $clauses;
	}

	/**
	 * Get event meta key.
	 *
	 * @since TBD
	 *
	 * @return string
	 */
	protected function get_event_meta_key(): string {
		/**
		 * Filters the event meta keys for the All Tickets Table.
		 *
		 * @since TBD
		 *
		 * @param array $event_meta_keys The event meta keys for the All Tickets Table.
		 *
		 * @return array
		 */
		$event_meta_keys = apply_filters( 'tec_tickets_all_tickets_table_event_meta_keys', [] );

		$provider_options = $this->get_provider_options();
		$default_provider = empty( $provider_options ) ? '' : key( $provider_options );
		$current_provider = tribe_get_request_var( self::$provider_key, $default_provider );

		if ( ! isset( $event_meta_keys[ $current_provider ] ) ) {
			return '';
		}
		return $event_meta_keys[ $current_provider ];
	}

	/**
	 * Display the filter and search input.
	 *
	 * @since TBD
	 *
	 * @param string $which The location of the extra table nav.
	 */
	public function extra_tablenav( $which ) {
		if ( 'top' !== $which ) {
			return;
		}

		$provider_options = $this->get_provider_options();
		$default_provider = empty( $provider_options ) ? '' : key( $provider_options );
		$current_provider = tribe_get_request_var( self::$provider_key, $default_provider );

		$current_status = tribe_get_request_var( 'ticket-filter', self::$default_status );

		$template = $this->get_template();
		$context  = [
			'list_table'           => $this,
			'status_options'       => $this->get_status_options(),
			'current_status'       => $current_status,
			'search_id'            => 'tec-tickets-all-tickets-search-input',
			'search_value'         => tribe_get_request_var( 's' ),
			'show_provider_filter' => $this->show_ticket_provider_filter(),
			'provider_options'     => $provider_options,
			'current_provider'     => $current_provider,
		];

		$template->template( 'all-tickets/filters', $context );
	}

	/**
	 * Get the default status options.
	 *
	 * @since TBD
	 *
	 * @return array
	 */
	protected function get_status_options(): array {
		$status_options = [
			'active'     => esc_html__( 'Active Tickets', 'event-tickets' ),
			'past'       => esc_html__( 'Past Tickets', 'event-tickets' ),
			'upcoming'   => esc_html__( 'Upcoming Tickets', 'event-tickets' ),
			'discounted' => esc_html__( 'Discounted Tickets', 'event-tickets' ),
			'all'        => esc_html__( 'All Tickets', 'event-tickets' ),
		];

		/**
		 * Filters the status options for the All Tickets Table.
		 *
		 * @since TBD
		 *
		 * @param array $status_options The status options for the All Tickets Table.
		 *
		 * @return array
		 */
		return apply_filters( 'tec_tickets_all_tickets_table_status_options', $status_options );
	}

	/**
	 * Get the ticket providers.
	 *
	 * @since TBD
	 *
	 * @return array
	 */
	protected function get_provider_options() {
		/**
		 * Filters the ticket providers for the All Tickets Table.
		 *
		 * @since TBD
		 *
		 * @param array $providers The ticket providers for the All Tickets Table.
		 *
		 * @return array
		 */
		return apply_filters( 'tec_tickets_all_tickets_table_provider_options', [] );
	}

	/**
	 * Whether or not to display the ticket provider filter.
	 *
	 * @since TBD
	 *
	 * @return boolean
	 */
	protected function show_ticket_provider_filter(): bool {
		$providers = $this->get_provider_options();

		// Only show if more than one provider.
		return count( $providers ) > 1;
	}
}
