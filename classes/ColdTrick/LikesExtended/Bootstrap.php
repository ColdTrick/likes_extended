<?php

namespace ColdTrick\LikesExtended;

use Elgg\DefaultPluginBootstrap;
use Elgg\Project\Paths;

/**
 * Plugin bootstrap
 */
class Bootstrap extends DefaultPluginBootstrap {
	
	/**
	 * {@inheritdoc}
	 */
	public function boot() {
		$paths = [];
		if (is_dir($this->plugin()->getPath() . 'classes/Elgg/Likes')) {
			$paths[] = Paths::sanitize($this->plugin()->getPath() . 'classes');
		}
		
		$likes = elgg_get_plugin_from_id('likes');
		if ($likes instanceof \ElggPlugin && is_dir($likes->getPath() . 'classes/Elgg/Likes')) {
			$paths[] = Paths::sanitize($likes->getPath() . 'classes');
		}
		
		if (!empty($paths)) {
			_elgg_services()->classLoader->registerNamespace('Elgg\Likes', $paths);
		}
	}
}
