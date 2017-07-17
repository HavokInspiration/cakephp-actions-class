<?php
namespace HavokInspiration\ActionsClass\Controller;

use Cake\Controller\Controller;

abstract class Action extends Controller
{

    public function invokeAction()
    {

    }

    abstract function execute();
}