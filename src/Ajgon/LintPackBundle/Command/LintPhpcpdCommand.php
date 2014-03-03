<?php
namespace Ajgon\LintPackBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ajgon\LintPackBundle\Command\LintCommand;

class LintPhpcpdCommand extends LintCommand
{
    protected function configure()
    {
        $this
            ->setName('lint:phpcpd')
            ->setDescription('Lint all files with phpcpd');
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
        $config = $this->getContainer()->getParameter('lint_pack.phpcpd');

        return $config['bin'] .
               ' --progress' .
               ($config['min_lines'] ? ' --min-lines=' . $config['min_lines'] : '') .
               ($config['min_tokens'] ? ' --min-tokens=' . $config['min_tokens'] : '') .
               ($config['extensions'] ? ' --names=\\*.' . implode(',\\*.', $config['extensions']) : '') .
               ($config['ignores'] ?
                    ' --names-exclude=' . implode(',', $config['ignores']) .
                    ' --exclude=' . implode(',', $config['ignores']) :
                    ''
               ) .
               ' ' . implode(',', $config['locations']);
    }
}
