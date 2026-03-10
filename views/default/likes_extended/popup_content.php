<?php

use Elgg\Database\Clauses\OrderByClause;

$guid = (int) elgg_extract('guid', $vars);
$subtype = elgg_extract('subtype', $vars, 'likes');

if (!get_entity($guid)) {
	echo elgg_echo('error:missing_data');
	return;
}

$list = elgg_list_annotations([
	'guid' => $guid,
	'annotation_name' => 'likes',
	'annotation_value' => $subtype,
	'limit' => 99,
	'pagination' => false,
	'order_by' => new OrderByClause('a_table.time_created', 'desc'),
	'no_results' => true,
]);

echo elgg_format_element('div', ['class' => 'elgg-likes-popup'], $list);
