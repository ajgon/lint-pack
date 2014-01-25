<?php
namespace Ajgon\LintPackBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ajgon_lintpack');

        $rootNode
            ->children()
                ->arrayNode('jshint')
                ->canBeEnabled()
                ->children()
                    ->scalarNode('bin')
                        ->defaultValue('jshint')
                    ->end()
                    ->scalarNode('jshintrc')
                        ->defaultValue('%kernel.root_dir%/.jshintrc')
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
                        ->defaultValue(array('%kernel.root_dir%/app', '%kernel.root_dir%/src'))
                        ->prototype('scalar')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
