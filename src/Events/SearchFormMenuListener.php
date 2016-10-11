<?php
namespace Search\Events;

use App\View\AppView;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Network\Request;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Search\Controller\Traits\SearchableTrait;

class SearchFormMenuListener implements EventListenerInterface
{
    use SearchableTrait;

    /**
     * Menu element name
     */
    const MENU_ELEMENT = 'Search.basic_search';

    /**
     * Implemented Events
     *
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'QoboAdminPanel.Element.Navbar.Menu.Top' => 'getElementNavbarMenuTop'
        ];
    }

    /**
     * Method that adds elements to index View top menu.
     *
     * @param  Cake\Event\Event     $event   Event object
     * @return Cake\Event\Event
     */
    public function getElementNavbarMenuTop(Event $event)
    {
        $tableName = $event->data['request']->params['controller'];

        if (!is_null($event->data['request']->params['plugin'])) {
            $tableName = $event->data['request']->params['plugin'] . '.' . $tableName;
        }

        if (!$this->isSearchable($tableName)) {
            return $event->result;
        }

        if ($event->subject()->elementExists(static::MENU_ELEMENT)) {
            $event->result .= $event->subject()->element(static::MENU_ELEMENT);
        }

        return $event->result;
    }
}
