<?php

namespace Soluti\SogenactifBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('soluti_sogenactif');

        $rootNode
            ->children()
                ->scalarNode('request_bin')->isRequired()->end()
                ->scalarNode('response_bin')->isRequired()->end()
                ->arrayNode('settings')
                    ->children()
                        ->scalarNode('pathfile')->isRequired()->end()
                        ->scalarNode('merchant_id')->isRequired()->end()
                        ->scalarNode('merchant_country')->isRequired()->end()
                        ->scalarNode('logo_id')->end()
                        ->scalarNode('payment_means')->end()
                        ->scalarNode('data')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
