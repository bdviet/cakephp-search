<?php
namespace Search\Controller;

use Search\Controller\Widgets\ReportWidget;
use Search\Controller\Widgets\SavedSearchWidget;

class WidgetFactory
{
    /**
     * create method assembles correct Widget by its type
     * @param array $widget with corresponding data
     * @param Cake\Request $request default
     * @param Cake\Response $response default
     * @param Cake\EventManager $em default
     * @return WidgetObject $wObject
     */
    public static function create($widget, $request, $response, $em)
    {
        $wObject = null;

        switch ($widget->widget_type) {
            case 'saved_search':
                $wObject = new SavedSearchWidget($request, $response, $em);
                break;
            case 'report':
                $wObject = new ReportWidget($request, $response, $em);
                break;
        }

        //setting Cake\Model\Entity to widget
        $wObject->setWidget($widget);

        //preparing|fetching required data
        //based on widget type and Cake\Model\Entity
        $wObject->prepareWidget();


        //returning defined object
        //that's ready to be rendered
        return $wObject;
    }
}
