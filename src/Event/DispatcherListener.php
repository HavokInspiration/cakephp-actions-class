<?php
namespace HavokInspiration\ActionsClass\Event;

use Cake\Event\Event;
use HavokInspiration\ActionsClass\Http\ActionFactory;
use Cake\Event\EventListenerInterface;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use HavokInspiration\ActionsClass\Http\Exception\MissingActionClassException;

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
    
    public function beforeDispatch(Event $event, ServerRequest $request, Response $response)
    {
        try {
            $factory = new ActionFactory();
            $action = $factory->create($request, $response);
            $event->setData('controller', $action);
        } catch (MissingActionClassException $e) {
            $event->setData('controller', null);
        }

        return $event;
    }
}