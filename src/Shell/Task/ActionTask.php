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
     * {@inheritDoc}
     */
    public $pathFragment = '';

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
     * {@inheritDoc}
     */
    public function bake($name)
    {
        $this->out("\n" . sprintf('Baking action class for %s...', $name), 1, Shell::QUIET);

        $path = APP;

        return $this->bakeAction($name, $path);
    }

    /**
     * {@inheritDoc}
     */
    public function bakeTest($name)
    {
        if (!empty($this->params['no-test'])) {
            return null;
        }
        $path = TESTS . 'TestCase' . DS;

        return $this->bakeAction($name, $path, 'Test');
    }

    /**
     * {@inheritDoc}
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
     * Do the bake action with the parameters in command line
     *
     * @param string $name The name of the action
     * @param string $path Where the file will be create
     * @param string $type if is Test or other
     *
     * @return bool
     */
    protected function bakeAction($name, $path, $type = '')
    {
        list($controller, $action) = $this->setName($name);

        $namespace = Configure::read('App.namespace');

        $plugin = $this->plugin;
        if ($plugin) {
            $path = $this->_pluginPath($plugin) . 'src' . DS;
            if ($type == 'Test') {
                $path = $this->_pluginPath($plugin) . 'tests' . DS . 'TestCase' . DS;
            }
            $namespace = $this->_pluginNamespace($this->plugin);
        }
        $path .= 'Controller';

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
        $filename = $path . DS . $controller . DS . $action . 'Action' . $type . '.php';

        if ($type == 'Test') {
            $this->createTestFile($filename);
        } else {
            $this->createActionFile($filename);
        }

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
    protected function createActionFile($filename)
    {
        $contents = $this->BakeTemplate->generate($this->template());
        $this->createFile($filename, $contents);
    }

    /**
     * Create TestCase file
     *
     * @param string $filename The filename path to create file
     */
    protected function createTestFile($filename)
    {
        $contents = $this->BakeTemplate->generate('HavokInspiration/ActionsClass.tests');
        $this->createFile($filename, $contents);
    }
}
