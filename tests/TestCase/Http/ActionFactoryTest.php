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
namespace HavokInspiration\ActionsClass\Test\TestCase\Http;

use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use HavokInspiration\ActionsClass\Http\ActionFactory;

class ActionFactoryTest extends TestCase
{

    protected $factory;

    protected $response;


    /**
     * Setup
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        static::setAppNamespace();
        $this->factory = new ActionFactory();
        $this->response = $this->getMockBuilder('Cake\Http\Response')->getMock();
    }

    /**
     * Test building an application action
     *
     * @return void
     */
    public function testApplicationAction()
    {
        $request = new ServerRequest([
            'url' => 'cakes/index',
            'params' => [
                'controller' => 'Cakes',
                'action' => 'index',
            ]
        ]);
        $result = $this->factory->create($request, $this->response);
        $this->assertInstanceOf('TestApp\Controller\Cakes\IndexAction', $result);
        $this->assertEquals('Index', $result->name);
        $this->assertEquals('Cakes', $result->getControllerName());
        $this->assertSame($request, $result->request);
        $this->assertSame($this->response, $result->response);
    }

    /**
     * Test building a prefixed app action.
     *
     * @return void
     */
    public function testPrefixedAppAction()
    {
        $request = new ServerRequest([
            'url' => 'admin/posts/index',
            'params' => [
                'prefix' => 'admin',
                'controller' => 'Posts',
                'action' => 'index',
            ]
        ]);
        $result = $this->factory->create($request, $this->response);
        $this->assertInstanceOf(
            'TestApp\Controller\Admin\Posts\IndexAction',
            $result
        );
        $this->assertEquals('Posts', $result->getControllerName());
        $this->assertSame($request, $result->request);
        $this->assertSame($this->response, $result->response);
    }

    /**
     * Test building a nested prefix app action
     *
     * @return void
     */
    public function testNestedPrefixedAppAction()
    {
        $request = new ServerRequest([
            'url' => 'admin/sub/posts/index',
            'params' => [
                'prefix' => 'admin/sub',
                'controller' => 'Posts',
                'action' => 'index',
            ]
        ]);
        $result = $this->factory->create($request, $this->response);
        $this->assertInstanceOf(
            'TestApp\Controller\Admin\Sub\Posts\IndexAction',
            $result
        );
        $this->assertEquals('Posts', $result->getControllerName());
        $this->assertSame($request, $result->request);
        $this->assertSame($this->response, $result->response);
    }

    /**
     * Test building a plugin action
     *
     * @return void
     */
    public function testPluginAction()
    {
        $request = new ServerRequest([
            'url' => 'test_plugin/test_plugin/index',
            'params' => [
                'plugin' => 'TestPlugin',
                'controller' => 'TestPlugin',
                'action' => 'index',
            ]
        ]);
        $result = $this->factory->create($request, $this->response);
        $this->assertInstanceOf(
            'TestPlugin\Controller\TestPlugin\IndexAction',
            $result
        );
        $this->assertSame($request, $result->request);
        $this->assertSame($this->response, $result->response);
    }

    /**
     * Test building a vendored plugin action.
     *
     * @return void
     */
    public function testVendorPluginAction()
    {
        $request = new ServerRequest([
            'url' => 'test_plugin_three/ovens/index',
            'params' => [
                'plugin' => 'Company/TestPluginThree',
                'controller' => 'Ovens',
                'action' => 'index',
            ]
        ]);
        $result = $this->factory->create($request, $this->response);
        $this->assertInstanceOf(
            'Company\TestPluginThree\Controller\Ovens\IndexAction',
            $result
        );
        $this->assertSame($request, $result->request);
        $this->assertSame($this->response, $result->response);
    }

    /**
     * Test building a prefixed plugin action
     *
     * @return void
     */
    public function testPrefixedPluginAction()
    {
        $request = new ServerRequest([
            'url' => 'test_plugin/admin/comments',
            'params' => [
                'prefix' => 'admin',
                'plugin' => 'TestPlugin',
                'controller' => 'Comments',
                'action' => 'index',
            ]
        ]);
        $result = $this->factory->create($request, $this->response);
        $this->assertInstanceOf(
            'TestPlugin\Controller\Admin\Comments\IndexAction',
            $result
        );
        $this->assertSame($request, $result->request);
        $this->assertSame($this->response, $result->response);
    }

    /**
     * Test that trying to load an existing action that is abstract will throw an exception.
     *
     * @expectedException \HavokInspiration\ActionsClass\Http\Exception\MissingActionClassException
     * @expectedExceptionMessage Action class Controller\Invalid\AbstractAction could not be found.
     * @return void
     */
    public function testAbstractClassFailure()
    {
        $request = new ServerRequest([
            'url' => 'invalid/abstract',
            'params' => [
                'controller' => 'Invalid',
                'action' => 'Abstract',
            ]
        ]);
        $this->factory->create($request, $this->response);
    }

    /**
     * Test that trying to load an existing action that is an interface will throw an exception.
     *
     * @expectedException \HavokInspiration\ActionsClass\Http\Exception\MissingActionClassException
     * @expectedExceptionMessage Action class Controller\Invalid\InterfaceAction could not be found.
     * @return void
     */
    public function testInterfaceFailure()
    {
        $request = new ServerRequest([
            'url' => 'invalid/interface',
            'params' => [
                'controller' => 'Invalid',
                'action' => 'Interface',
            ]
        ]);
        $this->factory->create($request, $this->response);
    }

    /**
     * That that trying to load a missing class will throw an exception.
     *
     * @expectedException \HavokInspiration\ActionsClass\Http\Exception\MissingActionClassException
     * @expectedExceptionMessage Action class Controller\Invisible\IndexAction could not be found.
     * @return void
     */
    public function testMissingClassFailure()
    {
        $request = new ServerRequest([
            'url' => 'interface/index',
            'params' => [
                'controller' => 'Invisible',
                'action' => 'index',
            ]
        ]);
        $this->factory->create($request, $this->response);
    }

    /**
     * Test that having a slash in the controller name will throw an exception.
     *
     * @expectedException \HavokInspiration\ActionsClass\Http\Exception\MissingActionClassException
     * @expectedExceptionMessage Action class Controller\Admin/PostsAction could not be found.
     * @return void
     */
    public function testSlashedControllerFailure()
    {
        $request = new ServerRequest([
            'url' => 'admin/posts/index',
            'params' => [
                'controller' => 'Admin/Posts',
                'action' => 'index',
            ]
        ]);
        $this->factory->create($request, $this->response);
    }

    /**
     * Test that trying to load an absolute namespace path in the controller will throw an exception.
     *
     * @expectedException \HavokInspiration\ActionsClass\Http\Exception\MissingActionClassException
     * @expectedExceptionMessage Action class Controller\TestApp\Controller\CakesAction could not be found.
     * @return void
     */
    public function testAbsoluteReferenceFailure()
    {
        $request = new ServerRequest([
            'url' => 'interface/index',
            'params' => [
                'controller' => 'TestApp\Controller\Cakes',
                'action' => 'index',
            ]
        ]);
        $this->factory->create($request, $this->response);
    }
    
    /**
     * Test that trying to load an absolute namespace path in the action will throw an exception.
     *
     * @expectedException \HavokInspiration\ActionsClass\Http\Exception\MissingActionClassException
     * @expectedExceptionMessage Action class Controller\Admin\Posts\IndexAction could not be found.
     * @return void
     */
    public function testAbsoluteReferenceInActionFailure()
    {
        $request = new ServerRequest([
            'url' => 'interface/index',
            'params' => [
                'controller' => 'Admin',
                'action' => 'Posts\Index',
            ]
        ]);
        $this->factory->create($request, $this->response);
    }
}
