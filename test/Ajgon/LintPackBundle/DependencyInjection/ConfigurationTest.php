<?php
namespace Ajgon\LintPackBundle\DependencyInjection;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\Definition\Processor;

use Ajgon\LintPackBundle\Test\LintPackTestCase;

class ConfigurationTest extends LintPackTestCase
{
    public function setUp()
    {
        $this->processor = new Processor();
        $this->config = new Configuration();
    }
    public function testIfJshintConfigContainsDefaultValues()
    {
        $configValues = $this->processor->processConfiguration($this->config, array());
        $this->assertFalse($configValues['jshint']['enabled']);
        $this->assertEquals('jshint', $configValues['jshint']['bin']);
        $this->assertEquals('%kernel.root_dir%/../.jshintrc', $configValues['jshint']['jshintrc']);
        $this->assertEquals(array('js'), $configValues['jshint']['extensions']);
        $this->assertEquals(array(), $configValues['jshint']['ignores']);
        $this->assertEquals(
            array(
                '%kernel.root_dir%',
                '%kernel.root_dir%/../src'
            ),
            $configValues['jshint']['locations']
        );
    }

    public function testIfJshintConfigContainsCustomValues()
    {
        $configFromYaml = Yaml::parse(TESTS_PATH.'/fixtures/config.yml');
        $configValues = $this->processor->processConfiguration($this->config, $configFromYaml);

        $this->assertTrue($configValues['jshint']['enabled']);
        $this->assertEquals('test-jshint', $configValues['jshint']['bin']);
        $this->assertEquals('/tmp/.jshintrc', $configValues['jshint']['jshintrc']);
        $this->assertEquals(array('js', 'javascript'), $configValues['jshint']['extensions']);
        $this->assertEquals(array('@r.js$@', '@s[^/]+/jquery.js@'), $configValues['jshint']['ignores']);
        $this->assertEquals(array('%kernel.root_dir%/../test/fixtures'), $configValues['jshint']['locations']);
    }
}
