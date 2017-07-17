<?php
use Cake\Event\EventManager;

EventManager::instance()->on(new HavokInspiration\ActionsClass\Event\DispatcherListener());