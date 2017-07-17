<?php
namespace HavokInspiration\ActionsClass\Controller;

use Cake\Controller\Controller;

abstract class BaseAction extends Controller
{

    public function invokeAction()
    {

    }

    abstract function execute();
}