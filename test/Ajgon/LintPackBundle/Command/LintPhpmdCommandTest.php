<?php
namespace Ajgon\LintPackBundle\Command;

use Ajgon\LintPackBundle\Test\LintPackTestCase;

class LintPhpmdCommandTest extends LintPackTestCase
{
    public function setUp()
    {
        $this->command = new LintPhpmdCommand();
        parent::setUp();
    }

    public function testPhpmdIfCommandHasGoodName()
    {
        $this->assertEquals('lint:phpmd', $this->command->getName());
    }

    public function testPhpmdConfigurationWithEmptyBin()
    {
        $this->assertEmptyConfigParameter('phpmd', 'bin', false);
    }

    public function testPhpmdConfigurationWithEmptyLocations()
    {
        $this->assertEmptyConfigParameter('phpmd', 'locations', true);
    }

    public function testPhpmdIfDoesntLaunchWhenDisabled()
    {
        $this->assertDisabledConfig('phpmd');
    }

    public function testPhpmdIfProperCommandIsBuilt()
    {
        $this->assertEquals(
            $this->getProperCommand($this->getTestConfig()),
            $this->command->getCommand()
        );
    }

    public function testPhpmdEmptyConfiguration()
    {
        $config = $this->getEmptyTestConfig();

        $this->initWithConfig($config);
        $this->assertEquals(
            $this->getProperCommand($config),
            $this->command->getCommand()
        );
    }

    public function testPhpmdIfProperCommandIsBuiltForDefaults()
    {
        $this->command = new LintPhpmdCommand();
        $this->initWithoutConfig();

        $this->assertEquals(
            $this->getProperCommand($this->getDefaultConfig()),
            $this->command->getCommand()
        );
    }

    public function testPhpmdIfProcessExecutesCorrectly()
    {
        $config = $this->getTestConfig();
        $config['lint_pack']['phpmd']['locations'] =
            $this->parseConfigDirs($config['lint_pack']['phpmd']['locations']);

        list($returnValue, $output) = $this->executeClassWithConfig($config);

        $this->assertEquals(0, $returnValue);
        $this->assertEquals($this->getProperCommand($config) . "\n\n\nDone, without errors.\n\n", $output->fetch());
    }

    public function testPhpmdIfProcessFailsCorrectly()
    {
        $config = $this->getTestConfig();
        $config['lint_pack']['phpmd']['locations'] =
            $this->parseConfigDirs(array('%kernel.root_dir%/../test/fixtures/phpmd/bad'));

        list($returnValue, $output) = $this->executeClassWithConfig($config);
        $result = $output->fetch();

        $this->assertEquals(2, $returnValue);
        $this->assertContains($this->getProperCommand($config), $result);
        $this->assertContains('Command failed.', $result);
    }

    private function getProperCommand($config)
    {
        $config['lint_pack']['phpmd']['locations'] =
            $this->parseConfigDirs($config['lint_pack']['phpmd']['locations']);

        return $config['lint_pack']['phpmd']['bin'] .
               ' ' . implode(DIRECTORY_SEPARATOR . '*,', $config['lint_pack']['phpmd']['locations']) .
               ' text' .
               ' ' . implode(',', $config['lint_pack']['phpmd']['rulesets']) .
               (
                   $config['lint_pack']['phpmd']['extensions'] ?
                   ' --suffixes=' . implode(',', $config['lint_pack']['phpmd']['extensions']) :
                   ''
               ) .
               (
                   $config['lint_pack']['phpmd']['ignores'] ?
                   ' --exclude ' . implode(',', $config['lint_pack']['phpmd']['ignores']) :
                   ''
               );
    }
}
