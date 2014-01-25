<?php
namespace Ajgon\LintPackBundle\Command;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Yaml\Yaml;

use Ajgon\LintPackBundle\Test\LintPackTestCase;

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
        $goodFiles = $this->getValidFiles();

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

    private function getValidFiles()
    {
        $goodFiles = array();
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(TESTS_PATH . DIRECTORY_SEPARATOR . 'fixtures'),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $file) {
            $file = (string)$file;
            if (preg_match('/(?:.*file\.j.*)|(?:fixtures.jquery)|(?:subdir.r)/', $file)) {
                $goodFiles[] = $file;
            }
        }

        return $goodFiles;
    }
}
