<?php

use ColdTrick\LikesExtended\Bootstrap;

require_once(__DIR__ . '/lib/functions.php');

return [
	'plugin' => [
		'version' => '2.0',
		'dependencies' => [
			'likes' => [
				'position' => 'after',
			],
		],
	],
	'bootstrap' => Bootstrap::class,
	'actions' => [
		'likes/add' => [],
		'likes/delete' => [],
	],
	'events' => [
		'ajax_results' => [
			'all' => [
				\Elgg\Likes\AjaxResponseHandler::class => ['unregister' => true],
				\ColdTrick\LikesExtended\Likes\AjaxResponseHandler::class => [],
			],
		],
		'elgg.data' => [
			'page' => [
				\Elgg\Likes\JsConfigHandler::class => ['unregister' => true],
			],
		],
		'parameters' => [
			'menu:social' => [
				'\ColdTrick\LikesExtended\Menus\Social::commentParameters' => [],
			],
		],
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
	'notifications' => [
		'annotation' => [
			'likes' => [
				'create' => [
					\ColdTrick\LikesExtended\Likes\CreateLikesEventHandler::class => [],
					\Elgg\Likes\Notifications\CreateLikesEventHandler::class => ['unregister' => true],
				],
			],
		],
	],
	'view_extensions' => [
		'elgg.css' => [
			'likes_extended/site.css' => [],
		],
	],
	'view_options' => [
		'likes_extended/popup_content' => ['ajax' => true],
	],
];
