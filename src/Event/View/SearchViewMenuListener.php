<?php
namespace Search\Event\View;

use App\View\AppView;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Network\Request;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

class SearchViewMenuListener implements EventListenerInterface
{
    /**
     * Menu element name
     */
    const MENU_ELEMENT = 'Menu.menu';

    /**
     * Implemented Events
     *
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Search.View.View.Menu.Actions' => 'getIndexMenuActions'
        ];
    }

    /**
     * Method that adds elements to index View actions menu.
     *
     * @param  \Cake\Event\Event      $event  Event object
     * @param  \Cake\ORM\Entity|array $entity Entity
     * @param  string                 $model  Model name
     * @return void
     */
    public function getIndexMenuActions(Event $event, $entity, $model)
    {
        if ($entity instanceof Entity) {
            $entity = $entity->toArray();
        }

        list($plugin, $controller) = pluginSplit($model);

        $btnView = $event->subject()->Html->link(
            '',
            ['plugin' => $plugin, 'controller' => $controller, 'action' => 'view', $entity['id']],
            ['title' => __('View'), 'class' => 'btn btn-default glyphicon glyphicon-eye-open']
        );

        $menu = [
            [
                'label' => $btnView,
                'url' => [
                    'plugin' => $plugin,
                    'controller' => $controller,
                    'action' => 'view',
                    $entity['id']
                ],
                'capabilities' => 'fromUrl'
            ]
        ];

        if ($event->subject()->elementExists(static::MENU_ELEMENT)) {
            $event->result .= $event->subject()->element(static::MENU_ELEMENT, ['menu' => $menu, 'renderAs' => 'provided']);
        } else {
            $event->result .= $btnView;
        }
    }
}
