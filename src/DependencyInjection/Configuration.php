<?php


namespace Apipa169\DataTablesCompatibilityBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('data_tables_compatibility');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('mapping')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('from')->end()
                            ->scalarNode('to')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}