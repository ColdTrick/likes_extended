<?php

namespace ColdTrick\LikesExtended\Likes;

/**
 * Send a notification to the Entity owner when a Likes annotation is created
 *
 * @since 6.1
 */
class CreateLikesEventHandler extends \Elgg\Likes\Notifications\CreateLikesEventHandler {
	
	/**
	 * {@inheritdoc}
	 */
	protected function getNotificationSubject(\ElggUser $recipient, string $method): string {
		$subtype = $this->getLikesAnnotation()->value;
		$key = 'likes:notifications:subject';
		if (elgg_language_key_exists("likes_extended:{$subtype}:notification:subject")) {
			$key = "likes_extended:{$subtype}:notification:subject";
		}
		
		return elgg_echo($key, [
			$this->getEventActor()?->getDisplayName(),
			$this->getEntityTitle(80),
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getNotificationSummary(\ElggUser $recipient, string $method): string {
		$subtype = $this->getLikesAnnotation()->value;
		$key = 'likes:notifications:subject';
		if (elgg_language_key_exists("likes_extended:{$subtype}:notification:summary")) {
			$key = "likes_extended:{$subtype}:notification:summary";
		} elseif (elgg_language_key_exists("likes_extended:{$subtype}:notification:subject")) {
			$key = "likes_extended:{$subtype}:notification:subject";
		}
		
		return elgg_echo($key, [
			$this->getEventActor()?->getDisplayName(),
			$this->getEntityTitle(),
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getNotificationBody(\ElggUser $recipient, string $method): string {
		$subtype = $this->getLikesAnnotation()->value;
		$key = 'likes:notifications:body';
		if (elgg_language_key_exists("likes_extended:{$subtype}:notification:body")) {
			$key = "likes_extended:{$subtype}:notification:body";
		}
		
		return elgg_echo($key, [
			$this->getEventActor()?->getDisplayName(),
			$this->getEntityTitle(),
			elgg_get_site_entity()->getDisplayName(),
			$this->getLikedEntity()?->getURL(),
			$this->getEventActor()?->getURL(),
		]);
	}
}
