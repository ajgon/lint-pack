<?php
namespace Ajgon\LintPackBundle\DependencyInjection;

use Ajgon\LintPackBundle\Test\LintPackTestCase;

class LintPackExtensionTest extends LintPackTestCase
{
    public function setUp()
    {
        $this->extension = new LintPackExtension();
    }

    public function testIfJshintValuesWereLoadedToContainer()
    {
        $jshintConfig = $this->getParameterConfig('lint_pack.jshint');

        $this->assertEquals('test-jshint', $jshintConfig['bin']);
        $this->assertEquals('/tmp/.jshintrc', $jshintConfig['jshintrc']);
        $this->assertEquals(array('js', 'javascript'), $jshintConfig['extensions']);
        $this->assertEquals(array('@r.js$@', '@/s[^/]+/jquery.js@'), $jshintConfig['ignores']);
        $this->assertEquals(array('%kernel.root_dir%/../test/fixtures/jshint'), $jshintConfig['locations']);
    }

    public function testIfCsslintValuesWereLoadedToContainer()
    {
        $csslintConfig = $this->getParameterConfig('lint_pack.csslint');

        $this->assertEquals('test-csslint', $csslintConfig['bin']);
        $this->assertEquals(array('adjoining-classes', 'box-sizing'), $csslintConfig['disable_rules']);
        $this->assertEquals(array('ignore.css'), $csslintConfig['ignores']);
        $this->assertEquals(array('%kernel.root_dir%/../test/fixtures/csslint'), $csslintConfig['locations']);
    }

    public function testIfPhpcsValuesWereLoadedToContainer()
    {
        $phpcsConfig = $this->getParameterConfig('lint_pack.phpcs');

        $this->assertEquals('vendor/bin/phpcs', $phpcsConfig['bin']);
        $this->assertFalse($phpcsConfig['warnings']);
        $this->assertFalse($phpcsConfig['recursion']);
        $this->assertEquals('PEAR', $phpcsConfig['standard']);
        $this->assertEquals(array('php', 'php5'), $phpcsConfig['extensions']);
        $this->assertEquals(array('ignore.php'), $phpcsConfig['ignores']);
        $this->assertEquals(array('%kernel.root_dir%/../test/fixtures/phpcs'), $phpcsConfig['locations']);
    }

    public function testIfPhpmdValuesWereLoadedToContainer()
    {
        $phpmdConfig = $this->getParameterConfig('lint_pack.phpmd');

        $this->assertEquals('vendor/bin/phpmd', $phpmdConfig['bin']);
        $this->assertEquals(
            array('naming', 'controversial'),
            $phpmdConfig['rulesets']
        );
        $this->assertEquals(array('php', 'php5'), $phpmdConfig['extensions']);
        $this->assertEquals(array(), $phpmdConfig['ignores']);
        $this->assertEquals(array('%kernel.root_dir%/../test/fixtures/phpmd/good'), $phpmdConfig['locations']);
    }

    public function testIfPhpcpdValuesWereLoadedToContainer()
    {
        $phpcpdConfig = $this->getParameterConfig('lint_pack.phpcpd');

        $this->assertEquals('vendor/bin/phpcpd', $phpcpdConfig['bin']);
        $this->assertEquals('4', $phpcpdConfig['min_lines']);
        $this->assertEquals('60', $phpcpdConfig['min_tokens']);
        $this->assertEquals(array('php', 'php5'), $phpcpdConfig['extensions']);
        $this->assertEquals(array('ignore.php', 'BadFile.php'), $phpcpdConfig['ignores']);
        $this->assertEquals(array('%kernel.root_dir%/../test/fixtures/phpcpd'), $phpcpdConfig['locations']);
    }

    public function testIfTwigValuesWereLoadedToContainer()
    {
        $twigConfig = $this->getParameterConfig('lint_pack.twig');

        $this->assertEquals(array('@ignore.twig@', '@bad.twig@'), $twigConfig['ignores']);
        $this->assertEquals(array('%kernel.root_dir%/../test/fixtures/twig'), $twigConfig['locations']);
    }

    public function testIfAllLintersAreEnabled()
    {
        $enabledConfig = $this->getParameterConfig('lint_pack');

        $this->assertTrue($enabledConfig['jshint']);
        $this->assertTrue($enabledConfig['phpcpd']);
        $this->assertTrue($enabledConfig['phpcs']);
        $this->assertTrue($enabledConfig['phpmd']);
        $this->assertTrue($enabledConfig['twig']);
    }

    private function getParameterConfig($name)
    {
        $container = $this->getContainerBuilder();
        $this->loadConfigToContainer($container, null, false);
        return $container->getParameter($name);
    }
}
