<?php
namespace Ajgon\LintPackBundle\Test;

use PHPUnit_Framework_TestCase;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;

use Ajgon\LintPackBundle\DependencyInjection\LintPackExtension;

class LintPackTestCase extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->extension = new LintPackExtension();
    }

    protected function loadConfigToContainer(&$container, $config = null, $parseDir = false)
    {
        if (!$config) {
            $config = $this->getTestConfig();
        }

        if ($parseDir) {
            $config['ajgon_lintpack']['jshint']['locations'] =
                $this->parseConfigDirs($config['ajgon_lintpack']['jshint']['locations']);
        }
        $this->extension->load($config, $container);
    }

    private function parseConfigDirs($dirs)
    {
        $results = array();

        foreach ($dirs as $dir) {
            $results[] = realpath(str_replace('%kernel.root_dir%', TESTS_PATH, $dir));
        }

        return $results;
    }

    protected function getContainerBuilder()
    {
        $container = new ContainerBuilder();

        $bundles = array(
            'LintPackBundle' => 'Ajgon\LintPackBundle\LintPackBundle',
        );

        $container->setParameter('kernel.bundles', $bundles);

        return $container;
    }

    protected function getTestConfig()
    {
        return Yaml::parse(TESTS_PATH.'/fixtures/config.yml');
    }


}
