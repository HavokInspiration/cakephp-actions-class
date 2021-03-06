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
namespace HavokInspiration\ActionsClass\Controller;

use Cake\Controller\Controller;
use LogicException;

/**
 * Class Action
 *
 * This is the base class your action classes should extend.
 * Implementing a method `execute()` is the only requirement. The `execute()` method is the same thing as the action
 * method you would use in a CakePHP Controller.
 * Everything else will work as if you were using a CakePHP Controller.
 */
abstract class Action extends Controller
{

    /**
     * Controller name this action is binded to.
     *
     * @var string|null
     */
    protected $controllerName = null;

    /**
     * Returns the controller name this action is binded to.
     *
     * @return string|null Null if controllerName have not been resolved when the class was created.
     */
    public function getControllerName()
    {
        return $this->controllerName;
    }

    /**
     * Sets the controller name this action is binded to.
     *
     * @param string $controllerName
     * @return \HavokInspiration\ActionsClass\Controller\Action
     */
    public function setControllerName(string $controllerName) : Action
    {
        $this->controllerName = $controllerName;
        $this->modelClass = $controllerName;

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * Override in order for the method to use the `controllerName` property of the object instead of `name` (which
     * is used to store the action name) to append to the view path returned.
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

    /**
     * {@inheritDoc}
     *
     * Rewrite it as we only expect a method 'execute' and not an action name that is auto-resolved.
     */
    public function invokeAction()
    {
        $request = $this->request;
        if (!isset($request)) {
            throw new LogicException('No Request object configured. Cannot invoke action');
        }

        if (!method_exists($this, 'execute')) {
            throw new LogicException(
                sprintf(
                    'Your class `%s` should implement an `execute()` method',
                    get_class($this)
                )
            );
        }

        /* @var callable $callable */
        $callable = [$this, 'execute'];

        return $callable(...array_values($request->getParam('pass')));
    }

    /**
     * This method should be no-op as an action can not redirect to another action.
     *
     * @param string $action The new action to be 'redirected' to.
     *   Any other parameters passed to this method will be passed as parameters to the new action.
     * @param array ...$args Arguments passed to the action
     * @return void
     */
    public function setAction($action, ...$args)
    {
    }

    /**
     * This method should be no-op as we already are in an action.
     *
     * @param string $action The action to check.
     * @return void
     */
    public function isAction($action)
    {
    }
}
