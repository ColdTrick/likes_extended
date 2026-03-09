<?php

use ColdTrick\LikesExtended\Bootstrap;

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
	'bootstrap' => Bootstrap::class,
	'actions' => [
		'likes/add' => [],
	],
	'events' => [
		'register' => [
			'menu:social' => [
				'\ColdTrick\LikesExtended\Menus\Social::register' => [],
				'\Elgg\Likes\Menus\Social::register' => ['unregister' => true],
			],
		],
		'view_vars' => [
			'page/components/list' => [
				'\Elgg\Likes\Preloader::preload' => ['unregister' => true],
				'\ColdTrick\LikesExtended\Likes\Preloader::preload' => [],
			],
		],
	],
	'view_options' => [
		'likes_extended/popup_content' => ['ajax' => true],
	],
];
