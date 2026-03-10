<?php
/**
 * Helper functions can be found here
 */

/**
 * Get the supported subtypes for Likes
 *
 * @return array
 */
function likes_extended_get_subtypes(): array {
	$defaults = [
		'likes' => [
			'icon' => 'thumbs-up',
			'add_text' => elgg_echo('likes:likethis'),
			'remove_text' => elgg_echo('likes:remove'),
		],
	];
	
	return elgg_trigger_event_results('likes:subtypes', 'likes_extended', [], $defaults);
}
