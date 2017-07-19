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
namespace HavokInspiration\ActionsClass\Test\TestCase\Event;

use Cake\Core\Configure;
use Cake\Event\EventDispatcherTrait;
use Cake\Event\EventManager;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use HavokInspiration\ActionsClass\Event\DispatcherListener;

class DispatcherListenerTest extends TestCase
{

    use EventDispatcherTrait;

    protected $response;

    /**
     * setUp.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        static::setAppNamespace();
        $this->response = $this->getMockBuilder('Cake\Http\Response')->getMock();
    }

    /**
     * Tests that dispatching an event with an existing action will return the correct instance in the `controller` key
     * of the returned data.
     *
     * @return void
     */
    public function testDispatchingEventWithExistingAction()
    {
        $request = new ServerRequest([
            'url' => 'cakes/index',
            'params' => [
                'controller' => 'Cakes',
                'action' => 'index',
            ]
        ]);

        EventManager::instance()->on(new DispatcherListener());
        $beforeEvent = $this->dispatchEvent(
            'Dispatcher.beforeDispatch',
            [
                'request' => $request,
                'response' => $this->response
            ]
        );

        $this->assertInstanceOf('TestApp\Controller\Cakes\IndexAction', $beforeEvent->getData('controller'));
    }

    /**
     * Tests that dispatching an event with a missing action will return `null` in the `controller` key of the returned
     * data if strict mode is disabled (meaning CakePHP will try its regular routine to load a "regular controller"
     * object).
     *
     * @return void
     */
    public function testDispatchingEventWithMissingActionNoStrictMode()
    {
        $request = new ServerRequest([
            'url' => 'cakes/index',
            'params' => [
                'controller' => 'Cakes',
                'action' => 'notHere',
            ]
        ]);

        EventManager::instance()->on(new DispatcherListener());
        $beforeEvent = $this->dispatchEvent(
            'Dispatcher.beforeDispatch',
            [
                'request' => $request,
                'response' => $this->response
            ]
        );

        $this->assertNull($beforeEvent->getData('controller'));
    }

    /**
     * Tests that dispatching an event with a missing action will throw an exception if strict mode is on.
     *
     * @expectedException \HavokInspiration\ActionsClass\Http\Exception\MissingActionClassException
     * @expectedExceptionMessage Action class Controller\Cakes\NotHereAction could not be found.
     * @return void
     */
    public function testDispatchingEventWithMissingActionWithStrictMode()
    {
        $request = new ServerRequest([
            'url' => 'cakes/index',
            'params' => [
                'controller' => 'Cakes',
                'action' => 'notHere',
            ]
        ]);

        Configure::write('ActionsClass.strictMode', true);
        EventManager::instance()->on(new DispatcherListener());
        $beforeEvent = $this->dispatchEvent(
            'Dispatcher.beforeDispatch',
            [
                'request' => $request,
                'response' => $this->response
            ]
        );
    }
}
