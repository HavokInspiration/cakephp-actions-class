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
namespace HavokInspiration\ActionsClass\Test\TestCase\Controller;

use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use HavokInspiration\ActionsClass\Http\ActionFactory;

class ActionTest extends TestCase
{

    protected $response;
    protected $action;

    /**
     * setUp.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        static::setAppNamespace();

        $request = new ServerRequest([
            'url' => 'cakes/add',
            'params' => [
                'controller' => 'Cakes',
                'action' => 'add',
            ]
        ]);
        $this->response = $this->getMockBuilder('Cake\Http\Response')->getMock();
        $this->action = (new ActionFactory())->create($request, $this->response);
    }

    /**
     * Test that an action without an execute method will throw an exception.
     *
     * @expectedException \LogicException
     * @expectedExceptionMessage Your class `TestApp\Controller\Cakes\AddAction` should implement an `execute()` method
     * @return void
     */
    public function testInvokeActionMissingExecuteMethod()
    {
        $this->action->invokeAction();
    }

    /**
     * Test that an action without a request object will throw an exception.
     *
     * @expectedException \LogicException
     * @expectedExceptionMessage No Request object configured. Cannot invoke action
     * @return void
     */
    public function testInvokeActionMissingRequest()
    {
        $this->action->request = null;
        $this->action->invokeAction();
    }

    /**
     * Test that an action will run its `execute` method.
     *
     * @return void
     */
    public function testInvokeAction()
    {
        $request = new ServerRequest([
            'url' => 'cakes/index',
            'params' => [
                'controller' => 'Cakes',
                'action' => 'index',
                'pass' => []
            ]
        ]);
        $this->response = $this->getMockBuilder('Cake\Http\Response')->getMock();
        $this->action = (new ActionFactory())->create($request, $this->response);
        $this->action->invokeAction();

        $this->assertEquals('executed', $this->action->someProperty);
        $this->assertEquals('Cakes', $this->action->viewPath);
    }

    /**
     * Test that a prefixed action will have its prefix in the viewPath.
     *
     * @return void
     */
    public function testInvokePrefixedAction()
    {
        $request = new ServerRequest([
            'url' => 'admin/sub/posts/index',
            'params' => [
                'prefix' => 'admin/sub',
                'controller' => 'Posts',
                'action' => 'index',
                'pass' => [
                    'order' => 'desc',
                    'limit' => 500
                ]
            ]
        ]);
        $this->response = $this->getMockBuilder('Cake\Http\Response')->getMock();
        $this->action = (new ActionFactory())->create($request, $this->response);
        $this->action->invokeAction();

        $this->assertEquals('executed', $this->action->someProperty);
        $this->assertEquals('Admin/Sub/Posts', $this->action->viewPath);

        $expected = [
            'order' => 'desc',
            'limit' => 500
        ];
        $this->assertEquals($expected, $this->action->passed);
    }
}
