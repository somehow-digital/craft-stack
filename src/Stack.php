<?php

declare(strict_types=1);

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

/**
 * @method Settings getSettings()
 */
class Stack extends Plugin
{
    public const EVENT_NAMESPACE_CONTEXT = 'namespaceContext';

    public function init(): void
    {
        parent::init();

        if (count($this->getSettings()->namespaces) > 0) {
            $this->registerSiteTemplateRoots();
        }
    }

    protected function createSettingsModel(): Model
    {
        return new Settings();
    }

    private function registerSiteTemplateRoots(): void
    {
        Event::on(
            View::class,
            View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS,
            function (RegisterTemplateRootsEvent $event): void {
                /** @var View $view */
                $view = $event->sender;

                foreach ($this->getSettings()->namespaces as $namespace) {
                    $this->registerNamespace($view, $event, $namespace);
                }
            }
        );
    }

    /**
     * @param array{handle?: string, path?: string, context?: array<string, mixed>} $namespace
     */
    private function registerNamespace(View $view, RegisterTemplateRootsEvent $event, array $namespace): void
    {
        $context = $this->getNamespaceContext($namespace);

        $path = $view->renderObjectTemplate($namespace['path'] ?? '', $context);

        if ($path === '') {
            return;
        }

        $path = FileHelper::normalizePath($view->getTemplatesPath() . DIRECTORY_SEPARATOR . $path);
        /** @var array<string, list<string>> $roots */
        $roots = $event->roots;
        $name = trim($view->renderObjectTemplate($namespace['handle'] ?? '', $context));

        if ($name !== '') {
            $roots[$this->getSettings()->prefix . $name][] = $path;
        }

        $roots[''][] = $path;
        $event->roots = $roots;
    }

    /**
     * @param array{handle?: string, path?: string, context?: array<string, mixed>} $namespace
     * @return array<string, mixed>
     */
    private function getNamespaceContext(array $namespace): array
    {
        $site = Craft::$app->getSites()->getCurrentSite();
        $group = $site->groupId === null ? null : Craft::$app->getSites()->getGroupById($site->groupId);

        $event = new NamespaceContextEvent();
        $event->namespace = $namespace;
        $event->context = [
            'site' => $site,
            'group' => $group,
            ...$namespace['context'] ?? [],
        ];

        $this->trigger(self::EVENT_NAMESPACE_CONTEXT, $event);

        return $event->context;
    }
}
