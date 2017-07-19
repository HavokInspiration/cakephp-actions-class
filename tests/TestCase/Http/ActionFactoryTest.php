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
     * Test building an application controller
     *
     * @return void
     */
    public function testApplicationController()
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
        $this->assertSame($request, $result->request);
        $this->assertSame($this->response, $result->response);
    }

    /**
     * Test building a prefixed app controller.
     *
     * @return void
     */
    public function testPrefixedAppController()
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
        $this->assertSame($request, $result->request);
        $this->assertSame($this->response, $result->response);
    }

    /**
     * Test building a nested prefix app controller
     *
     * @return void
     */
    public function testNestedPrefixedAppController()
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
        $this->assertSame($request, $result->request);
        $this->assertSame($this->response, $result->response);
    }

    /**
     * Test building a plugin controller
     *
     * @return void
     */
    public function testPluginController()
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
     * Test building a vendored plugin controller.
     *
     * @return void
     */
    public function testVendorPluginController()
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
     * Test building a prefixed plugin controller
     *
     * @return void
     */
    public function testPrefixedPluginController()
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

//    /**
//     * @expectedException \Cake\Routing\Exception\MissingControllerException
//     * @expectedExceptionMessage Controller class Admin/Posts could not be found.
//     * @return void
//     */
//    public function testSlashedControllerFailure()
//    {
//        $request = new ServerRequest([
//            'url' => 'admin/posts/index',
//            'params' => [
//                'controller' => 'Admin/Posts',
//                'action' => 'index',
//            ]
//        ]);
//        $this->factory->create($request, $this->response);
//    }

    /**
     * @expectedException \HavokInspiration\ActionsClass\Http\Exception\MissingActionClassException
     * @expectedExceptionMessage Action class Controller\TestApp\Controller\Cakes\IndexAction could not be found.
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
