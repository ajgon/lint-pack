<?php
namespace Ajgon\LintPackBundle\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Ajgon\LintPackBundle\Command\LintCommand;

class LintTwigCommand extends LintCommand
{
    protected function configure()
    {
        $this
            ->setName('lint:twig')
            ->setDescription('Lint all files with twig:lint');
    }

    /**
     * {@inheritDoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->getContainer()->getParameter('lint_pack.twig');

        $files = $this->getFilesMatching($config['locations'], $config['ignores'], '/\.twig$/');
        $returnCodes = array();

        $output->writeln('Twig linter...');
        foreach ($files as $file) {
            $input = new ArrayInput(array('filename' => $file));
            $returnCodes[] = $this->launchTask('twig:lint', $input, $output, false);
        }
        $returnCode = max($returnCodes);
        $this->displayResult($returnCode, $output);

        return $returnCode;
    }
}
