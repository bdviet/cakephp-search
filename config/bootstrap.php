<?php
use Cake\Core\Configure;
use Cake\Event\EventManager;
use Search\Events\DashboardViewMenuListener;
use Search\Events\SearchFormMenuListener;
use Search\Events\SearchViewMenuListener;

/*
dashboards columns
 */
Configure::write('Search.dashboard.columns', ['Left Side', 'Right Side']);

EventManager::instance()->on(new DashboardViewMenuListener());
EventManager::instance()->on(new SearchFormMenuListener());
EventManager::instance()->on(new SearchViewMenuListener());
