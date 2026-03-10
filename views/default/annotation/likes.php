<?php
/**
 * Elgg show the users who liked the object
 *
 * @uses $vars['annotation'] The like annotation
 */

$annotation = elgg_extract('annotation', $vars);
if (!$annotation instanceof \ElggAnnotation) {
	return;
}

$owner = $annotation->getOwnerEntity();
if (!$owner instanceof \ElggEntity) {
	return;
}

$owner_link = elgg_view_entity_url($owner);

$subtype = $annotation->value;

$key = 'likes:this';
if (elgg_language_key_exists("likes_extended:{$subtype}:annotation")) {
	$key = "likes_extended:{$subtype}:annotation";
}

$params = [
	'title' => elgg_echo($key, [$owner_link]),
	'content' => false,
];
$params = $params + $vars;
echo elgg_view('annotation/elements/summary', $params);
