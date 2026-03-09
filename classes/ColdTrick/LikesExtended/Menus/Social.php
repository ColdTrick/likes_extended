<?php

namespace ColdTrick\LikesExtended\Menus;

use Elgg\Likes\DataService;
use Elgg\Menu\MenuItems;

/**
 * Add items to the social menu
 */
class Social {
	
	/**
	 * Add items to the social menu
	 *
	 * @param \Elgg\Event $event 'register', 'menu:social'
	 *
	 * @return MenuItems|null
	 */
	public static function register(\Elgg\Event $event): ?MenuItems {
		$entity = $event->getEntityParam();
		if (!$entity instanceof \ElggEntity) {
			return null;
		}
		
		if (!$entity->hasCapability('likable') || !$entity->canAnnotate(0, 'likes')) {
			return null;
		}
		
		/** @var MenuItems $return */
		$return = $event->getValue();
		
		$subtypes = likes_extended_get_subtypes();
		$dataservice = DataService::instance();
		
		foreach ($subtypes as $subtype => $config) {
			$is_liked = $dataservice->currentUserLikesEntity($entity->guid, $subtype);
			
			$class = '';
			if ($is_liked) {
				$class = 'elgg-state-active';
				$action = elgg_generate_action_url('likes/delete', [
					'guid' => $entity->guid,
				]);
				$text = elgg_extract('remove_text', $config, $subtype);
				if (empty($text) && elgg_language_key_exists("likes_extended:menu:text:{$subtype}:remove")) {
					$text = elgg_echo("likes_extended:menu:text:{$subtype}:remove");
				}
			} else {
				$action = elgg_generate_action_url('likes/add', [
					'guid' => $entity->guid,
					'subtype' => $subtype,
				]);
				$text = elgg_extract('add_text', $config, $subtype);
				if (empty($text) && elgg_language_key_exists("likes_extended:menu:text:{$subtype}:add")) {
					$text = elgg_echo("likes_extended:menu:text:{$subtype}:add");
				}
			}
			
			$return[] = \ElggMenuItem::factory([
				'name' => "likes_{$subtype}",
				'href' => $action,
				'icon' => elgg_extract('icon', $config),
				'class' => $class,
				'text' => $text,
				'title' => $text,
			]);
		}
		
		return $return;
	}
}
