<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
*/
class MLCacheController {
	/**
	* Set cache item as wp transient record
	*
	* @since 3.3.3
	*
	* @param $type String - type of the record (for the flush cache by type)
	* @param $key String - unique key for the data
	* @param $data String - cached data
	*/
	public function set_cache( $type, $key, $data ) {
		$hash = hash( 'crc32', $key );
		set_transient( $type . '_' . $hash, $data, 8 * HOUR_IN_SECONDS );
	}

	/**
	* Get cache from wp transient database
	*
	* @since 3.3.3
	*
	* @param $type String
	* @param $key String
	*
	* @return String | null
	*/
	function get_cache( $type, $key ) {
		$hash   = hash( 'crc32', $key );
		$cached = get_transient( $type . '_' . $hash );

		return ( ! empty( $cached ) ? $cached : null );
	}

	/**
	* Flush cache from wp transient database
	*
	* @since 3.5.1
	*
	* @param $type String
	*/
	public function flush_cache( $type ) {
		global $wpdb;
		$sql = 	$wpdb->prepare(
			"SELECT option_name AS name FROM $wpdb->options
			WHERE option_name LIKE %s",
			$wpdb->esc_like( "_transient_{$type}" ) .'%'
		);

		$json_transients = $wpdb->get_results( $sql );
		foreach ( $json_transients as $transient ) {
			delete_transient( str_replace( '_transient_', '', $transient->name ) );
		}
	}

	/**
	* Flush post cache from wp transient database
	*
	* @since 3.5.1
	*
	* @param $post_id int
	*/
	public function flush_post_cache( $post_id ) {
		// post with any image_format
		delete_transient( 'ml_post_' . hash( 'crc32', $this->post_cache_key( $post_id ) ) );
		delete_transient( 'ml_post_' . hash( 'crc32', $this->post_cache_key( $post_id, 1 ) ) );
		delete_transient( 'ml_post_' . hash( 'crc32', $this->post_cache_key( $post_id, 2 ) ) );
	}

	/**
	* @param $post_id
	*
	* @return string
	*/
	public function post_cache_key( $post_id, $image_format = null ) {
		if ( is_null( $image_format ) ) {
			$key = http_build_query(
				array(
					'post_id' => "$post_id",
					'type'    => 'ml_post',
				)
			);
		} else {
			$key = http_build_query(
				array(
					'post_id' => "$post_id",
					'type'    => 'ml_post',
					'i_f'     => $image_format,
				)
			);
		}
		return $key;
	}
}
