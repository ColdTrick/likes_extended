<?php

$guid = (int) get_input('guid');

if (!get_entity($guid)) {
	echo elgg_echo('error:missing_data');
	return;
}

echo elgg_view('page/components/tabs', [
	'tabs' => [
		[
			'text' => 'Likes',
			'href' => 'ajax/view/likes_extended/popup_content?subtype=likes&guid=' . $guid,
		],
		[
			'text' => 'Cheers',
			'href' => 'ajax/view/likes_extended/popup_content?subtype=cheers&guid=' . $guid,
		],
	],
]);
