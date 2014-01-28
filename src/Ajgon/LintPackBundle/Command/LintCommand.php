<?php
namespace Ajgon\LintPackBundle\Command;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * {@inheritDoc}
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class LintCommand extends ContainerAwareCommand
{
    protected $allFiles;

    protected function configure()
    {
        $this
            ->setName('lint:all')
            ->setDescription('Lint all files with all linters');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $tasks = array('lint:jshint', 'lint:phpcs', 'lint:phpcpd', 'lint:phpmd', 'lint:twig');
        $returnCodes = array();

        foreach ($tasks as $task) {
            $returnCodes[] = $this->launchTask($task, $input, $output, $this->isTaskEnabled($task));
        }

        return max($returnCodes);
    }

    protected function executeCommand($command, OutputInterface $output)
    {
        if (!$this->isTaskEnabled()) {
            return $this->handleDisabledTask($output);
        }

        $process = new Process($command);

        $output->writeln($command);
        $returnValue = $process->run();
        $output->writeln($process->getOutput());

        $this->displayResult($returnValue, $output);
        return $returnValue;
    }

    protected function launchTask($task, InputInterface $input, OutputInterface $output, $addNoise = true)
    {
        if ($addNoise) {
            $output->writeln("Invoking <info>{$task}</info>");
        }

        $application = $this->getApplication();
        $returnValue = $application->find($task)->run($input, $output);

        return $returnValue;
    }

    protected function displayResult($returnValue, $output)
    {
        if ($returnValue === 0) {
            $output->writeln("<info>Done, without errors.</info>\n");
        } else {
            $output->writeln("<error>Command failed.</error>\n");
        }
    }

    protected function getFilesMatching($locations, $ignoresRegexpes, $extensionsRegexp)
    {
        $files = array();

        foreach ($locations as $location) {
            $ignoredFiles = $this->findIgnoredFiles($location, $ignoresRegexpes);
            $files = array_merge($files, $this->findFilesWhichAreNotIgnored($location, $ignoredFiles));
        }

        return $this->filterFilesMatchingExtensionsRegexp($files, $extensionsRegexp);
    }

    protected function findIgnoredFiles($location, $ignoresRegexpes)
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

    protected function findFilesWhichAreNotIgnored($location, $ignoredFiles)
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

    protected function filterFilesMatchingExtensionsRegexp($files, $extensionsRegexp)
    {
        $matchedFiles = array();

        foreach ($files as $file) {
            if (preg_match($extensionsRegexp, $file)) {
                $matchedFiles[] = $file;
            }
        }

        return $matchedFiles;
    }

    protected function getAllFiles($location)
    {
        if (!isset($this->allFiles[$location])) {
            $this->allFiles[$location] = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($location),
                RecursiveIteratorIterator::SELF_FIRST
            );
        }
        return $this->allFiles[$location];
    }

    protected function convertIteratorToArray($iterator)
    {
        $result = array();
        foreach ($iterator as $i) {
            $result[] = (string)$i;
        }

        return $result;
    }

    protected function isTaskEnabled($name = null)
    {
        $enabled = $this->getContainer()->getParameter('lint_pack');
        return !!$enabled[str_replace('lint:', '', ($name ? $name : $this->getName()))];
    }

    protected function handleDisabledTask($output)
    {
        $output->writeln('<comment>Command has been disabled.</comment>');
        return 0;
    }

    public function getCommand()
    {
        return 'app/console lint:all';
    }
}
