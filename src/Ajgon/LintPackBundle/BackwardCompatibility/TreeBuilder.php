<?php
namespace Ajgon\LintPackBundle\BackwardCompatibility;

class TreeBuilder extends \Symfony\Component\Config\Definition\Builder\TreeBuilder
{
    public function root(
        $name,
        $type = 'array',
        \Symfony\Component\Config\Definition\Builder\NodeBuilder $builder = null
    ) {
        $builder = $builder ?: new NodeBuilder();

        return $this->root = $builder->node($name, $type)->setParent($this);
    }
}
