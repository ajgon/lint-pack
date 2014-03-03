<?php
namespace Ajgon\LintPackBundle\Command;

use Ajgon\LintPackBundle\Test\LintPackTestCase;

class LintPhpcpdCommandTest extends LintPackTestCase
{
    public function setUp()
    {
        $this->command = new LintPhpcpdCommand();
        parent::setUp();
    }

    public function testIfCommandHasGoodName()
    {
        $this->assertEquals('lint:phpcpd', $this->command->getName());
    }

    public function testConfigurationWithEmptyBin()
    {
        $this->assertEmptyConfigParameter('phpcpd', 'bin', false);
    }

    public function testConfigurationWithEmptyLocations()
    {
        $this->assertEmptyConfigParameter('phpcpd', 'locations', true);
    }

    public function testIfDoesntLaunchWhenDisabled()
    {
        $this->assertDisabledConfig('phpcpd');
    }

    public function testIfProperCommandIsBuilt()
    {
        $this->assertEquals(
            $this->getProperCommand($this->getTestConfig()),
            $this->command->getCommand()
        );
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

    public function testIfProperCommandIsBuiltForDefaults()
    {
        $this->command = new LintPhpcpdCommand();
        $this->initWithoutConfig();

        $this->assertEquals(
            $this->getProperCommand($this->getDefaultConfig()),
            $this->command->getCommand()
        );
    }

    public function testIfProcessExecutesCorrectly()
    {
        $config = $this->getTestConfig();
        $config['lint_pack']['phpcpd']['locations'] =
            $this->parseConfigDirs($config['lint_pack']['phpcpd']['locations']);

        list($returnValue, $output) = $this->executeClassWithConfig($config);
        $result = $output->fetch();

        $this->assertEquals(0, $returnValue);
        $this->assertContains($this->getProperCommand($config), $result);
        $this->assertContains('Done, without errors.', $result);
    }

    public function testIfProcessFailsCorrectly()
    {
        $config = $this->getTestConfig();
        $config['lint_pack']['phpcpd']['locations'] =
            $this->parseConfigDirs($config['lint_pack']['phpcpd']['locations']);
        $config['lint_pack']['phpcpd']['ignores'] =
            array('ignore.php', 'GoodFile.php');

        list($returnValue, $output) = $this->executeClassWithConfig($config);
        $result = $output->fetch();

        $this->assertEquals(1, $returnValue);
        $this->assertContains($this->getProperCommand($config), $result);
        $this->assertContains('duplicated lines', $result);
        $this->assertContains('Command failed.', $result);
    }

    private function getProperCommand($config)
    {
        $config['lint_pack']['phpcpd']['locations'] =
            $this->parseConfigDirs($config['lint_pack']['phpcpd']['locations']);

        return $config['lint_pack']['phpcpd']['bin'] .
               ' --progress' .
               (
                   $config['lint_pack']['phpcpd']['min_lines'] ?
                   ' --min-lines=' . $config['lint_pack']['phpcpd']['min_lines'] :
                   ''
               ) .
               (
                   $config['lint_pack']['phpcpd']['min_tokens'] ?
                   ' --min-tokens=' . $config['lint_pack']['phpcpd']['min_tokens'] :
                   ''
               ) .
               (
                   $config['lint_pack']['phpcpd']['extensions'] ?
                   ' --names=\\*.' . implode(',\\*.', $config['lint_pack']['phpcpd']['extensions']) :
                   ''
               ) .
               (
                   $config['lint_pack']['phpcpd']['ignores'] ?
                   ' --names-exclude=' . implode(',', $config['lint_pack']['phpcpd']['ignores']) .
                   ' --exclude=' . implode(',', $config['lint_pack']['phpcpd']['ignores']) :
                   ''
               ) .
               ' ' . implode(',', $config['lint_pack']['phpcpd']['locations']);
    }
}
