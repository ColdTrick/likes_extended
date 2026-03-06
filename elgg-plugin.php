<?php

require_once(__DIR__ . '/lib/functions.php');

return [
	'plugin' => [
		'version' => '0.1',
		'dependencies' => [
			'likes' => [
				'position' => 'after',
			],
		],
	],
	'actions' => [
		'likes/add' => [],
	],
	'events' => [
		'register' => [
			'menu:social' => [
				'\ColdTrick\LikesExtended\Menus\Social::register' => [],
				'Elgg\Likes\Menus\Social::register' => ['unregister' => true],
			],
		],
	],
	'view_options' => [
		'likes_extended/popup_content' => ['ajax' => true],
	],
];
