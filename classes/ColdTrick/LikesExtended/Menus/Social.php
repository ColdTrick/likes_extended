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
		
		$likes_extended_dropdown = (bool) $event->getParam('likes_extended_dropdown', false);
		$likes_extended_stats = (bool) $event->getParam('likes_extended_stats', false);
		if ($likes_extended_stats) {
			$likes_extended_dropdown = false;
		}
		
		$menu_name = elgg_extract(1, explode(':', $event->getType()));
		
		/** @var MenuItems $return */
		$return = $event->getValue();
		
		$subtypes = likes_extended_get_subtypes();
		if (count($subtypes) < 2) {
			$likes_extended_dropdown = false;
		}
		
		$dataservice = DataService::instance();
		$total = $dataservice->getTotalNumLikes($entity);
		if ($likes_extended_stats && $total < 1) {
			return null;
		}
		
		$base_priority = 1000000;
		$top_item = null;
		$top_priority = 0;
		$top_item_locked = false;
		$like_items = [];
		
		foreach ($subtypes as $subtype => $config) {
			$is_liked = $dataservice->currentUserLikesEntity($entity->guid, $subtype);
			$count = $dataservice->getNumLikes($entity, $subtype);
			
			$class = [];
			if ($count) {
				$class[] = 'elgg-likes-has-badge';
			}
			
			if ($is_liked) {
				$class[] = 'elgg-state-selected';
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
			
			$item = \ElggMenuItem::factory([
				'name' => "likes_{$subtype}",
				'href' => $action,
				'icon' => elgg_extract('icon', $config),
				'link_class' => $class,
				'text' => $text,
				'badge' => $count ?: null,
				'title' => $text,
				'data-likes-guid' => $entity->guid,
				'deps' => ['elgg/likes'],
				'priority' => $base_priority - $count,
				'parent_name' => $likes_extended_dropdown ? 'likes_dropdown' : null,
			]);
			
			if ($is_liked) {
				$top_item = $item;
				$top_item_locked = true;
			} elseif (!isset($top_item)) {
				$top_item = $item;
				$top_priority = $count;
			} elseif (!$top_item_locked && $count > $top_priority) {
				$top_item = $item;
				$top_priority = $count;
			}
			
			$like_items[] = $item;
		}
		
		if ($likes_extended_dropdown) {
			$return[] = \ElggMenuItem::factory([
				'name' => 'likes_dropdown',
				'href' => false,
				'icon' => $top_item->icon,
				'text' => elgg_echo('likes_extended:menu:social:likes_dropdown:text'),
				'title' => elgg_echo('likes_extended:menu:social:likes_dropdown:title'),
				'link_class' => $top_item->getLinkClass(),
				'priority' => $base_priority,
				'data-likes-guid' => $entity->guid,
				'child_menu' => [
					'display' => 'dropdown',
					'data-position' => json_encode([
						'at' => 'right bottom',
						'my' => 'right top',
						'collision' => 'fit fit',
					]),
					'class' => "elgg-{$menu_name}-dropdown-menu",
				],
			]);
		}
		
		if (!$likes_extended_stats) {
			$return->merge($like_items);
		}
		
		// view listing
		if ($total == 1) {
			$likes_string = elgg_echo('likes_extended:num_likes:text:single', [$total]);
		} else {
			$likes_string = elgg_echo('likes_extended:num_likes:text:plural', [Values::shortFormatOutput($total)]);
		}
		
		if ($likes_extended_stats) {
			$return[] = \ElggMenuItem::factory([
				'name' => 'likes_stats',
				'href' => elgg_generate_url('ajax', [
					'segments' => 'view/likes/popup',
					'guid' => $entity->guid,
				]),
				'icon' => $top_item->icon,
				'text' => $likes_string,
				'title' => elgg_echo('likes_extended:num_likes:title'),
				'link_class' => $top_item->getLinkClass() . ' elgg-lightbox',
				'data-likes-guid' => $entity->guid,
				'data-colorbox-opts' => json_encode([
					'maxHeight' => '85%',
				]),
				'priority' => $base_priority,
			]);
		}
		
		$return[] = \ElggMenuItem::factory([
			'name' => 'likes_count',
			'text' => $likes_extended_dropdown || $likes_extended_stats ? $total : $likes_string,
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
	
	/**
	 * Make the comments social menu into a likes dropdown
	 *
	 * @param \Elgg\Event $event 'parameters', 'menu:social'
	 *
	 * @return array|null
	 */
	public static function commentParameters(\Elgg\Event $event): ?array {
		$entity = $event->getEntityParam();
		if (!$entity instanceof \ElggComment) {
			return null;
		}
		
		$result = $event->getValue();
		
		$result['likes_extended_dropdown'] = (bool) elgg_extract('likes_extended_dropdown', $result, true);
		
		return $result;
	}
}
