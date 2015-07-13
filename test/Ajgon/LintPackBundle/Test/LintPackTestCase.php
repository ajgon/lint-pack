<?php
namespace Ajgon\LintPackBundle\Test;

use PHPUnit_Framework_TestCase;
use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;
use Ajgon\LintPackBundle\DependencyInjection\LintPackExtension;
use Ajgon\LintPackBundle\DependencyInjection\Configuration;

class LintPackTestCase extends PHPUnit_Framework_TestCase
{
    protected $command;
    protected $extension;

    public function setUp()
    {
        $this->extension = new LintPackExtension();
        $this->initWithConfig();
    }

    protected function initWithConfig($config = null)
    {
        $container = $this->getContainerBuilder();
        $this->loadConfigToContainer($container, $config);
        $this->command->setContainer($container);
    }

    protected function initWithoutConfig()
    {
        $config = $this->getDefaultConfig();
        $container = $this->getContainerBuilder();
        $this->loadConfigToContainer($container, $config);
        $this->command->setContainer($container);
    }

    protected function assertEmptyConfigParameter($linter, $param, $isArray)
    {
        $this->setExpectedException(
            'Symfony\Component\Config\Definition\Exception\InvalidConfigurationException',
            (
                $isArray ?
                "The path \"lint_pack.{$linter}.{$param}\" should have at least 1 element(s) defined." :
                "The path \"lint_pack.{$linter}.{$param}\" cannot contain an empty value, but got null."
            ),
            0
        );

        $config = $this->getTestConfig();
        $config['lint_pack'][$linter][$param] = ($isArray ? array() : null);
        $this->initWithConfig($config);
    }

    protected function assertDisabledConfig($name)
    {
        $config = $this->getTestConfig();
        unset($config['lint_pack'][$name]);

        list($returnValue, $output) = $this->executeClassWithConfig($config);

        $this->assertEquals(0, $returnValue);
        $this->assertEquals("Command has been disabled.\n", $output->fetch());
    }

    protected function loadConfigToContainer(&$container, $config = null, $parseDir = true)
    {
        if (is_null($config)) {
            $config = $this->getTestConfig();
        }

        if ($parseDir && isset($config['lint_pack'])) {
            foreach ($config['lint_pack'] as $linter => $options) {
                $config['lint_pack'][$linter]['locations'] = $this->parseConfigDirs($options['locations']);
            }
        }
        $this->extension->load($config, $container);
    }

    protected function executeClassWithConfig($config)
    {
        $this->initWithConfig($config);

        $input = new ArrayInput(array('param'));
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
            'TwigBundle' => 'Symfony\Bundle\TwigBundle\TwigBundle'
            // 'TemplateLocator' => 'Symfony\Bundle\FrameworkBundle\Templating\Loader\TemplateLocator'
        );

        $container->setParameter('kernel.bundles', $bundles);
        $container->setParameter('kernel.environment', 'test');

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

    protected function getBaseTwigCommand()
    {
        $container = $this->buildTwigContainer();
        $extension = new TwigExtension();
        $extension->load(array(), $container);

        $baseTwigCommand = new \Symfony\Bundle\TwigBundle\Command\LintCommand();
        $baseTwigCommand->setContainer($container);

        return $baseTwigCommand;
    }

    private function buildTwigContainer()
    {
        $container = $this->getContainerBuilder();
        $container->setParameter('kernel.root_dir', TESTS_PATH);
        $container->setParameter('kernel.debug', false);
        $container->setParameter('kernel.cache_dir', TESTS_PATH . DIRECTORY_SEPARATOR . 'cache');
        $container->setParameter('kernel.charset', 'UTF-8');
        $container->set('templating.locator', $this->getTemplatingLocator());
        $container->set('templating.name_parser', $this->getTemplatingNameParser());
        $container->set('templating.globals', new \stdClass());

        return $container;
    }

    private function getTemplatingLocator()
    {
        return $this
            ->getMockBuilder('Symfony\Bundle\FrameworkBundle\Templating\Loader\TemplateLocator')
            ->setConstructorArgs(array($this->getFileLocator()))
            ->getMock();
    }

    private function getTemplatingNameParser()
    {
        return $this
            ->getMockBuilder('Symfony\Bundle\FrameworkBundle\Templating\TemplateNameParser')
            ->setConstructorArgs(array($this->getMock('Symfony\Component\HttpKernel\KernelInterface')))
            ->getMock();
    }

    private function getFileLocator()
    {
        return $this
            ->getMockBuilder('Symfony\Component\Config\FileLocator')
            ->setMethods(array('locate'))
            ->setConstructorArgs(array('/path/to/fallback'))
            ->getMock();
    }
}
