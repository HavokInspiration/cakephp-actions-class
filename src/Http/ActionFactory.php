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
            $this->missingAction($request);
        }

        $className = App::className($pluginPath . $action, $namespace, 'Action');
        if (!$className) {
            $this->missingAction($request);
        }
        $reflection = new ReflectionClass($className);
        if ($reflection->isAbstract() || $reflection->isInterface()) {
            $this->missingAction($request);
        }

        return $reflection->newInstance($request, $response, $controller);
    }

    /**
     * Throws an exception when a controller is missing.
     *
     * @param \Cake\Http\ServerRequest $request The request.
     * @throws \Cake\Routing\Exception\MissingControllerException
     * @return void
     */
    protected function missingAction($request)
    {
        throw new MissingActionClassException([
            'class' => $request->getParam('controller'),
            'plugin' => $request->getParam('plugin'),
            'prefix' => $request->getParam('prefix'),
            '_ext' => $request->getParam('_ext')
        ]);
    }
}