<?php
/*

	@ POLL HELPER METHODS

*/

Class OnyxPolls {

	/**
	 * Constructor
	 */
	public function __construct() {
		// empty
	}

	/**
	* Update poll status when expire
	*/
	public static function expire_polls() {
		$today = date_i18n('Y-m-d H:i:s');
		$query = new WP_Query([
			"post_type"         => "onyxpolls",
			"no_found_rows"     => true,
			"posts_per_page"    => 5,
			"fields"            => 'ids',
			"meta_query"        => array(
				"relation"    => "AND",
				array(
					"key"      => 'onyx_poll_end',
					"value"    => $today,
					"compare"  => "<=",
					"type"     => "DATETIME"
				),
				array(
					"key"      => 'onyx_poll_expired',
					"value"    => 1,
					"compare"  => "!="
				)
			)
		]);

		if ($query->have_posts()) {
			$response = $query->posts;
			foreach ($query->posts as $post) {
				// $post = array("ID" => $post, 'post_status' => 'draft');
				// wp_update_post($post);
				update_field('onyx_poll_expired', 1, $post);
			}
		} else {
			$response = null;
		}
		return $response;
	}

	/**
	 * Verify if has active polls
	 * 
	 * @param bool $noexpired optional
	 * @param bool $onlymodal optional
	 * @return $post->ID
	 */
	public static function has_polls($noexpired = false, $onlymodal = false) {
		$args = [
			'post_type'         => 'onyxpolls',
			'no_found_rows'     => true,
			'suppress_filters'  => true,
			'posts_per_page'    => 1,
			'fields'            => 'ids',
			'meta_query'        => array()
		];

		if ($noexpired) {
			$args['meta_query'][] = array(
				array(
					'key'   => 'onyx_poll_expired',
					'value'   => '1',
					'compare' => '!='
				)
			);
		}

		if ($onlymodal) {
			$args['meta_query'][] = array(
				array(
					'key'   => 'onyx_poll_modal',
					'value' => '1'
				)
			);
		}

		

		$query = new WP_Query($args);
		return ($query->have_posts()) ? $query->posts[0] : false;
	}

	/**
	 * Verify if poll id exist (even expired)
	 * 
	 * @param int $id required
	 */
	public static function has_poll($id = false) {

		$args = array(
			'post_type'         => 'onyxpolls',
			'no_found_rows'     => true,
			'suppress_filters'  => true,
			'posts_per_page'    => 1,
			'fields'            => 'ids'
		);

		if($id) $args['p'] = (int) $id;

		$query = new WP_Query($args);
		return ($query->have_posts()) ? $query->posts[0] : false;
	}

}

?>
