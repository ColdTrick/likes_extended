<?php

namespace ColdTrick\LikesExtended\Likes;

use Elgg\Database\AnnotationsTable;

/**
 * Preload likes counts
 */
class Preloader extends \Elgg\Likes\Preloader {
	
	/**
	 * Preload likes count based on guids
	 *
	 * @param int[] $guids the guids to preload
	 *
	 * @return void
	 */
	protected function preloadCountsFromQuery(array $guids) {
		$count_rows = elgg_get_annotations([
			'annotation_names' => 'likes',
			'guids' => $guids,
			'selects' => ['e.guid', AnnotationsTable::DEFAULT_JOIN_ALIAS . '.value', 'COUNT(*) AS cnt'],
			'group_by' => 'e.guid, ' . AnnotationsTable::DEFAULT_JOIN_ALIAS . '.value',
			'limit' => false,
			'callback' => false,
		]);
		
		$subtypes = likes_extended_get_subtypes();
		
		foreach ($guids as $guid) {
			foreach ($subtypes as $subtype => $config) {
				$this->data->setNumLikes($guid, 0, $subtype);
			}
		}
		
		foreach ($count_rows as $row) {
			$this->data->setNumLikes($row->guid, $row->cnt, $row->value);
		}
	}
	
	/**
	 * Preload likes for given guids for current user
	 *
	 * @param int[] $guids preload guids
	 *
	 * @return void
	 */
	protected function preloadCurrentUserLikes(array $guids) {
		$owner_guid = elgg_get_logged_in_user_guid();
		if (!$owner_guid) {
			return;
		}
		
		$annotation_rows = elgg_get_annotations([
			'annotation_names' => 'likes',
			'annotation_owner_guids' => $owner_guid,
			'guids' => $guids,
			'limit' => false,
			'callback' => false,
		]);
		
		$subtypes = likes_extended_get_subtypes();
		
		foreach ($guids as $guid) {
			foreach ($subtypes as $subtype => $config) {
				$this->data->setLikedByCurrentUser($guid, false, $subtype);
			}
		}
		
		foreach ($annotation_rows as $row) {
			$this->data->setLikedByCurrentUser($row->entity_guid, true, $row->value);
		}
	}
}
