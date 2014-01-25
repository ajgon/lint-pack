<?php
namespace Ajgon\LintPackBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;

use Ajgon\LintPackBundle\Test\LintPackTestCase;

class LintPackExtensionTest extends LintPackTestCase
{
    public function testIfJshintValuesWereLoadedToContainer()
    {
        $container = $this->getContainerBuilder();
        $this->loadConfigToContainer($container);
        $jshintConfig = $container->getParameter('ajgon_lintpack.jshint');

        $this->assertEquals('test-jshint', $jshintConfig['bin']);
        $this->assertEquals('/tmp/.jshintrc', $jshintConfig['jshintrc']);
        $this->assertEquals(array('js', 'javascript'), $jshintConfig['extensions']);
        $this->assertEquals(array('r.js', '*/jquery.js'), $jshintConfig['ignores']);
        $this->assertEquals(array('%kernel.root_dir%/../test/fixtures'), $jshintConfig['locations']);
    }
}
