<?php
namespace Ajgon\LintPackBundle\BackwardCompatibility;

class ArrayNodeDefinition extends \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
{
    /**
     * Adds an "enabled" boolean to enable the current section.
     * Take from Symfony 2.4
     *
     * By default, the section is disabled. If any configuration is specified then
     * the node will be automatically enabled:
     *
     * enableableArrayNode: {enabled: true, ...}   # The config is enabled & default values get overridden
     * enableableArrayNode: ~                      # The config is enabled & use the default values
     * enableableArrayNode: true                   # The config is enabled & use the default values
     * enableableArrayNode: {other: value, ...}    # The config is enabled & default values get overridden
     * enableableArrayNode: {enabled: false, ...}  # The config is disabled
     * enableableArrayNode: false                  # The config is disabled
     *
     * @return ArrayNodeDefinition
     */
    public function canBeEnabled()
    {
        $this
            ->addDefaultsIfNotSet()
            ->treatFalseLike(array('enabled' => false))
            ->treatTrueLike(array('enabled' => true))
            ->treatNullLike(array('enabled' => true))
            ->beforeNormalization()
                ->ifArray()
                ->then(function ($v) {
                    $v['enabled'] = isset($v['enabled']) ? $v['enabled'] : true;
                    return $v;
                })
            ->end()
            ->children()
                ->booleanNode('enabled')
                    ->defaultFalse()
        ;
        return $this;
    }
}
