<?php
namespace Search\Events;

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
     * @param  Cake\Event\Event     $event   Event object
     * @param  Cake\Network\Request $request Request object
     * @param  Cake\ORM\Entity      $options Entity options
     * @return undefined
     */
    public function getIndexMenuActions(Event $event, Request $request, $options)
    {
        $appView = new AppView();

        list($plugin, $controller) = pluginSplit($options['model']);

        $btnView = $appView->Html->link(
            '',
            ['plugin' => $plugin, 'controller' => $controller, 'action' => 'view', $options['entity']['id']],
            ['title' => __('View'), 'class' => 'btn btn-default glyphicon glyphicon-eye-open']
        );

        $menu = [
            [
                'label' => $btnView,
                'url' => [
                    'plugin' => $plugin,
                    'controller' => $controller,
                    'action' => 'view',
                    $options['entity']['id']
                ],
                'capabilities' => 'fromUrl'
            ]
        ];

        if ($appView->elementExists(static::MENU_ELEMENT)) {
            $result = $appView->element(static::MENU_ELEMENT, ['menu' => $menu, 'renderAs' => 'provided']);
        } else {
            $result = $btnView;
        }

        return $result;
    }
}
