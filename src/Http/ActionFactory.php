<?php
namespace HavokInspiration\ActionsClass\Http;

use HavokInspiration\ActionsClass\Http\Exception\MissingActionClassException;
use Cake\Core\App;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Utility\Inflector;
use ReflectionClass;

class ActionFactory
{
    /**
     * Create a controller for a given request/response
     *
     * @param \Cake\Http\ServerRequest $request The request to build a controller for.
     * @param \Cake\Http\Response $response The response to use.
     * @return \Cake\Controller\Controller
     */
    public function create(ServerRequest $request, Response $response)
    {
        $pluginPath = $controller = null;
        $namespace = 'Controller';
        if ($request->getParam('plugin')) {
            $pluginPath = $request->getParam('plugin') . '.';
        }
        if ($request->getParam('controller')) {
            $namespace .= '\\' . $request->getParam('controller');
        }
        if ($request->getParam('prefix')) {
            if (strpos($request->getParam('prefix'), '/') === false) {
                $namespace .= '/' . Inflector::camelize($request->getParam('prefix'));
            } else {
                $prefixes = array_map(
                    'Cake\Utility\Inflector::camelize',
                    explode('/', $request->getParam('prefix'))
                );
                $namespace .= '/' . implode('/', $prefixes);
            }
        }

        $action = 'Index';
        if ($request->getParam('controller')) {
            $action = Inflector::camelize($request->getParam('action'));
        }
        $firstChar = substr($action, 0, 1);

        // Disallow plugin short forms, / and \\ from
        // controller names as they allow direct references to
        // be created.
        if (strpos($action, '\\') !== false ||
            strpos($action, '/') !== false ||
            strpos($action, '.') !== false ||
            $firstChar === strtolower($firstChar)
        ) {
            $this->missingAction($namespace, $action);
        }

        $className = App::className($pluginPath . $action, $namespace, 'Action');
        if (!$className) {
            $this->missingAction($namespace, $action);
        }
        $reflection = new ReflectionClass($className);
        if ($reflection->isAbstract() || $reflection->isInterface()) {
            $this->missingAction($namespace, $action);
        }

        return $reflection->newInstance($request, $response, $controller);
    }

    /**
     * Throws an exception when a controller is missing.
     *
     * @return void
     */
    protected function missingAction($namespace, $action)
    {
        throw new MissingActionClassException([
            $namespace . '\\' . $action . 'Action'
        ]);
    }
}