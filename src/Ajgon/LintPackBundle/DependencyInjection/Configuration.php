<?php
namespace Ajgon\LintPackBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('lint_pack');

        $rootNode
            ->children()
                ->arrayNode('jshint')
                    ->canBeEnabled()
                    ->children()
                        ->scalarNode('bin')
                            ->defaultValue('jshint')
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('jshintrc')
                        ->end()
                        ->scalarNode('jshintignore')
                        ->end()
                        ->arrayNode('extensions')
                            ->defaultValue(array('js'))
                            ->prototype('scalar')
                            ->end()
                        ->end()
                        ->arrayNode('ignores')
                            ->defaultValue(array())
                            ->prototype('scalar')
                            ->end()
                        ->end()
                        ->arrayNode('locations')
                            ->defaultValue(array('%kernel.root_dir%', '%kernel.root_dir%/../src'))
                            ->prototype('scalar')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('phpcs')
                    ->canBeEnabled()
                    ->children()
                        ->scalarNode('bin')
                            ->defaultValue('phpcs')
                            ->cannotBeEmpty()
                        ->end()
                        ->booleanNode('warnings')
                            ->defaultFalse()
                        ->end()
                        ->booleanNode('recursion')
                            ->defaultTrue()
                        ->end()
                        ->scalarNode('standard')
                            ->defaultValue('PSR2')
                        ->end()
                        ->arrayNode('extensions')
                            ->defaultValue(array('php'))
                            ->prototype('scalar')
                            ->end()
                        ->end()
                        ->arrayNode('ignores')
                            ->defaultValue(array())
                            ->prototype('scalar')
                            ->end()
                        ->end()
                        ->arrayNode('locations')
                            ->defaultValue(array('%kernel.root_dir%/../src'))
                            ->prototype('scalar')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
