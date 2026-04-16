<?php

use Elgg\Likes\DataService;

$guid = (int) get_input('guid');
$entity = get_entity($guid);
if (!$entity instanceof \ElggEntity) {
	echo elgg_echo('error:missing_data');
	return;
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
			'segments' => 'view/likes_extended/popup_content',
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
	'enable_overflow' => false,
]);

?>
<script type="module">
	import 'jquery';
	
	$(document).on('open', '.elgg-tabs-component.likes-extended-detail-tabs .elgg-tabs > li', function() {
		$(window).trigger('resize.lightbox');
	});
	$('#cboxLoadedContent').find('.elgg-menu-navigation-tabs .elgg-components-tab.elgg-state-selected a').trigger('click');
</script>
