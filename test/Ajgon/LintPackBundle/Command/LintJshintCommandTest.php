<?php
namespace Ajgon\LintPackBundle\Command;

use Ajgon\LintPackBundle\Test\LintPackTestCase;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Yaml\Yaml;

class LintJshintCommandTest extends LintPackTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->initWithConfig();
    }

    public function testIfCommandHasGoodName()
    {
        $this->assertEquals('lint:jshint', $this->command->getName());
    }

    public function testIfProperCommandIsBuilt()
    {
        $this->assertEquals($this->getProperCommand(), $this->command->getCommand());
    }

    public function testIfProcessExecutesCorrectly()
    {
        $config = $this->getTestConfig();
        $config['ajgon_lintpack']['jshint']['bin'] = 'true';

        list($returnValue, $output) = $this->executeClassWithConfig($config);

        $this->assertEquals(0, $returnValue);
        $this->assertEquals($this->getProperCommand($config) . "\nDone, without errors.\n\n", $output->fetch());
    }

    public function testIfProcessFailsCorrectly()
    {
        $config = $this->getTestConfig();
        $config['ajgon_lintpack']['jshint']['bin'] = 'false';

        list($returnValue, $output) = $this->executeClassWithConfig($config);

        $this->assertEquals(1, $returnValue);
        $this->assertEquals($this->getProperCommand($config) . "\nCommand failed.\n\n", $output->fetch());
    }

    private function initWithConfig($config = null)
    {
        $this->command = new LintJshintCommand();
        $container = $this->getContainerBuilder();
        $this->loadConfigToContainer($container, $config, true);
        $this->command->setContainer($container);
    }

    private function getProperCommand($config = null)
    {
        if (!$config) {
            $config = $this->getTestConfig();
        }

        $jshintConfig = $config['ajgon_lintpack']['jshint'];

        $goodFiles = array(
            TESTS_PATH . '/fixtures/file.javascript',
            TESTS_PATH . '/fixtures/file.js',
            TESTS_PATH . '/fixtures/jquery.js',
            TESTS_PATH . '/fixtures/subdir/file.javascript',
            TESTS_PATH . '/fixtures/subdir/file.js',
            TESTS_PATH . '/fixtures/subdir/r.js'
        );

        return $jshintConfig['bin'] . ' --config ' . $jshintConfig['jshintrc'] . ' ' . implode(' ', $goodFiles);
    }

    private function executeClassWithConfig($config)
    {
        $this->initWithConfig($config);

        $input = new ArrayInput(array());
        $output = new BufferedOutput();

        $returnValue = $this->command->execute($input, $output);
        return array($returnValue, $output);
    }
}
