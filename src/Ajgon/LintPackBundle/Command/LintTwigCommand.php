<?php
namespace Ajgon\LintPackBundle\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Ajgon\LintPackBundle\Command\LintCommand;

class LintTwigCommand extends LintCommand
{
    private $files = array();

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

        $files = empty($this->getFiles()) ?
            $this->getFilesMatching($config['locations'], $config['ignores'], '/\.twig$/') :
            $this->getFiles();

        $returnCodes = array();

        $output->writeln('Twig linter...');
        foreach ($files as $file) {
            $input = new ArrayInput(array('filename' => $file));
            try {
                $returnCodes[] = $this->launchTask('twig:lint', $input, $output, false);
            } catch (\Exception $e) {
                $output->writeln("<error>KO</error> in $file\n      <error>{$e->getMessage()}</error>");
                $returnCodes[] = 1;
            }
        }
        $returnCode = max($returnCodes);
        $this->displayResult($returnCode, $output);

        return $returnCode;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function setFiles($files)
    {
        $this->files = $files;
    }
}
