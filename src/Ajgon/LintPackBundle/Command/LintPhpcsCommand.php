<?php
namespace Ajgon\LintPackBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class LintPhpcsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('lint:phpcs')
            ->setDescription('Lint all files with phpcs');
    }

    /**
     * {@inheritDoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $command = $this->getCommand();
        $process = new Process($command);

        $output->writeln($command);
        $returnValue = $process->run();
        $output->writeln($process->getOutput());

        $this->displayResult($returnValue, $output);
        return $returnValue;
    }

    public function getCommand()
    {
        $config = $this->getContainer()->getParameter('lint_pack.phpcs');

        return $config['bin'] .
               ' -p' .
               ($config['warnings'] ? '' : ' -n') .
               ($config['recursion'] ? '' : ' -l') .
               ($config['standard'] ? ' --standard=' . $config['standard'] : '') .
               ($config['extensions'] ? ' --extensions=' . implode(',', $config['extensions']) : '') .
               ($config['ignores'] ? ' --ignore=' . implode(',', $config['ignores']) : '') .
               ' ' . implode(' ', $config['locations']);
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
