<?php
namespace Ajgon\LintPackBundle\Command;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Ajgon\LintPackBundle\Test\LintPackTestCase;

class LintJshintCommandTest extends LintPackTestCase
{
    public function setUp()
    {
        $this->command = new LintJshintCommand();
        parent::setUp();
    }

    public function testJshintIfCommandHasGoodName()
    {
        $this->assertEquals('lint:jshint', $this->command->getName());
    }

    public function testJshintIfDoesntLaunchWhenDisabled()
    {
        $this->assertDisabledConfig('jshint');
    }

    public function testJshintIfProperCommandIsBuiltWithoutIgnore()
    {
        $this->command = new LintJshintCommand();
        $config = $this->getTestConfig();
        unset($config['lint_pack']['jshint']['jshintignore']);
        $this->initWithConfig($config);

        $this->assertEquals(
            $this->getProperCommand($config),
            $this->command->getCommand()
        );
    }

    public function testJshintIfProperComandIsBuiltWithIgnore()
    {
        $this->assertEquals(
            $this->getProperCommand($this->getTestConfig()),
            $this->command->getCommand()
        );
    }

    public function testJshintIfProperCommandIsBuiltForDefaults()
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

    public function testJshintIfProcessExecutesCorrectly()
    {
        $config = $this->getTestConfig();
        $config['lint_pack']['jshint']['bin'] = 'true';

        list($returnValue, $output) = $this->executeClassWithConfig($config);

        $this->assertEquals(0, $returnValue);
        $this->assertEquals($this->getProperCommand($config) . "\nDone, without errors.\n\n", $output->fetch());
    }

    public function testJshintIfProcessFailsCorrectly()
    {
        $config = $this->getTestConfig();
        $config['lint_pack']['jshint']['bin'] = 'false';

        list($returnValue, $output) = $this->executeClassWithConfig($config);

        $this->assertEquals(1, $returnValue);
        $this->assertEquals($this->getProperCommand($config) . "\nCommand failed.\n\n", $output->fetch());
    }

    public function testJshintConfigurationWithEmptyBin()
    {
        $this->assertEmptyConfigParameter('jshint', 'bin', false);
    }

    public function testJshintConfigurationWithEmptyLocations()
    {
        $this->assertEmptyConfigParameter('jshint', 'locations', true);
    }

    public function testJshintEmptyConfiguration()
    {
        $config = $this->getEmptyTestConfig();

        $this->initWithConfig($config);
        $this->assertEquals(
            $this->getProperCommand($config, $this->getValidFiles('/\.[^.]+/')),
            $this->command->getCommand()
        );
    }

    private function getProperCommand($config, $goodFiles = null)
    {
        $jshintConfig = $config['lint_pack']['jshint'];
        $jshintConfig['locations'] = $this->parseConfigDirs($jshintConfig['locations']);

        if (isset($jshintConfig['jshintignore'])) {
            return $this->getProperCommandWithIgnore($jshintConfig);
        }
        return $this->getProperCommandWithoutIgnore($jshintConfig, $goodFiles);
    }

    private function getProperCommandWithIgnore($jshintConfig)
    {
        return $jshintConfig['bin'] .
               (isset($jshintConfig['jshintrc']) ? ' --config ' . $jshintConfig['jshintrc'] : '') .
               ' --exclude-path ' . $jshintConfig['jshintignore'] .
               ' --extra-ext ' . implode(',', $jshintConfig['extensions']) .
               ($jshintConfig['locations'] ? ' ' . implode(' ', $jshintConfig['locations']) : '');
    }

    private function getProperCommandWithoutIgnore($jshintConfig, $goodFiles = null)
    {
        if (is_null($goodFiles)) {
            $goodFiles = $this->getValidFiles('/(?:.*file\.j.*)|(?:fixtures.jshint.jquery)/');
        }

        return $jshintConfig['bin'] .
               (isset($jshintConfig['jshintrc']) ? ' --config ' . $jshintConfig['jshintrc'] : '') .
               ($jshintConfig['locations'] ? ' ' . implode(' ', $goodFiles) : '');
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
