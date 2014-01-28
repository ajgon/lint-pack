<?php

namespace Ajgon\LintPackBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class LintPackExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('lint_pack.jshint', $config['jshint']);
        $container->setParameter('lint_pack.csslint', $config['csslint']);
        $container->setParameter('lint_pack.phpcs', $config['phpcs']);
        $container->setParameter('lint_pack.phpmd', $config['phpmd']);
        $container->setParameter('lint_pack.phpcpd', $config['phpcpd']);
        $container->setParameter('lint_pack.twig', $config['twig']);
        $container->setParameter('lint_pack', $this->determineEnabledLinters($config));
    }

    private function determineEnabledLinters($config)
    {
        $enabledList = array();
        foreach ($config as $linter => $options) {
            $enabledList[$linter] = !!$options['enabled'];
        }

        return $enabledList;
    }
}
