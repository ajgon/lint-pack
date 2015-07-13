<?php
namespace Ajgon\LintPackBundle\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ajgon\LintPackBundle\Command\LintCommand;

class LintTwigviewsCommand extends LintCommand
{
    private $files = array();

    protected function configure()
    {
        $this
            ->setName('lint:twigviews')
            ->setDescription('Lint all files with twig linter');
    }

    /**
     * {@inheritDoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->isTaskEnabled()) {
            return $this->handleDisabledTask($output);
        }

        $files = $this->getTwigFiles();
        $output->writeln('Twig linter...');
        $returnCode = $this->handleExecution($files, $output);
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

    private function getTwigFiles()
    {
        $config = $this->getContainer()->getParameter('lint_pack.twigviews');
        $files = $this->getFiles();

        $files = empty($files) ?
            $this->getFilesMatching($config['locations'], $config['ignores'], '/\.twig$/') :
            $files;

        return $files;
    }

    private function handleExecution($files, OutputInterface $output)
    {
        $returnCodes = array();
        $version = (int)str_pad(str_replace('.', '', \Symfony\Component\HttpKernel\Kernel::VERSION), 5, '0');

        foreach ($files as $file) {
            if ($version >= 27000) {
                $input = new ArrayInput(array('lint:twig', 'filename' => array($file)));
            } else {
                $input = new ArrayInput(array('twig:lint', 'filename' => $file));
            }
            try {
                $returnCodes[] =
                    $this->launchTask(($version >= 27000 ? 'lint:twig' : 'twig:lint'), $input, $output, false);
            } catch (\Exception $e) {
                $output->writeln("<error>KO</error> in $file\n      <error>{$e->getMessage()}</error>");
                $returnCodes[] = 1;
            }
        }
        return max($returnCodes);
    }
}
