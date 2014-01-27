<?php
namespace Ajgon\LintPackBundle\Command;

use Ajgon\LintPackBundle\Test\LintPackTestCase;

class LintPhpcsCommandTest extends LintPackTestCase
{
    public function setUp()
    {
        $this->command = new LintPhpcsCommand();
        parent::setUp();
    }

    public function testIfCommandHasGoodName()
    {
        $this->assertEquals('lint:phpcs', $this->command->getName());
    }

    public function testConfigurationWithEmptyBin()
    {
        $this->assertEmptyConfigParameter('phpcs', 'bin', false);
    }

    public function testConfigurationWithEmptyLocations()
    {
        $this->assertEmptyConfigParameter('phpcs', 'locations', true);
    }

    public function testEmptyConfiguration()
    {
        $config = $this->getEmptyTestConfig();

        $this->initWithConfig($config);
        $this->assertEquals(
            $this->getProperCommand($config),
            $this->command->getCommand()
        );
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
        $this->command = new LintPhpcsCommand();
        $this->initWithoutConfig();

        $this->assertEquals(
            $this->getProperCommand($this->getDefaultConfig()),
            $this->command->getCommand()
        );
    }

    public function testIfProcessExecutesCorrectly()
    {
        $config = $this->getTestConfig();
        $config['lint_pack']['phpcs']['ignores'] = array('ignore.php', 'BadFile.php');
        $config['lint_pack']['phpcs']['locations'] =
            $this->parseConfigDirs($config['lint_pack']['phpcs']['locations']);

        list($returnValue, $output) = $this->executeClassWithConfig($config);

        $this->assertEquals(0, $returnValue);
        $this->assertEquals($this->getProperCommand($config) . "\n.\n\n\nDone, without errors.\n\n", $output->fetch());
    }

    public function testIfProcessFailsCorrectly()
    {
        $config = $this->getTestConfig();
        $config['lint_pack']['phpcs']['locations'] =
            $this->parseConfigDirs($config['lint_pack']['phpcs']['locations']);

        list($returnValue, $output) = $this->executeClassWithConfig($config);
        $result = $output->fetch();

        $this->assertEquals(1, $returnValue);
        $this->assertContains($this->getProperCommand($config), $result);
        $this->assertContains('Command failed.', $result);
        $this->assertContains('FOUND 1 ERROR(S) AFFECTING 1 LINE(S)', $result);
        $this->assertContains('25 | ERROR | Missing function doc comment', $result);
    }

    private function getProperCommand($config)
    {
        return $config['lint_pack']['phpcs']['bin'] .
               ' -p' .
               ($config['lint_pack']['phpcs']['warnings'] ? '' : ' -n') .
               ($config['lint_pack']['phpcs']['recursion'] ? '' : ' -l') .
               (
                   $config['lint_pack']['phpcs']['standard'] ?
                   ' --standard=' . $config['lint_pack']['phpcs']['standard'] :
                   ''
               ) .
               (
                   $config['lint_pack']['phpcs']['extensions'] ?
                   ' --extensions=' . implode(',', $config['lint_pack']['phpcs']['extensions']) :
                   ''
               ) .
               (
                   $config['lint_pack']['phpcs']['ignores'] ?
                   ' --ignore=' . implode(',', $config['lint_pack']['phpcs']['ignores']) :
                   ''
               ) .
               ' ' . implode(' ', $config['lint_pack']['phpcs']['locations']);
    }
}
