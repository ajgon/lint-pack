<?php
namespace Ajgon\LintPackBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Ajgon\LintPackBundle\Test\LintPackTestCase;

/**
 * {@inheritDoc}
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class ConfigurationTest extends LintPackTestCase
{
    private $processor;
    private $config;

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
        $this->assertFalse(isset($configValues['jshint']['jshintrc']));
        $this->assertFalse(isset($configValues['jshint']['jshintignore']));
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
        $config = $this->getTestConfig();
        $configValues = $this->processor->processConfiguration($this->config, $config);

        $this->assertTrue($configValues['jshint']['enabled']);
        $this->assertEquals('test-jshint', $configValues['jshint']['bin']);
        $this->assertEquals('/tmp/.jshintrc', $configValues['jshint']['jshintrc']);
        $this->assertEquals('/tmp/.jshintignore', $configValues['jshint']['jshintignore']);
        $this->assertEquals(array('js', 'javascript'), $configValues['jshint']['extensions']);
        $this->assertEquals(array('@r.js$@', '@/s[^/]+/jquery.js@'), $configValues['jshint']['ignores']);
        $this->assertEquals(array('%kernel.root_dir%/../test/fixtures/jshint'), $configValues['jshint']['locations']);
    }

    public function testIfCsslintConfigContainsDefaultValues()
    {
        $configValues = $this->processor->processConfiguration($this->config, array());

        $this->assertFalse($configValues['csslint']['enabled']);
        $this->assertEquals('csslint', $configValues['csslint']['bin']);
        $this->assertEquals(array(), $configValues['csslint']['disable_rules']);
        $this->assertEquals(array(), $configValues['csslint']['ignores']);
        $this->assertEquals(
            array(
                '%kernel.root_dir%',
                '%kernel.root_dir%/../src'
            ),
            $configValues['csslint']['locations']
        );
    }

    public function testIfCsslintConfigContainsCustomValues()
    {
        $config = $this->getTestConfig();
        $configValues = $this->processor->processConfiguration($this->config, $config);

        $this->assertTrue($configValues['csslint']['enabled']);
        $this->assertEquals('test-csslint', $configValues['csslint']['bin']);
        $this->assertEquals(array('adjoining-classes', 'box-sizing'), $configValues['csslint']['disable_rules']);
        $this->assertEquals(array('ignore.css'), $configValues['csslint']['ignores']);
        $this->assertEquals(array('%kernel.root_dir%/../test/fixtures/csslint'), $configValues['csslint']['locations']);
    }

    public function testIfPhpcsConfigContainsDefaultValues()
    {
        $configValues = $this->processor->processConfiguration($this->config, array());

        $this->assertFalse($configValues['phpcs']['enabled']);
        $this->assertEquals('phpcs', $configValues['phpcs']['bin']);
        $this->assertFalse($configValues['phpcs']['warnings']);
        $this->assertTrue($configValues['phpcs']['recursion']);
        $this->assertEquals('PSR2', $configValues['phpcs']['standard']);
        $this->assertEquals(array('php'), $configValues['phpcs']['extensions']);
        $this->assertEquals(array(), $configValues['phpcs']['ignores']);
        $this->assertEquals(array('%kernel.root_dir%/../src'), $configValues['phpcs']['locations']);
    }

    public function testIfPhpcsConfigContainsCustomValues()
    {
        $config = $this->getTestConfig();
        $configValues = $this->processor->processConfiguration($this->config, $config);

        $this->assertTrue($configValues['phpcs']['enabled']);
        $this->assertEquals('vendor/bin/phpcs', $configValues['phpcs']['bin']);
        $this->assertFalse($configValues['phpcs']['warnings']);
        $this->assertFalse($configValues['phpcs']['recursion']);
        $this->assertEquals('PEAR', $configValues['phpcs']['standard']);
        $this->assertEquals(array('php', 'php5'), $configValues['phpcs']['extensions']);
        $this->assertEquals(array('ignore.php'), $configValues['phpcs']['ignores']);
        $this->assertEquals(array('%kernel.root_dir%/../test/fixtures/phpcs'), $configValues['phpcs']['locations']);
    }

    public function testIfPhpmdConfigContainsDefaultValues()
    {
        $configValues = $this->processor->processConfiguration($this->config, array());

        $this->assertFalse($configValues['phpmd']['enabled']);
        $this->assertEquals('phpmd', $configValues['phpmd']['bin']);
        $this->assertEquals(
            array('codesize', 'controversial', 'design', 'naming', 'unusedcode'),
            $configValues['phpmd']['rulesets']
        );
        $this->assertEquals(array('php'), $configValues['phpmd']['extensions']);
        $this->assertEquals(array(), $configValues['phpmd']['ignores']);
        $this->assertEquals(array('%kernel.root_dir%/../src'), $configValues['phpmd']['locations']);
    }

    public function testIfPhpmdConfigContainsCustomValues()
    {
        $config = $this->getTestConfig();
        $configValues = $this->processor->processConfiguration($this->config, $config);

        $this->assertTrue($configValues['phpmd']['enabled']);
        $this->assertEquals('vendor/bin/phpmd', $configValues['phpmd']['bin']);
        $this->assertEquals(
            array('naming', 'controversial'),
            $configValues['phpmd']['rulesets']
        );
        $this->assertEquals(array('php', 'php5'), $configValues['phpmd']['extensions']);
        $this->assertEquals(array(), $configValues['phpmd']['ignores']);
        $this->assertEquals(
            array('%kernel.root_dir%/../test/fixtures/phpmd/good'),
            $configValues['phpmd']['locations']
        );
    }

    public function testIfPhpcpdConfigContainsDefaultValues()
    {
        $configValues = $this->processor->processConfiguration($this->config, array());

        $this->assertFalse($configValues['phpcpd']['enabled']);
        $this->assertEquals('phpcpd', $configValues['phpcpd']['bin']);
        $this->assertEquals('5', $configValues['phpcpd']['min_lines']);
        $this->assertEquals('70', $configValues['phpcpd']['min_tokens']);
        $this->assertEquals(array('php'), $configValues['phpcpd']['extensions']);
        $this->assertEquals(array(), $configValues['phpcpd']['ignores']);
        $this->assertEquals(array('%kernel.root_dir%/../src'), $configValues['phpcpd']['locations']);
    }

    public function testIfPhpcpdConfigContainsCustomValues()
    {
        $config = $this->getTestConfig();
        $configValues = $this->processor->processConfiguration($this->config, $config);

        $this->assertTrue($configValues['phpcpd']['enabled']);
        $this->assertEquals('vendor/bin/phpcpd', $configValues['phpcpd']['bin']);
        $this->assertEquals('4', $configValues['phpcpd']['min_lines']);
        $this->assertEquals('60', $configValues['phpcpd']['min_tokens']);
        $this->assertEquals(array('php', 'php5'), $configValues['phpcpd']['extensions']);
        $this->assertEquals(array('ignore.php', 'BadFile.php'), $configValues['phpcpd']['ignores']);
        $this->assertEquals(array('%kernel.root_dir%/../test/fixtures/phpcpd'), $configValues['phpcpd']['locations']);
    }

    public function testIfTwigConfigContainsDefaultValues()
    {
        $configValues = $this->processor->processConfiguration($this->config, array());

        $this->assertFalse($configValues['twig']['enabled']);
        $this->assertEquals(array(), $configValues['twig']['ignores']);
        $this->assertEquals(array('%kernel.root_dir%', '%kernel.root_dir%/../src'), $configValues['twig']['locations']);
    }

    public function testIfTwigConfigContainsCustomValues()
    {
        $config = $this->getTestConfig();
        $configValues = $this->processor->processConfiguration($this->config, $config);

        $this->assertTrue($configValues['twig']['enabled']);
        $this->assertEquals(array('@ignore.twig@', '@bad.twig@'), $configValues['twig']['ignores']);
        $this->assertEquals(array('%kernel.root_dir%/../test/fixtures/twig'), $configValues['twig']['locations']);
    }

    public function testIfItWorksInLegacySymfony()
    {
        \Ajgon\LintPackBundle\BackwardCompatibility\NodeBuilder::$forceHack = true;

        $config = $this->getTestConfig();
        $configValues = $this->processor->processConfiguration($this->config, $config);

        $this->assertTrue($configValues['twig']['enabled']);
        $this->assertEquals(array('@ignore.twig@', '@bad.twig@'), $configValues['twig']['ignores']);
        $this->assertEquals(array('%kernel.root_dir%/../test/fixtures/twig'), $configValues['twig']['locations']);
    }
}
