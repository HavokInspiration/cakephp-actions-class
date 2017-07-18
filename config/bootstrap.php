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