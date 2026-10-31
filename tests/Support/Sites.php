<?php

namespace SomehowDigital\Craft\Stack\Tests\Support;

use craft\models\Site;
use craft\models\SiteGroup;
use craft\services\Sites as SitesService;

/**
 * The sites service, serving a single site instead of the ones stored in the database.
 */
class Sites extends SitesService
{
	public function getCurrentSite(): Site
	{
		return new Site([
			'id' => 1,
			'groupId' => 1,
			'handle' => 'default',
			'name' => 'Default',
			'language' => 'en-US',
		]);
	}

	public function getGroupById(int $groupId, bool $withTrashed = false): ?SiteGroup
	{
		return new SiteGroup([
			'id' => $groupId,
			'name' => 'Group',
		]);
	}
}
