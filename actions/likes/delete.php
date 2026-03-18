<?php
/**
 * Elgg delete like action
 *
 */

$likes = elgg_get_annotations([
	'guid' => (int) get_input('guid'),
	'annotation_owner_guid' => elgg_get_logged_in_user_guid(),
	'annotation_name' => 'likes',
	'limit' => 1,
]);

if (empty($likes)) {
	return elgg_error_response(elgg_echo('likes:notdeleted'));
}

/** @var \ElggAnnotation $like */
$like = elgg_extract(0, $likes);
$subtype = $like->value;
if (!$like->delete()) {
	return elgg_error_response(elgg_echo('likes:notdeleted'));
}

$key = 'likes:deleted';
if (elgg_language_key_exists("likes_extended:{$subtype}:action:deleted")) {
	$key = "likes_extended:{$subtype}:action:deleted";
}

return elgg_ok_response('', elgg_echo($key));
