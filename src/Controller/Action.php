<?php
namespace HavokInspiration\ActionsClass\Controller;

use Cake\Controller\Controller;

abstract class Action extends Controller
{

    protected $controllerName;

    public function setControllerName($controllerName)
    {
        $this->controllerName = $controllerName;
    }

    /**
     * Get the viewPath based on controller name and request prefix.
     *
     * @return string
     */
    protected function _viewPath()
    {
        $viewPath = $this->controllerName;
        if ($this->request->getParam('prefix')) {
            $prefixes = array_map(
                'Cake\Utility\Inflector::camelize',
                explode('/', $this->request->getParam('prefix'))
            );
            $viewPath = implode(DIRECTORY_SEPARATOR, $prefixes) . DIRECTORY_SEPARATOR . $viewPath;
        }

        return $viewPath;
    }

    public function invokeAction()
    {
        $request = $this->request;
        /* @var callable $callable */
        $callable = [$this, 'execute'];

        return $callable(...array_values($request->getParam('pass')));
    }

    abstract function execute();
}