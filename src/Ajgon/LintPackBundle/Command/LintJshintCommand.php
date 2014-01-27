<?php
namespace Ajgon\LintPackBundle\Command;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class LintJshintCommand extends ContainerAwareCommand
{
    private $allFiles = array();

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
        $extensions = '/(?:\.' . implode('$)|(?:\.', $config['extensions']) . '$)/';
        $files = $this->getFilesMatching($config['locations'], $config['ignores'], $extensions);

        return trim(
            $config['bin'] .
            ((isset($config['jshintrc']) && $config['jshintrc']) ? ' --config ' . $config['jshintrc'] : '') .
            ' ' . implode(' ', $files)
        );
    }

    private function getFilesMatching($locations, $ignoresRegexpes, $extensionsRegexp)
    {
        $files = array();

        foreach ($locations as $location) {
            $ignoredFiles = $this->findIgnoredFiles($location, $ignoresRegexpes);
            $files = array_merge($files, $this->findFilesWhichAreNotIgnored($location, $ignoredFiles));
        }

        return $this->filterFilesMatchingExtensionsRegexp($files, $extensionsRegexp);
    }

    private function findIgnoredFiles($location, $ignoresRegexpes)
    {
        $ignoredFiles = array();
        $allFiles = $this->getAllFiles($location);

        foreach ($ignoresRegexpes as $ignoresRegexp) {

            $ignoredFiles = array_merge_recursive(
                $ignoredFiles,
                $this->convertIteratorToArray(new RegexIterator($allFiles, $ignoresRegexp))
            );
        }

        return $ignoredFiles;
    }

    private function findFilesWhichAreNotIgnored($location, $ignoredFiles)
    {
        $notIgnoredFiles = array();
        $iteratedFiles = $this->getAllFiles($location);

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

    private function getAllFiles($location)
    {
        if (!isset($this->allFiles[$location])) {
            $this->allFiles[$location] = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($location),
                RecursiveIteratorIterator::SELF_FIRST
            );
        }
        return $this->allFiles[$location];
    }

    private function convertIteratorToArray($iterator)
    {
        $result = array();
        foreach ($iterator as $i) {
            $result[] = (string)$i;
        }

        return $result;
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
