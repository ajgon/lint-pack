<?php
namespace Ajgon\LintPackBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Ajgon\LintPackBundle\Command\LintCommand;

class LintPhpcsCommand extends LintCommand
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
        return $this->executeCommand($output);
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
}
