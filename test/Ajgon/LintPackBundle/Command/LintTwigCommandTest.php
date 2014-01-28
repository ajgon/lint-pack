<?php
namespace Ajgon\LintPackBundle\Command;

use Symfony\Component\Console\Application;

use Ajgon\LintPackBundle\Test\LintPackTestCase;

class LintTwigCommandTest extends LintPackTestCase
{
    public function setUp()
    {
        $this->command = new LintTwigCommand();
        parent::setUp();
    }

    public function testIfCommandHasGoodName()
    {
        $this->assertEquals('lint:twig', $this->command->getName());
    }

    public function testConfigurationWithEmptyLocations()
    {
        $this->assertEmptyConfigParameter('phpcpd', 'locations', true);
    }

    public function testIfDoesntLaunchWhenDisabled()
    {
        $this->assertDisabledConfig('twig');
    }

    public function testIfProcessExecutesCorrectly()
    {
        $config = $this->getTestConfig();
        $config['lint_pack']['twig']['locations'] =
            $this->parseConfigDirs($config['lint_pack']['twig']['locations']);
        $this->command->setApplication($this->getApplication());

        list($returnValue, $output) = $this->executeClassWithConfig($config);
        $result = $output->fetch();

        $this->assertEquals(0, $returnValue);
        $this->assertContains('Twig linter...', $result);
        $this->assertRegExp('@OK.*good.twig\\n@', $result);
        $this->assertContains('Done, without errors.', $result);
    }

    public function testIfProcessFailsCorrectly()
    {
        $config = $this->getTestConfig();
        $config['lint_pack']['twig']['locations'] =
            $this->parseConfigDirs($config['lint_pack']['twig']['locations']);
        $config['lint_pack']['twig']['ignores'] = array('@ignore.twig@');
        $this->command->setApplication($this->getApplication());

        list($returnValue, $output) = $this->executeClassWithConfig($config);
        $result = $output->fetch();

        $this->assertEquals(1, $returnValue);
        $this->assertContains('Twig linter...', $result);
        $this->assertRegExp('@OK.*good.twig\\n@', $result);
        $this->assertRegExp('@KO.*bad.twig@', $result);
        $this->assertContains('bazinga', $result);
        $this->assertContains('Command failed.', $result);
    }

    public function testIfProcessCatchesExceptionCorrectly()
    {
        $config = $this->getTestConfig();
        $config['lint_pack']['twig']['locations'] =
            $this->parseConfigDirs($config['lint_pack']['twig']['locations']);
        $this->command->setApplication($this->getApplication());
        $this->command->setFiles(array('blah.twig'));

        list($returnValue, $output) = $this->executeClassWithConfig($config);
        $result = $output->fetch();

        $this->assertEquals(1, $returnValue);
        $this->assertContains('Twig linter...', $result);
        $this->assertRegExp('@KO.*blah.twig@', $result);
        $this->assertContains('not readable', $result);
        $this->assertContains('Command failed.', $result);
    }

    private function getApplication()
    {
        $application = new Application();
        $application->add($this->getBaseTwigCommand());

        return $application;
    }
}
