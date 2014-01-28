<?php
namespace Ajgon\LintPackBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ajgon\LintPackBundle\Command\LintCommand;

class LintJshintCommand extends LintCommand
{
    protected function configure()
    {
        $this
            ->setName('lint:jshint')
            ->setDescription('Lint all files with jshint');
    }

    /**
     * {@inheritDoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        return $this->executeCommand(($this->isTaskEnabled() ? $this->getCommand() : ''), $output);
    }

    public function getCommand()
    {
        $config = $this->getContainer()->getParameter('lint_pack.jshint');

        if (isset($config['jshintignore']) && is_string($config['jshintignore'])) {
            return $this->getCommandWithIgnore($config);
        }
        return $this->getCommandWithoutIgnore($config);
    }

    private function getCommandWithIgnore($config)
    {
        return trim(
            $config['bin'] .
            ((isset($config['jshintrc']) && $config['jshintrc']) ? ' --config ' . $config['jshintrc'] : '') .
            ' --exclude-path ' . $config['jshintignore'] .
            (!is_null($config['extensions']) ? ' --extra-ext ' . implode(',', $config['extensions']) : '') .
            (!is_null($config['locations']) ? ' ' . implode(' ', $config['locations']) : '')
        );
    }

    private function getCommandWithoutIgnore($config)
    {
        if (empty($config['extensions'])) {
            $extensions = '/\..[^.]+/';
        } else {
            $extensions = '/(?:\.' . implode('$)|(?:\.', $config['extensions']) . '$)/';
        }

        $files = $this->getFilesMatching($config['locations'], $config['ignores'], $extensions);

        return trim(
            $config['bin'] .
            ((isset($config['jshintrc']) && $config['jshintrc']) ? ' --config ' . $config['jshintrc'] : '') .
            ' ' . implode(' ', $files)
        );
    }
}
