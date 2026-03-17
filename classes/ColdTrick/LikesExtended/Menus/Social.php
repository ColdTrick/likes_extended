<?php

namespace ColdTrick\LikesExtended\Menus;

use Elgg\Likes\DataService;
use Elgg\Menu\MenuItems;
use Elgg\Values;

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
		$base_priority = 1000000;
		
		foreach ($subtypes as $subtype => $config) {
			$is_liked = $dataservice->currentUserLikesEntity($entity->guid, $subtype);
			$count = $dataservice->getNumLikes($entity, $subtype);
			
			$class = [];
			if ($count) {
				$class[] = 'elgg-likes-has-badge';
			}
			
			if ($is_liked) {
				$class[] = 'elgg-state-active';
				$action = elgg_generate_action_url('likes/delete', [
					'guid' => $entity->guid,
				]);
				$text = elgg_extract('remove_text', $config, $subtype);
				if (empty($text) && elgg_language_key_exists("likes_extended:{$subtype}:menu:remove")) {
					$text = elgg_echo("likes_extended:{$subtype}:menu:remove");
				}
			} else {
				$action = elgg_generate_action_url('likes/add', [
					'guid' => $entity->guid,
					'subtype' => $subtype,
				]);
				$text = elgg_extract('add_text', $config, $subtype);
				if (empty($text) && elgg_language_key_exists("likes_extended:{$subtype}:menu:add")) {
					$text = elgg_echo("likes_extended:{$subtype}:menu:add");
				}
			}
			
			$return[] = \ElggMenuItem::factory([
				'name' => "likes_{$subtype}",
				'href' => $action,
				'icon' => elgg_extract('icon', $config),
				'class' => $class,
				'text' => $text,
				'badge' => $count ?: null,
				'title' => $text,
				'data-likes-guid' => $entity->guid,
				'deps' => ['elgg/likes'],
				'priority' => $base_priority - $count,
			]);
		}
		
		// view listing
		$total = $dataservice->getTotalNumLikes($entity);
		if ($total == 1) {
			$likes_string = elgg_echo('likes_extended:num_likes:text:single', [$total]);
		} else {
			$likes_string = elgg_echo('likes_extended:num_likes:text:plural', [Values::shortFormatOutput($total)]);
		}
		
		$return[] = \ElggMenuItem::factory([
			'name' => 'likes_count',
			'text' => $likes_string,
			'title' => elgg_echo('likes_extended:num_likes:title'),
			'href' => elgg_generate_url('ajax', [
				'segments' => 'view/likes/popup',
				'guid' => $entity->guid,
			]),
			'data-likes-guid' => $entity->guid,
			'data-colorbox-opts' => json_encode([
				'maxHeight' => '85%',
			]),
			'item_class' => $total === 0 ? 'hidden' : '',
			'link_class' => 'elgg-lightbox',
			'deps' => ['elgg/likes'],
			'priority' => $base_priority,
		]);
		
		return $return;
	}
}
