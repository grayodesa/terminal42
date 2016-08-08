<?php
/*
 * IMPORTANT: READ THE LICENSE AGREEMENT CAREFULLY.
 *
 * BY INSTALLING, COPYING, RUNNING, OR OTHERWISE USING THE 
 * WORDPRESS SOCIAL SHARING OPTIMIZATION (WPSSO) PRO APPLICATION, YOU AGREE 
 * TO BE BOUND BY THE TERMS OF ITS LICENSE AGREEMENT.
 * 
 * License: Nontransferable License for a WordPress Site Address URL
 * License URI: http://surniaulula.com/wp-content/plugins/wpsso/license/pro.txt
 *
 * IF YOU DO NOT AGREE TO THE TERMS OF ITS LICENSE AGREEMENT,
 * PLEASE DO NOT INSTALL, RUN, COPY, OR OTHERWISE USE THE
 * WORDPRESS SOCIAL SHARING OPTIMIZATION (WPSSO) PRO APPLICATION.
 * 
 * Copyright 2012-2016 Jean-Sebastien Morisset (http://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'WpssoProEventTribeEvents' ) ) {

	class WpssoProEventTribeEvents {

		private $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;

			$this->p->util->add_plugin_filters( $this, array(
				'get_event_options' => 3,		// $opts, $mod, $event_id
				'get_person_options' => 3,		// $opts, $mod, $person_id
				'get_place_options' => 3,		// $opts, $mod, $place_id
				'schema_meta_itemprop' => 2,		// $mt_schema, $mod
			) );

			if ( ! empty( $this->p->is_avail['json'] ) )
				add_filter( 'tribe_json_ld_event_data', '__return_empty_array', 1000 );
		}

		public function filter_get_event_options( $opts, $mod, $event_id ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			if ( $opts !== false )	// first come, first served
				return $opts;
			elseif ( preg_match( '/^tribe_events-([0-9]+)$/', $event_id, $match ) )	// specific event
				$mod = $this->p->m['util']['post']->get_mod( $match[1] );
			elseif ( $mod['post_type'] !== 'tribe_events' )				// current post
				return $opts;

			$organizer_ids = array();
			$venue_id = null;

			foreach ( (array) tribe_get_organizer_ids( $mod['id'] ) as $id )
				if ( ! empty( $id ) )
					$organizer_ids[] = 'tribe_organizer-'.$id;

			$venue_id = tribe_get_venue_id( $mod['id'] );
			if ( ! empty( $venue_id ) )
				$venue_id = 'tribe_venue-'.$venue_id;

			$opts = array(
				'event_type' => $mod['obj']->get_options( $mod['id'], 'schema_type' ),
				'event_start_date' => date( 'c', Tribe__Events__Timezones::event_start_timestamp( $mod['id'], 'UTC' ) ),
				'event_end_date' => date( 'c', Tribe__Events__Timezones::event_end_timestamp( $mod['id'], 'UTC' ) ),
				'event_organizer_person_id' => reset( $organizer_ids ),
				'event_place_id' => $venue_id,
				'event_offers' => array(	// array of arrays
					array(
						'offer_price' => tribe_get_cost( $mod['id'] ),
					),
				),
			);

			return $opts;
		}

		public function filter_get_person_options( $opts, $mod, $id ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			if ( $opts !== false )
				return $opts;
			elseif ( preg_match( '/^tribe_organizer-([0-9]+)$/', $id, $match ) )
				$mod = $this->p->m['util']['post']->get_mod( $match[1] );
			elseif ( $mod['post_type'] !== 'tribe_organization' )
				return $opts;

			$opts = array(
				'person_type' => 'person',
				'person_url' => tribe_get_organizer_link( $mod['id'], false, false ),
				'person_name' => tribe_get_organizer( $mod['id'] ),
				'person_desc' => $this->p->webpage->get_description( $this->p->options['schema_desc_len'], '...', $mod, true,
					false, true, 'schema_desc' ),	// $add_hashtags = false, $encode = true, $md_idx = schema_desc
				'person_email' => tribe_get_organizer_email( $mod['id'] ),
				'person_phone' => tribe_get_organizer_phone( $mod['id'] ),
			);

			return $opts;
		}

		public function filter_get_place_options( $opts, $mod, $place_id ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			if ( $opts !== false )	// first come, first served
				return $opts;
			elseif ( preg_match( '/^tribe_venue-([0-9]+)$/', $place_id, $match ) )	// specific place
				$mod = $this->p->m['util']['post']->get_mod( $match[1] );
			elseif ( $mod['post_type'] !== 'tribe_venue' )			// current post
				return $opts;

			$opts = array(
				'place_url' => tribe_get_venue_link( $mod['id'], false ),
				'place_name' => tribe_get_venue( $mod['id'] ),
				'place_desc' => $this->p->webpage->get_description( $this->p->options['schema_desc_len'], '...', $mod, true,
					false, true, 'schema_desc' ),	// $add_hashtags = false, $encode = true, $md_idx = schema_desc
				'place_streetaddr' => tribe_get_address( $mod['id'] ),
				'place_city' => tribe_get_city( $mod['id'] ),
				'place_state' => tribe_get_region( $mod['id'] ),
				'place_zipcode' => tribe_get_zip( $mod['id'] ),
				'place_country' => tribe_get_country( $mod['id'] ),
				'place_phone' => tribe_get_phone( $mod['id'] ),
			);

			$coords = tribe_get_coordinates( $mod['id'] );
			foreach ( array( 'lat' => 'latitude', 'lng' => 'longitude' ) as $key => $suffix )
				if ( isset( $coords[$key] ) )
					$opts['place_'.$suffix] = $coords[$key];

			return $opts;
		}

		public function filter_schema_meta_itemprop( $mt_schema, $mod ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			if ( $mod['post_type'] !== 'tribe_events' )
				return $mt_schema;

			$mt_schema['startdate'] = date( 'c', Tribe__Events__Timezones::event_start_timestamp( $mod['id'], 'UTC' ) );
			$mt_schema['enddate'] = date( 'c', Tribe__Events__Timezones::event_end_timestamp( $mod['id'], 'UTC' ) );

			return $mt_schema;
		}
	}
}

?>
