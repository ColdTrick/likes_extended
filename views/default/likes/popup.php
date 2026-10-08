<?php

use Elgg\Exceptions\Http\UnauthorizedException;
use Elgg\Likes\DataService;

$guid = (int) get_input('guid');

$entity = get_entity($guid);
if (!$entity instanceof \ElggEntity || !$entity->hasCapability('likable')) {
	echo elgg_echo('error:missing_data');
	return;
}

if (!(bool) elgg_get_plugin_setting('details', 'likes') && !$entity->canEdit()) {
	throw new UnauthorizedException();
}

$tabs = [];
$selected_tab = null;
$selected_tab_count = 0;

$dataservice = DataService::instance();
$subtypes = likes_extended_get_subtypes();

foreach ($subtypes as $subtype => $config) {
	$count = $dataservice->getNumLikes($entity, $subtype);
	if (!isset($selected_tab) || $count > $selected_tab_count) {
		$selected_tab = $subtype;
		$selected_tab_count = $count;
	}
}

foreach ($subtypes as $subtype => $config) {
	$count = $dataservice->getNumLikes($entity, $subtype);
	if ($count < 1) {
		continue;
	}
	
	$key = $subtype;
	if (elgg_language_key_exists("likes_extended:{$subtype}:tab")) {
		$key = "likes_extended:{$subtype}:tab";
	}
	
	$href = false;
	$content = null;
	if ($selected_tab === $subtype) {
		$content = elgg_view('likes_extended/popup_content', [
			'guid' => $guid,
			'subtype' => $subtype,
		]);
	} else {
		$href = elgg_generate_url('ajax', [
			'type' => 'view',
			'segments' => 'likes_extended/popup_content',
			'subtype' => $subtype,
			'guid' => $guid,
		]);
	}
	
	$tabs[] = [
		'text' => elgg_echo($key),
		'href' => $href,
		'content' => $content,
		'badge' => $count,
		'selected' => $selected_tab === $subtype,
		'data-ajax-reload' => false,
		'priority' => -$count,
	];
}

if (count($tabs) === 1) {
	echo $tabs[0]['content'];
	return;
}

echo elgg_view('page/components/tabs', [
	'tabs' => $tabs,
	'class' => 'likes-extended-detail-tabs',
]);
