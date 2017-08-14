<?php

namespace HavokInspiration\ActionsClass\Shell\Task;

use Cake\Console\Shell;
use Bake\Shell\Task\SimpleBakeTask;
use Cake\Core\Configure;

/**
 * Command line to create HavokInspiration\ActionsClass action file
 */
class ActionTask extends SimpleBakeTask
{
    /**
     * Tasks to be loaded by this Task
     *
     * @var array
     */
    public $tasks = [
        'Bake.BakeTemplate'
    ];

    /**
     * {@inheritDoc}
     */
    public function name()
    {
        return 'file for action';
    }

    /**
     * {@inheritDoc}
     */
    public function fileName($name)
    {
        return $name . '.php';
    }

    /**
     * {@inheritDoc}
     */
    public function template()
    {
        return 'HavokInspiration/ActionsClass.action';
    }

    /**
     * Generate a class stub
     *
     * @param string $name The classname to generate.
     * @return string
     */
    public function bake($name)
    {
        $this->out("\n" . sprintf('Baking action class for %s...', $name), 1, Shell::QUIET);

        list($controller, $action) = $this->setName($name);

        $path = APP . 'Controller';
        $namespace = Configure::read('App.namespace');

        $prefix = $this->_getPrefix();
        if ($prefix) {
            $path .= DS . $prefix;
            $prefix = '\\' . str_replace('/', '\\', $prefix);
        }

        if ($this->plugin) {
            $path = $this->getPath() . 'Controller';
            $namespace = $this->_pluginNamespace($this->plugin);
        }

        $data = [
            'action' => $action,
            'controller' => $controller,
            'namespace' => $namespace,
            'prefix' => $prefix
        ];

        $this->BakeTemplate->set($data);

        $filename = $path . DS . $controller . DS . $action . 'Action.php';
        $this->_createActionFile($filename);
    }

    /**
     * Gets the option parser instance and configures it.
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $name = $this->name();
        $parser->setDescription(
            'Bake an Action class'
        )->addOption('prefix', [
            'help' => 'The namespace/routing prefix to use.'
        ]);

        return $parser;
    }


    /**
     * Assembles and writes a unit test file
     *
     * @param string $name Name parameter
     * @return string|null Baked test
     */
    public function bakeTest($name)
    {
        if (!empty($this->params['no-test'])) {
            return null;
        }

        list($controller, $action) = $this->setName($name);
        $namespace = Configure::read('App.namespace');
        $path = TESTS . 'TestCase' . DS . 'Controller';

        $plugin = $this->plugin;
        if ($plugin) {
            $path = $this->getPath() . 'Controller';
            $namespace = $this->_pluginNamespace($this->plugin);
        }

        $prefix = $this->_getPrefix();
        if ($prefix) {
            $path .= DS . $prefix;
            $prefix = '\\' . str_replace('/', '\\', $prefix);
        }

        $data = [
            'action' => $action,
            'controller' => $controller,
            'namespace' => $namespace,
            'prefix' => $prefix
        ];

        $this->BakeTemplate->set($data);
        
        $path .= DS . $controller . DS . $action . 'ActionTest.php';
        $this->_createTestFile($path);

        return true;
    }

    /**
     * Transform the name parameter into Controller & Action name
     *
     * @param string $name
     * @return array
     */
    protected function setName($name)
    {
        if (strpos($name, '/') !== false) {
            list($controller, $action) = explode('/', $name);
        } else {
            $controller = $name;
            $action = 'index';
        }
        $controller = $this->_camelize($controller);
        $action = $this->_camelize($action);

        return [$controller, $action];
    }

    /**
     * Create Controller Action file
     *
     * @param string $filename The filename path to create file
     */
    protected function _createActionFile($filename)
    {
        $contents = $this->BakeTemplate->generate($this->template());
        $this->createFile($filename, $contents);
    }

    /**
     * Create TestCase file
     *
     * @param string $filename The filename path to create file
     */
    protected function _createTestFile($filename)
    {
        $contents = $this->BakeTemplate->generate('HavokInspiration/ActionsClass.tests');
        $this->createFile($filename, $contents);
    }
}
