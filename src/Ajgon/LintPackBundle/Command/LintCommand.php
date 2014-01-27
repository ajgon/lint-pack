<?php
namespace Ajgon\LintPackBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class LintCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('lint:all')
            ->setDescription('Lint all files with all linters');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $jshintReturnCode = $this->launchTask('lint:jshint', $input, $output);
        $phpcsReturnCode = $this->launchTask('lint:phpcs', $input, $output);

        return max($jshintReturnCode, $phpcsReturnCode);
    }

    protected function executeCommand(OutputInterface $output)
    {
        $command = $this->getCommand();
        $process = new Process($command);

        $output->writeln($command);
        $returnValue = $process->run();
        $output->writeln($process->getOutput());

        $this->displayResult($returnValue, $output);
        return $returnValue;
    }

    private function launchTask($task, InputInterface $input, OutputInterface $output, $addNoise = true)
    {
        if ($addNoise) {
            $output->writeln("Invoking <info>{$task}</info>");
        }

        $application = $this->getApplication();
        $returnValue = $application->find($task)->run($input, $output);

        return $returnValue;
    }

    private function displayResult($returnValue, $output)
    {
        if ($returnValue === 0) {
            $output->writeln("<info>Done, without errors.</info>\n");
        } else {
            $output->writeln("<error>Command failed.</error>\n");
        }
    }
}
