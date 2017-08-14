<?php

namespace HavokInspiration\ActionsClass\Shell\Task;

use Cake\Console\Shell;
use Cake\Core\Configure;
use Bake\Shell\Task\SimpleBakeTask;

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
        return 'file for HavokInspiration/ActionsClass';
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

        if (strpos($name, '/') !== false) {
            list($controller, $action) = explode('/', $name);
        } else {
            $controller = $name;
            $action = 'index';
        }
        $controller = $this->_camelize($controller);
        $action = $this->_camelize($action);

        $path = APP . 'Controller';
        $namespace = Configure::read('App.namespace');

        $prefix = $this->_getPrefix();
        if ($prefix) {
            $path .= DS . $prefix;
            $prefix = '\\' . str_replace('/', '\\', $prefix);
        }

        if ($this->plugin) {
            $path = $this->getPath(). 'Controller';
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
        $this->_createTestFile(TESTS . 'TestCase' . DS . 'Controller' . DS . $controller . DS . $action . 'Action.php');
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
            sprintf('Bake a %s.', $name)
        )->addOption('prefix', [
            'help' => 'The namespace/routing prefix to use.'
        ]);

        return $parser;
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
