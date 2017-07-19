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
namespace HavokInspiration\ActionsClass\Http;

use HavokInspiration\ActionsClass\Http\Exception\MissingActionClassException;
use Cake\Core\App;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Utility\Inflector;
use ReflectionClass;

/**
 * Class ActionFactory
 *
 * In charge of creating the action the Dispatcher will use.
 */
class ActionFactory
{

    /**
     * Create an action for a given request/response
     *
     * @param \Cake\Http\ServerRequest $request The request to build an action for.
     * @param \Cake\Http\Response $response The response to use.
     * @return \HavokInspiration\ActionsClass\Controller\Action
     */
    public function create(ServerRequest $request, Response $response)
    {
        $pluginPath = $controller = null;
        $namespace = 'Controller';
        if ($request->getParam('plugin')) {
            $pluginPath = $request->getParam('plugin') . '.';
        }
        if ($request->getParam('prefix')) {
            if (strpos($request->getParam('prefix'), '/') === false) {
                $namespace .= '\\' . Inflector::camelize($request->getParam('prefix'));
            } else {
                $prefixes = array_map(
                    'Cake\Utility\Inflector::camelize',
                    explode('/', $request->getParam('prefix'))
                );
                $namespace .= '\\' . implode('\\', $prefixes);
            }
        }

        $this->failureIfForbiddenCharacters($request->getParam('controller'), $namespace);

        if ($request->getParam('controller')) {
            $namespace .= '\\' . $request->getParam('controller');
        }

        if ($request->getParam('action')) {
            $action = Inflector::camelize($request->getParam('action'));
        }

        // Disallow plugin short forms, / and \\ from
        // controller names as they allow direct references to
        // be created.
        $this->failureIfForbiddenCharacters($action, $namespace);

        $className = App::className($pluginPath . $action, $namespace, 'Action');
        if (!$className) {
            $this->missingAction($namespace, $action);
        }
        $reflection = new ReflectionClass($className);
        if ($reflection->isAbstract() || $reflection->isInterface()) {
            $this->missingAction($namespace, $action);
        }

        $instance = $reflection->newInstance($request, $response, $action);
        $instance->setControllerName($request->getParam('controller'));
        return $instance;
    }

    /**
     * Throws an exception when an action is missing.
     *
     * @return void
     */
    protected function missingAction($namespace, $action)
    {
        throw new MissingActionClassException([
            $namespace . '\\' . $action . 'Action'
        ]);
    }

    protected function failureIfForbiddenCharacters($name, $namespace)
    {
        if (is_string($name) &&
            (
                strpos($name, '\\') !== false ||
                strpos($name, '/') !== false ||
                strpos($name, '.') !== false ||
                substr($name, 0, 1) === strtolower(substr($name, 0, 1))
            )
        ) {
            $this->missingAction($namespace, $name);
        }
    }
}
