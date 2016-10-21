<?php
namespace Search\Controller;

use Search\Controller\Widgets\SavedSearchWidget;
use Search\Controller\Widgets\ReportWidget;


class WidgetFactory
{
    public static function create($widget, $request, $response, $em)
    {
        $wObject = null;

        switch($widget->widget_type) {
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
