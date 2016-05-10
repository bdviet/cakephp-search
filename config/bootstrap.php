<?php
use Cake\Event\EventManager;
use Search\Events\SearchViewMenuListener;

EventManager::instance()->on(new SearchViewMenuListener());
