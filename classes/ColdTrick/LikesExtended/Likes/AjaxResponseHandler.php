<?php

namespace ColdTrick\LikesExtended\Likes;

use Elgg\Likes\DataService;
use Elgg\Services\AjaxResponse;

/**
 * Ajax response handler
 */
class AjaxResponseHandler {

	/**
	 * Alter ajax response to send back likes count
	 *
	 * @param \Elgg\Event $event 'ajax_response', 'all'
	 *
	 * @return AjaxResponse|null
	 */
	public function __invoke(\Elgg\Event $event): ?AjaxResponse {
		$entity = get_entity((int) get_input('guid'));
		if (!$entity || elgg_get_viewtype() !== 'default') {
			return null;
		}
		
		if (!$entity->hasCapability('likable')) {
			return null;
		}
		
		/** @var AjaxResponse $response */
		$response = $event->getValue();
		
		$menu = _elgg_services()->menus->getUnpreparedMenu('social', [
			'entity' => $entity,
		]);
		
		$status = [
			'guid' => $entity->guid,
			'count' => DataService::instance()->getTotalNumLikes($entity),
			'menu' => [],
		];
		
		/** @var \ElggMenuItem $item */
		foreach ($menu->getItems() as $item) {
			if (!str_starts_with($item->getName(), 'likes_')) {
				continue;
			}
			
			if ($item->getName() === 'likes_dropdown') {
				$item->addLinkClass('elgg-menu-parent');
			}
			
			$status['menu'][$item->getName()] = elgg_view('navigation/menu/elements/item/url', [
				'item' => $item,
			]);
		}
		
		$response->getData()->likes_status = $status;
		
		return $response;
	}
}
