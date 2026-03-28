<?php

namespace SomehowDigital\Craft\Stack;

use Craft;
use craft\base\Event;
use craft\base\Model;
use craft\base\Plugin;
use craft\events\RegisterTemplateRootsEvent;
use craft\helpers\FileHelper;
use craft\web\View;
use SomehowDigital\Craft\Stack\events\NamespaceContextEvent;
use SomehowDigital\Craft\Stack\models\Settings;

class Stack extends Plugin
{
	const EVENT_NAMESPACE_CONTEXT = 'namespaceContext';

	public function init(): void
	{
		parent::init();

		if (count($this->getSettings()->namespaces) > 0) {
			$this->registerSiteTemplateRoots();
		}
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

				foreach ($this->getSettings()->namespaces as $namespace) {
					$context = $this->getNamespaceContext($namespace);

					$name = $view->renderObjectTemplate($namespace['handle'] ?? '', $context);
					$path = $view->renderObjectTemplate($namespace['path'] ?? '', $context);

					if ($path) {
						$name = $name ? $this->getSettings()->prefix . trim($name) : '';
						$path = FileHelper::normalizePath($view->getTemplatesPath() . DIRECTORY_SEPARATOR . $path);

						if ($name) {
							$event->roots[$name][] = $path;
						}

						$event->roots[''][] = $path;
					}
				}
			}
		);
	}

	private function getNamespaceContext(array $namespace): array
	{
		$site = Craft::$app->sites->getCurrentSite();
		$group = Craft::$app->sites->getGroupById($site->groupId);

		$event = new NamespaceContextEvent();
		$event->namespace = $namespace;
		$event->context = [
			'site' => $site,
			'group' => $group,
			...$namespace['context'] ?? [],
		];

		$this->trigger(static::EVENT_NAMESPACE_CONTEXT, $event);

		return $event->context;
	}
}
