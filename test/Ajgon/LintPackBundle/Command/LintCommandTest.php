<?php
namespace Ajgon\LintPackBundle\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

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

    public function testIfExecutesAllTasks()
    {
        $config = $this->getTestConfig();
        $config['lint_pack']['phpcs']['ignores'] = array('ignore.php', 'BadFile.php');
        $config['lint_pack']['phpcs']['locations'] =
            $this->parseConfigDirs($config['lint_pack']['phpcs']['locations']);
        $config['lint_pack']['phpmd']['locations'] =
            $this->parseConfigDirs($config['lint_pack']['phpmd']['locations']);
        $config['lint_pack']['jshint']['bin'] = 'true';

        $this->command->setApplication($this->getApplication($config));

        $returnValue = $this->executeClassWithConfig($config);

        $this->assertEquals(0, $returnValue[0]);
    }

    private function getApplication($config)
    {
        $jshintCommand = $this->initCommand(new LintJshintCommand(), $config);
        $phpcsCommand = $this->initCommand(new LintPhpcsCommand(), $config);
        $phpmdCommand = $this->initCommand(new LintPhpmdCommand(), $config);

        $application = new Application();
        $application->add($jshintCommand);
        $application->add($phpcsCommand);
        $application->add($phpmdCommand);

        return $application;
    }

    private function initCommand($command, $config)
    {
        $container = $this->getContainerBuilder();
        $this->loadConfigToContainer($container, $config, true);
        $command->setContainer($container);

        return $command;
    }
}
