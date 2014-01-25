<?php
namespace Ajgon\LintPackBundle\Command;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class LintJshintCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('lint:jshint')
            ->setDescription('Lint all src/*.js and app/*.js files with jshint');
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

        $this->displayResult($returnValue, $output);
        return $returnValue;
    }

    public function getCommand()
    {
        $config = $this->getContainer()->getParameter('ajgon_lintpack.jshint');
        $extensions = '/(?:\.' . implode('$)|(?:\.', $config['extensions']) . '$)/';
        $files = $this->getFilesMatching($config['locations'], $config['ignores'], $extensions);

        return $config['bin'] .
               ' --config ' .
               $config['jshintrc'] . ' ' .
               implode(' ', $files);
    }

    private function getFilesMatching($locations, $globPatternsToIgnore, $extensionsRegexp)
    {
        $files = array();

        foreach ($locations as $location) {
            $ignoredFiles = $this->findIgnoredFiles($location, $globPatternsToIgnore);
            $files = array_merge($files, $this->findFilesWhichAreNotIgnored($location, $ignoredFiles));
        }

        return $this->filterFilesMatchingExtensionsRegexp($files, $extensionsRegexp);
    }

    private function findIgnoredFiles($location, $globPatternsToIgnore)
    {
        $ignoredFiles = array();
        foreach ($globPatternsToIgnore as $globPatternToIgnore) {
            $ignoredFiles = array_merge(
                $ignoredFiles,
                glob($location . DIRECTORY_SEPARATOR . $globPatternToIgnore)
            );
        }

        return $ignoredFiles;
    }

    private function findFilesWhichAreNotIgnored($location, $ignoredFiles)
    {
        $notIgnoredFiles = array();
        $iteratedFiles = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($location),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iteratedFiles as $iteratedFile) {
            if (!in_array($iteratedFile, $ignoredFiles)) {
                $notIgnoredFiles[] = (string)$iteratedFile;
            }
        }

        return $notIgnoredFiles;
    }

    private function filterFilesMatchingExtensionsRegexp($files, $extensionsRegexp)
    {
        $matchedFiles = array();

        foreach ($files as $file) {
            if (preg_match($extensionsRegexp, $file)) {
                $matchedFiles[] = $file;
            }
        }

        return $matchedFiles;
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
