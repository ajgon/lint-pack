<?php
namespace Ajgon\LintPackBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;

class LintPackExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->extension = new LintPackExtension();
    }

    public function testIfJshintValuesWereLoadedToContainer()
    {
        $config = Yaml::parse(__DIR__.'/../../../fixtures/config.yml');
        $container = $this->getContainerBuilder();
        $this->extension->load($config, $container);
        $jshintConfig = $container->getParameter('ajgon_lintpack.jshint');

        $this->assertEquals('/tmp/.jshintrc', $jshintConfig['jshintrc']);
        $this->assertEquals(array('js', 'javascript'), $jshintConfig['extensions']);
        $this->assertEquals(array('r.js', 'jquery.js'), $jshintConfig['ignores']);
        $this->assertEquals(
            array(
                '%kernel.root_dir%/app',
                '%kernel.root_dir%/src',
                '%kernel.root_dir%/test'
            ),
            $jshintConfig['locations']
        );
    }

    private function getContainerBuilder()
    {
        $container = new ContainerBuilder();

        $bundles = array(
            'LintPackBundle' => 'Ajgon\LintPackBundle\LintPackBundle',
        );

        $container->setParameter('kernel.bundles', $bundles);

        return $container;
    }
}
