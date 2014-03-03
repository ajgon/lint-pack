<?php
namespace Ajgon\LintPackBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ajgon\LintPackBundle\Command\LintCommand;

class LintPhpmdCommand extends LintCommand
{
    protected function configure()
    {
        $this
            ->setName('lint:phpmd')
            ->setDescription('Lint all files with phpmd');
    }

    /**
     * {@inheritDoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        return $this->executeCommand($this->getCommand(), $output);
    }

    public function getCommand()
    {
        $config = $this->getContainer()->getParameter('lint_pack.phpmd');

        return $config['bin'] .
               ' ' . implode(DIRECTORY_SEPARATOR . '*,', $config['locations']) .
               ' text' .
               ' ' . implode(',', $config['rulesets']) .
               ($config['extensions'] ? ' --suffixes=' . implode(',', $config['extensions']) : '') .
               ($config['ignores'] ? ' --exclude ' . implode(',', $config['ignores']) : '');
    }
}
