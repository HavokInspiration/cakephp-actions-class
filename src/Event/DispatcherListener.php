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