<?php
namespace HavokInspiration\ActionsClass\Event;

use Cake\Core\Configure;
use Cake\Event\Event;
use HavokInspiration\ActionsClass\Controller\Action;
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
        $action = null;

        if (Configure::read('ActionsClass.strictMode') === true) {
            $action = $this->createActionFactory($request, $response);
        } else {
            try {
                $action = $this->createActionFactory($request, $response);
            } catch (MissingActionClassException $e) {
                // Do not do anything, let it fallback to CakePHP default dispatching cycle.
            }
        }

        $event->setData('controller', $action);

        return $event;
    }

    protected function createActionFactory(ServerRequest $request, Response $response) : Action
    {
        $factory = new ActionFactory();
        $action = $factory->create($request, $response);

        return $action;
    }
}