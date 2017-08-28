<?php
/**
 * Copyright (c) Yves Piquel (http://www.havokinspiration.fr)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Yves Piquel (http://www.havokinspiration.fr)
 * @link          http://github.com/HavokInspiration/cakephp-actions-class
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
declare(strict_types=1);
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
    public $pathFragment = 'Controller/';

    /**
     * Tasks to be loaded by this Task
     *
     * @var array
     */
    public $tasks = [
        'Bake.BakeTemplate',
        'Bake.Test'
    ];

    /**
     * {@inheritDoc}
     */
    public function name()
    {
        return 'action';
    }

    /**
     * {@inheritDoc}
     */
    public function fileName($name, $test = false)
    {
        $fileName = $name . 'Action';
        if ($test) {
            $fileName .= 'Test';
        }
        $fileName .= '.php';

        return $fileName;
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
    public function templateTest()
    {
        return 'HavokInspiration/ActionsClass.test';
    }

    /**
     * {@inheritDoc}
     */
    public function bake($name)
    {
        if (strpos($name, '/') === false) {
            $this->err('You must pass a Controller name for your action in the format `ControllerName/ActionName`');

            return (string)Shell::CODE_ERROR;
        }

        $this->out("\n" . sprintf('Baking action class for %s...', $name), 1, Shell::QUIET);

        list($controller, $action) = $this->getName($name);

        $namespace = Configure::read('App.namespace');
        if ($this->plugin) {
            $namespace = $this->_pluginNamespace($this->plugin);
        }

        $prefix = $this->_getPrefix();
        if ($prefix) {
            $prefix = '\\' . str_replace('/', '\\', $prefix);
        }

        $data = [
            'action' => $action,
            'controller' => $controller,
            'namespace' => $namespace,
            'prefix' => $prefix
        ];

        $out = $this->bakeAction($action, $data);

        if ($this->params['no-test'] !== true) {
            $this->bakeActionTest($action, $data);
        }

        return $out;
    }

    /**
     * Generate the action code
     *
     * @param string $actionName The name of the action.
     * @param array $data The data to turn into code.
     * @return string The generated action file.
     */
    public function bakeAction($actionName, array $data)
    {
        $data += [
            'namespace' => null,
            'controller' => null,
            'prefix' => null,
            'actions' => null,
        ];
        $this->BakeTemplate->set($data);
        $contents = $this->BakeTemplate->generate($this->template());
        $path = $this->getPath();
        $filename = $path . $data['controller'] . DS . $this->fileName($actionName);
        $this->createFile($filename, $contents);

        return $contents;
    }

    /**
     * Assembles and writes a unit test file
     *
     * @param string $className Controller class name
     * @return string|null Baked test
     */
    public function bakeActionTest($actionName, $data)
    {
        $data += [
            'namespace' => null,
            'controller' => null,
            'prefix' => null,
            'actions' => null,
        ];
        $this->Test->plugin = $this->plugin;
        $this->BakeTemplate->set($data);

        $prefix = $this->_getPrefix();
        $contents = $this->BakeTemplate->generate($this->templateTest());

        $path = $this->Test->getPath() . 'Controller' . DS;
        if ($prefix) {
            $path .= $prefix . DS;
        }
        $path .= $data['controller'];

        $filename = $path . DS . $this->fileName($actionName, true);
        $this->createFile($filename, $contents);

        return $contents;
    }

    /**
     * Transform the name parameter into Controller & Action name.
     *
     * @param string $name Name passed to the CLI.
     * @return array First key is the controller name, second key the action name.
     */
    protected function getName($name)
    {
        list($controller, $action) = explode('/', $name);

        $controller = $this->_camelize($controller);
        $action = $this->_camelize($action);

        return [$controller, $action];
    }

    /**
     * {@inheritDoc}
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $parser
            ->setDescription(
                'Bake an Action class file skeleton'
            )
            ->addOption('prefix', [
                'help' => 'The namespace/routing prefix to use.'
            ])
            ->addOption('no-test', [
                'boolean' => true,
                'help' => 'Do not generate a test skeleton.'
            ]);

        return $parser;
    }
}
