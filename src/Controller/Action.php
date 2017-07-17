<?php
namespace HavokInspiration\ActionsClass\Controller;

use Cake\Controller\Controller;

abstract class Action extends Controller
{

    public function invokeAction()
    {
        $request = $this->request;
        /* @var callable $callable */
        $callable = [$this, 'execute'];

        return $callable(...array_values($request->getParam('pass')));
    }

    abstract function execute();
}