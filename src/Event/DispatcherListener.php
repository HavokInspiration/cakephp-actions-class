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
namespace HavokInspiration\ActionsClass\Event;

use Cake\Core\Configure;
use Cake\Event\Event;
use HavokInspiration\ActionsClass\Controller\Action;
use HavokInspiration\ActionsClass\Http\ActionFactory;
use Cake\Event\EventListenerInterface;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use HavokInspiration\ActionsClass\Http\Exception\MissingActionClassException;

/**
 * Class DispatcherListener
 *
 * Event listener in charge of creating an Action object when the `Dispatcher.beforeDispatch` event is triggered
 * by the CakePHP dispatcher.
 */
class DispatcherListener implements EventListenerInterface
{

    /**
     * {@inheritDoc}
     */
    public function implementedEvents()
    {
        return [
            'Dispatcher.beforeDispatch' => 'beforeDispatch'
        ];
    }

    /**
     * Hook method called when a beforeDispatch event is triggered by the CakePHP Dispatcher.
     *
     * @param \Cake\Event\Event $event Instance of the Event being dispatched.
     * @param \Cake\Http\ServerRequest $request The request to build an action for.
     * @param \Cake\Http\Response $response The response to use.
     * @return \Cake\Event\Event
     */
    public function beforeDispatch(Event $event, ServerRequest $request, Response $response)
    {
        $action = null;

        try {
            $action = $this->createActionFactory($request, $response);
        } catch (MissingActionClassException $e) {
            if (Configure::read('ActionsClass.strictMode') === true) {
                throw $e;
            }
        }

        $event->setData('controller', $action);

        return $event;
    }

    /**
     * Create the Action object that will be used by the Dispatcher.
     *
     * @param \Cake\Http\ServerRequest $request The request to build an action for.
     * @param \Cake\Http\Response $response The response to use.
     * @return \HavokInspiration\ActionsClass\Controller\Action
     * @throws \HavokInspiration\ActionsClass\Http\Exception\MissingActionClassException In case the action can not be
     * found
     */
    protected function createActionFactory(ServerRequest $request, Response $response) : Action
    {
        $factory = new ActionFactory();
        $action = $factory->create($request, $response);

        return $action;
    }
}
