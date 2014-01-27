<?php
namespace Ajgon\LintPackBundle\Test;

use PHPUnit_Framework_TestCase;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

use Ajgon\LintPackBundle\DependencyInjection\LintPackExtension;
use Ajgon\LintPackBundle\DependencyInjection\Configuration;

class LintPackTestCase extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->extension = new LintPackExtension();
        $this->initWithConfig();
    }

    protected function initWithConfig($config = null)
    {
        $container = $this->getContainerBuilder();
        $this->loadConfigToContainer($container, $config, true);
        $this->command->setContainer($container);
    }

    protected function initWithoutConfig()
    {
        $config = $this->getDefaultConfig();
        $container = $this->getContainerBuilder();
        $this->loadConfigToContainer($container, $config, true);
        $this->command->setContainer($container);
    }

    protected function loadConfigToContainer(&$container, $config = null, $parseDir = false)
    {
        if (is_null($config)) {
            $config = $this->getTestConfig();
        }

        if ($parseDir) {
            $config['lint_pack']['jshint']['locations'] =
                $this->parseConfigDirs($config['lint_pack']['jshint']['locations']);
        }
        $this->extension->load($config, $container);
    }

    protected function executeClassWithConfig($config)
    {
        $this->initWithConfig($config);

        $input = new ArrayInput(array());
        $output = new BufferedOutput();

        $returnValue = $this->command->execute($input, $output);
        return array($returnValue, $output);
    }

    protected function parseConfigDirs($dirs)
    {
        $results = array();

        if (!is_array($dirs)) {
            return $results;
        }

        foreach ($dirs as $dir) {
            $results[] = realpath(str_replace('%kernel.root_dir%', TESTS_PATH, $dir));
        }

        return $results;
    }

    protected function getContainerBuilder()
    {
        $container = new ContainerBuilder();

        $bundles = array(
            'LintPackBundle' => 'Ajgon\LintPackBundle\LintPackBundle',
        );

        $container->setParameter('kernel.bundles', $bundles);

        return $container;
    }

    protected function getDefaultConfig()
    {
        $processor = new Processor();
        return array('lint_pack' => $processor->processConfiguration(new Configuration(), array()));
    }

    protected function getTestConfig()
    {
        return Yaml::parse(TESTS_PATH.'/fixtures/config.yml');
    }

    protected function getEmptyTestConfig()
    {
        return Yaml::parse(TESTS_PATH.'/fixtures/config-empty.yml');
    }
}
