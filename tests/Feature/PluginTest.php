<?php

declare(strict_types=1);

use craft\events\RegisterTemplateRootsEvent;
use craft\helpers\FileHelper;
use craft\web\View;
use SomehowDigital\Craft\Stack\events\NamespaceContextEvent;
use SomehowDigital\Craft\Stack\models\Settings;
use SomehowDigital\Craft\Stack\Stack;
use yii\base\Event;

/**
 * The template roots registered for the site.
 *
 * @return array<string, list<string>>
 */
function templateRoots(): array
{
    $event = new RegisterTemplateRootsEvent();
    Craft::$app->getView()->trigger(View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS, $event);

    // @phpstan-ignore return.type (Craft leaves the roots untyped)
    return $event->roots;
}

/**
 * The absolute path of a template directory.
 */
function templatePath(string $path): string
{
    return FileHelper::normalizePath(Craft::$app->getView()->getTemplatesPath() . DIRECTORY_SEPARATOR . $path);
}

it('has settings', function (): void {
    expect(Stack::getInstance()?->getSettings())->toBeInstanceOf(Settings::class);
});

it('registers no template roots without namespaces', function (): void {
    expect(templateRoots())->toBe([]);
});

it('registers a prefixed root and a global root for a namespace', function (): void {
    $this->createApp(['namespaces' => [['handle' => 'theme', 'path' => '_theme']]]);

    expect(templateRoots())->toBe([
        '@theme' => [templatePath('_theme')],
        '' => [templatePath('_theme')],
    ]);
});

it('uses the configured prefix', function (): void {
    $this->createApp(['prefix' => '#', 'namespaces' => [['handle' => 'theme', 'path' => '_theme']]]);

    expect(templateRoots())->toHaveKey('#theme');
});

it('registers multiple paths for a handle in order', function (): void {
    $this->createApp(['namespaces' => [
        ['handle' => 'theme', 'path' => '_site'],
        ['handle' => 'theme', 'path' => '_base'],
    ]]);

    expect(templateRoots())->toBe([
        '@theme' => [templatePath('_site'), templatePath('_base')],
        '' => [templatePath('_site'), templatePath('_base')],
    ]);
});

it('registers only the global root without a handle', function (): void {
    $this->createApp(['namespaces' => [['path' => '_shared']]]);

    expect(templateRoots())->toBe(['' => [templatePath('_shared')]]);
});

it('skips namespaces without a path', function (): void {
    $this->createApp(['namespaces' => [['handle' => 'theme']]]);

    expect(templateRoots())->toBe([]);
});

it('renders the handle and path with the site and group', function (): void {
    $this->createApp(['namespaces' => [['handle' => '{site.handle}', 'path' => '_{group.name}/{site.handle}']]]);

    expect(templateRoots())->toBe([
        '@default' => [templatePath('_Group/default')],
        '' => [templatePath('_Group/default')],
    ]);
});

it('renders the handle and path with the context of the namespace', function (): void {
    $this->createApp(['namespaces' => [[
        'handle' => '{brand}',
        'path' => '_{brand}',
        'context' => ['brand' => 'acme'],
    ]]]);

    expect(templateRoots())->toHaveKey('@acme', [templatePath('_acme')]);
});

it('lets the context be changed by an event', function (): void {
    $this->createApp(['namespaces' => [['handle' => 'theme', 'path' => '_{brand}']]]);

    Event::on(Stack::class, Stack::EVENT_NAMESPACE_CONTEXT, function (NamespaceContextEvent $event): void {
        expect($event->namespace['handle'] ?? null)->toBe('theme');

        $event->context['brand'] = 'acme';
    });

    expect(templateRoots())->toHaveKey('@theme', [templatePath('_acme')]);
});

it('trims the handle of a namespace', function (): void {
    $this->createApp(['namespaces' => [['handle' => ' theme ', 'path' => '_theme']]]);

    expect(templateRoots())->toHaveKey('@theme');
});

it('initializes as a Yii module', function (): void {
    expect(Stack::getInstance()?->controllerNamespace)->toBe('SomehowDigital\Craft\Stack\controllers');
});
