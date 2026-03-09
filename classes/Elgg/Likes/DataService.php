<?php

namespace Elgg\Likes;

use Elgg\Database\AnnotationsTable;

/**
 * Likes dataservice
 *
 * @internal
 */
class DataService {

	/**
	 * @var array [GUID => subtype => boolean]
	 */
	protected array $current_user_likes = [];

	/**
	 * @var array [GUID => subtype => int]
	 */
	protected array $num_likes = [];

	/**
	 * Set number of likes
	 *
	 * @param int    $guid    for guid
	 * @param int    $num     number of likes
	 * @param string $subtype type of like
	 *
	 * @return void
	 */
	public function setNumLikes(int $guid, int $num, string $subtype = 'likes'): void {
		$this->num_likes[$guid][$subtype] = $num;
	}

	/**
	 * Set liked status for an entity for the current logged-in user
	 *
	 * @param int    $guid     the entity guid
	 * @param bool   $is_liked liked or not
	 * @param string $subtype  type of like
	 *
	 * @return void
	 */
	public function setLikedByCurrentUser(int $guid, bool $is_liked, string $subtype = 'likes'): void {
		$this->current_user_likes[$guid][$subtype] = $is_liked;
	}

	/**
	 * Did the current logged-in user like the entity
	 *
	 * @param int    $entity_guid entity guid to check
	 * @param string $subtype     type of like
	 *
	 * @return bool
	 */
	public function currentUserLikesEntity(int $entity_guid, string $subtype = 'likes'): bool {
		if (!elgg_is_logged_in()) {
			return false;
		}
		
		if (!isset($this->current_user_likes[$entity_guid])) {
			$subtypes = likes_extended_get_subtypes();
			foreach ($subtypes as $reg_subtype => $config) {
				$this->current_user_likes[$entity_guid][$reg_subtype] = false;
			}

			$annotations = elgg_get_annotations([
				'annotation_name' => 'likes',
				'guid' => $entity_guid,
				'annotation_owner_guid' => elgg_get_logged_in_user_guid(),
			]);
			if (!empty($annotations)) {
				/** @var \ElggAnnotation $annotation */
				$annotation = $annotations[0];
				$this->current_user_likes[$entity_guid][$annotation->value] = true;
			}
		}
		
		return $this->current_user_likes[$entity_guid][$subtype] ?? false;
	}

	/**
	 * Get the number of likes for an entity
	 *
	 * @param \ElggEntity $entity  the entity to fetch for
	 * @param string      $subtype type of like
	 *
	 * @return int
	 */
	public function getNumLikes(\ElggEntity $entity, string $subtype = 'likes'): int {
		$guid = $entity->guid;
		if (!isset($this->num_likes[$guid])) {
			$count_rows = elgg_get_annotations([
				'annotation_names' => 'likes',
				'guid' => $guid,
				'selects' => ['e.guid', AnnotationsTable::DEFAULT_JOIN_ALIAS . '.value', 'COUNT(*) AS cnt'],
				'group_by' => 'e.guid, ' . AnnotationsTable::DEFAULT_JOIN_ALIAS . '.value',
				'limit' => false,
				'callback' => false,
			]);
			
			$subtypes = likes_extended_get_subtypes();
			foreach ($subtypes as $reg_subtype => $config) {
				$this->num_likes[$guid][$reg_subtype] = 0;
			}
			
			foreach ($count_rows as $row) {
				$this->num_likes[$guid][$row->value] = (int) $row->cnt;
			}
		}
		
		return $this->num_likes[$guid][$subtype] ?? 0;
	}

	/**
	 * Get a DataService instance
	 *
	 * @return self
	 */
	public static function instance(): self {
		static $inst;
		if ($inst === null) {
			$inst = new self();
		}
		
		return $inst;
	}
}
