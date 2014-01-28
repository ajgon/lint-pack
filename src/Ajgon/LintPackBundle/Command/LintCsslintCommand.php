<?php
namespace Ajgon\LintPackBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ajgon\LintPackBundle\Command\LintCommand;

class LintCsslintCommand extends LintCommand
{
    protected function configure()
    {
        $this
            ->setName('lint:csslint')
            ->setDescription('Lint all files with csslint');
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
        $config = $this->getContainer()->getParameter('lint_pack.csslint');

        return $config['bin'] .
               (
                   $config['disable_rules'] ?
                   ' --ignore=' . implode(',', $config['disable_rules']) :
                   ''
               ) .
               (
                   $config['ignores'] ?
                   ' --exclude-list=' . implode(',', $config['ignores']) :
                   ''
               ) .
               ' ' . implode(',', $config['locations']);
    }
}
