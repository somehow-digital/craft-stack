<?php

declare(strict_types=1);

namespace SomehowDigital\Craft\Stack\Tests\PHPStan;

use craft\base\Model;
use craft\base\Plugin;
use PHPat\Selector\Selector;
use PHPat\Selector\SelectorInterface;
use PHPat\Test\Attributes\TestRule;
use PHPat\Test\Builder\Rule;
use PHPat\Test\PHPat;
use yii\base\Event;

/**
 * What the classes of each layer extend and how they are named. Dependencies between layers are in deptrac.yaml.
 */
final class ArchitectureRules
{
    private const PLUGIN = 'SomehowDigital\Craft\Stack';

    private const TESTS = self::PLUGIN . '\Tests';

    #[TestRule]
    public function theRootHoldsOnlyThePlugin(): Rule
    {
        return PHPat::rule()
            ->classes($this->root())
            ->excluding(Selector::classname(self::PLUGIN . '\Stack'))
            ->shouldNot()->exist()
            ->because('Craft loads the plugin class, so only it is at the root, where it never moves.');
    }

    #[TestRule]
    public function thePluginIsACraftPlugin(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::classname(self::PLUGIN . '\Stack'))
            ->should()->extend()
            ->classes(Selector::classname(Plugin::class))
            ->because('The plugin is the class that Craft loads the plugin through.');
    }

    #[TestRule]
    public function modelsAreCraftModels(): Rule
    {
        return PHPat::rule()
            ->classes($this->layer('models'))
            ->should()->extend()
            ->classes(Selector::classname(Model::class))
            ->because('A model validates input as a Craft model.');
    }

    #[TestRule]
    public function eventsAreYiiEvents(): Rule
    {
        return PHPat::rule()
            ->classes($this->layer('events'))
            ->should()->extend()
            ->classes(Selector::classname(Event::class))
            ->because('Yii triggers and handles the events of the plugin.');
    }

    #[TestRule]
    public function eventsAreNamedEvent(): Rule
    {
        return PHPat::rule()
            ->classes($this->layer('events'))
            ->should()->beNamed('/Event$/', true)
            ->because('The name of an event tells what it is wherever it is used.');
    }

    #[TestRule]
    public function everyClassLivesInTheNamespaceOfALayer(): Rule
    {
        return PHPat::rule()
            ->classes($this->plugin())
            ->excluding($this->root(), $this->layer('models'), $this->layer('events'))
            ->shouldNot()->exist()
            ->because('A class belongs to a layer, whose namespace deptrac.yaml defines the dependencies of.');
    }

    #[TestRule]
    public function concreteClassesAreFinal(): Rule
    {
        return PHPat::rule()
            ->classes($this->plugin())
            ->excluding(Selector::isAbstract(), Selector::isInterface(), Selector::isTrait(), Selector::isEnum(), Selector::classname(self::PLUGIN . '\Stack'))
            ->should()->beFinal()
            ->because('Classes are composed rather than extended, so a change to one cannot break a class extending it.');
    }

    private function plugin(): SelectorInterface
    {
        return Selector::AllOf(Selector::inNamespace(self::PLUGIN), Selector::Not(Selector::inNamespace(self::TESTS)));
    }

    /**
     * Classes outside any sub-namespace.
     */
    private function root(): SelectorInterface
    {
        return Selector::inNamespace('/^' . preg_quote(self::PLUGIN, '/') . '$/', true);
    }

    /**
     * @param non-empty-string $namespace
     */
    private function layer(string $namespace): SelectorInterface
    {
        return Selector::inNamespace(self::PLUGIN . '\\' . $namespace);
    }
}
