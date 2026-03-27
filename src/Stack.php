<?php

namespace SomehowDigital\Craft\Stack;

use Craft;
use craft\base\Event;
use craft\base\Model;
use craft\base\Plugin;
use craft\events\RegisterTemplateRootsEvent;
use craft\helpers\FileHelper;
use craft\web\View;
use SomehowDigital\Craft\Stack\models\Settings;

class Stack extends Plugin
{
	public function init(): void
	{
		parent::init();

		$this->registerSiteTemplateRoots();
	}

	protected function createSettingsModel(): ?Model
	{
		return new Settings();
	}

	private function registerSiteTemplateRoots(): void
	{
		Event::on(
			View::class,
			View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS,
			function(RegisterTemplateRootsEvent $event) {
				/** @var View $view */
				$view = $event->sender;

				$site = Craft::$app->sites->getCurrentSite();
				$group = Craft::$app->sites->getGroupById($site->groupId);

				if ($site && $group) {
					foreach ($this->getSettings()->namespaces as $namespace) {
						$name = $view->renderObjectTemplate($namespace['handle'], $site, ['group' => $group]);
						$path = $view->renderObjectTemplate($namespace['path'], $site, ['group' => $group]);

						if ($name && $path) {
							$name = '@' . trim($name);
							$path = FileHelper::normalizePath($view->getTemplatesPath() . DIRECTORY_SEPARATOR . $path);

							$event->roots[$name][] = $path;
							$event->roots[''][] = $path;
						}
					}
				}
			}
		);
	}
}
