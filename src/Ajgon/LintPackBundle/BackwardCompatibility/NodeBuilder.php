<?php
namespace Ajgon\LintPackBundle\BackwardCompatibility;

class NodeBuilder extends \Symfony\Component\Config\Definition\Builder\NodeBuilder
{
    public static $forceHack = false;

    public function node($name, $type) {
        if ($type !== 'array' || (!self::$forceHack && method_exists('\Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition', 'canBeEnabled'))) {
            return parent::node($name, $type);
        }

        $node = new ArrayNodeDefinition($name);
        $this->append($node);
        return $node;
    }
}
