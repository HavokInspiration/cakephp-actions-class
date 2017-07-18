<?php
use Cake\Core\Configure;
use Cake\Event\EventManager;

/**
 * Options available :
 * - `strictMode` : a bool value. If set to true, the entire application will have to use actions classes. If set to
 *   false and an action class does not exist, it will fallback to the default CakePHP dispatching cycle (meaning it
 *   will look for a Controller.
 */
Configure::write('ActionsClass', [
    'strictMode' => false
]);

/**
 * Bind an event listener that will react to the "Dispatcher.beforeDispatch" triggered when the CakePHP
 * dispatchers dispatches the request
 */
EventManager::instance()->on(new HavokInspiration\ActionsClass\Event\DispatcherListener());