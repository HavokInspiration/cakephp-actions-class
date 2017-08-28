<?php
namespace <%= $namespace %>\Controller<%= $prefix %>\<%= $controller %>;

use HavokInspiration\ActionsClass\Controller\Action;

/**
 * Controller : <%= $controller %>
 * Action : <%= $action %>
 *
 * @package <%= $namespace %>\Controller
 */
class <%= $action %>Action extends Action
{
    /**
     * This method will be executed when the `<%= $controller %>` Controller action `<%= $action %>` will be invoked.
     * It is the equivalent of the `<%= $controller %>Controller::<%= $action %>()` method.
     *
     * @return void|\Cake\Network\Response
     */
    public function execute()
    {

    }
}
