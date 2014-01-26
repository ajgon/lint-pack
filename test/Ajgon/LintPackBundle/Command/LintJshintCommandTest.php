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
        $this->command = new LintJshintCommand();
        parent::setUp();
    }

    public function testIfCommandHasGoodName()
    {
        $this->assertEquals('lint:jshint', $this->command->getName());
    }

    public function testIfProperCommandIsBuilt()
    {
        $this->assertEquals(
            $this->getProperCommand($this->getTestConfig()),
            $this->command->getCommand()
        );
    }

    public function testIfProperCommandIsBuiltForDefaults()
    {
        $this->command = new LintJshintCommand();
        $this->initWithoutConfig();

        $this->assertEquals(
            $this->getProperCommand(
                $this->getDefaultConfig(),
                $this->getValidFiles('/\.js$/')
            ),
            $this->command->getCommand()
        );
    }

    public function testIfProcessExecutesCorrectly()
    {
        $config = $this->getTestConfig();
        $config['lint_pack']['jshint']['bin'] = 'true';

        list($returnValue, $output) = $this->executeClassWithConfig($config);

        $this->assertEquals(0, $returnValue);
        $this->assertEquals($this->getProperCommand($config) . "\nDone, without errors.\n\n", $output->fetch());
    }

    public function testIfProcessFailsCorrectly()
    {
        $config = $this->getTestConfig();
        $config['lint_pack']['jshint']['bin'] = 'false';

        list($returnValue, $output) = $this->executeClassWithConfig($config);

        $this->assertEquals(1, $returnValue);
        $this->assertEquals($this->getProperCommand($config) . "\nCommand failed.\n\n", $output->fetch());
    }

    public function testConfigurationWithEmptyBin()
    {
        $this->setExpectedException(
            'Symfony\Component\Config\Definition\Exception\InvalidConfigurationException',
            'The path "lint_pack.jshint.bin" cannot contain an empty value, but got null.',
            0
        );

        $config = $this->getTestConfig();
        $config['lint_pack']['jshint']['bin'] = null;
        $this->initWithConfig($config);
    }

    public function testEmptyConfiguration()
    {
        $config = $this->getEmptyTestConfig();

        $this->initWithConfig($config);
        $this->assertEquals($this->getProperCommand($config), $this->command->getCommand());
    }

    private function getProperCommand($config, $goodFiles = null)
    {
        if (is_null($goodFiles)) {
            $goodFiles = $this->getValidFiles('/(?:.*file\.j.*)|(?:fixtures.jshint.jquery)/');
        }
        $jshintConfig = $config['lint_pack']['jshint'];

        return $jshintConfig['bin'] .
               (isset($jshintConfig['jshintrc']) ? ' --config ' . $jshintConfig['jshintrc'] : '') .
               ($jshintConfig['locations'] ? ' ' . implode(' ', $goodFiles) : '');
    }

    private function executeClassWithConfig($config)
    {
        $this->initWithConfig($config);

        $input = new ArrayInput(array());
        $output = new BufferedOutput();

        $returnValue = $this->command->execute($input, $output);
        return array($returnValue, $output);
    }

    private function getValidFiles($match = '/.*/')
    {
        $goodFiles = array();
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                TESTS_PATH . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'jshint'
            ),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $file) {
            $file = (string)$file;
            if (preg_match($match, $file)) {
                $goodFiles[] = $file;
            }
        }

        return $goodFiles;
    }
}
