<?php
namespace Ajgon\LintPackBundle\Command;

use Symfony\Component\Console\Application;
use Ajgon\LintPackBundle\Test\LintPackTestCase;

class LintCommandTest extends LintPackTestCase
{
    public function setUp()
    {
        $this->command = new LintCommand();
        parent::setUp();
    }

    public function testIfCommandHasGoodName()
    {
        $this->assertEquals('lint:all', $this->command->getName());
    }

    public function testIfProperCommandIsBuilt()
    {
        $this->assertEquals(
            'app/console lint:all',
            $this->command->getCommand()
        );
    }

    public function testIfExecutesAllTasks()
    {
        $config = $this->getTestConfig();
        $config['lint_pack']['phpcs']['ignores'] = array('ignore.php', 'BadFile.php');
        $config['lint_pack']['phpcs']['locations'] =
            $this->parseConfigDirs($config['lint_pack']['phpcs']['locations']);
        $config['lint_pack']['phpmd']['locations'] =
            $this->parseConfigDirs($config['lint_pack']['phpmd']['locations']);
        $config['lint_pack']['phpcpd']['locations'] =
            $this->parseConfigDirs($config['lint_pack']['phpcpd']['locations']);
        $config['lint_pack']['twig']['locations'] =
            $this->parseConfigDirs($config['lint_pack']['twig']['locations']);
        $config['lint_pack']['jshint']['bin'] = 'true';

        $this->command->setApplication($this->getApplication($config));

        $returnValue = $this->executeClassWithConfig($config);

        $this->assertEquals(0, $returnValue[0]);
    }

    public function testExecuteWithAllLintersDisabled()
    {
        $config = array('lint_pack' => null);

        $this->command->setApplication($this->getApplication($config));

        list($returnValue, $output) = $this->executeClassWithConfig($config);

        $this->assertEquals(0, $returnValue);
        $this->assertContains('Command has been disabled.', $output->fetch());
    }

    private function getApplication($config)
    {
        $jshintCommand = $this->initCommand(new LintJshintCommand(), $config);
        $phpcsCommand = $this->initCommand(new LintPhpcsCommand(), $config);
        $phpmdCommand = $this->initCommand(new LintPhpmdCommand(), $config);
        $phpcpdCommand = $this->initCommand(new LintPhpcpdCommand(), $config);
        $twigCommand = $this->initCommand(new LintTwigCommand(), $config);

        $application = new Application();
        $application->add($this->getBaseTwigCommand());
        $application->add($jshintCommand);
        $application->add($phpcsCommand);
        $application->add($phpmdCommand);
        $application->add($phpcpdCommand);
        $application->add($twigCommand);

        return $application;
    }

    private function initCommand($command, $config)
    {
        $container = $this->getContainerBuilder();
        $this->loadConfigToContainer($container, $config);
        $command->setContainer($container);

        return $command;
    }
}
