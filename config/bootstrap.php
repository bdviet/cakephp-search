<?php
use Cake\Core\Configure;
use Cake\Event\EventManager;
use Search\Events\SearchViewMenuListener;

/*
dashboards columns
 */
Configure::write('Search.dashboard.columns', ['Left Side', 'Right Side']);

EventManager::instance()->on(new SearchViewMenuListener());
