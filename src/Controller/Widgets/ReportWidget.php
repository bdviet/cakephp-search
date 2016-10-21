<?php
namespace Search\Controller\Widgets;

use Search\Controller\AbstractWidget;
use Cake\View\Cell;
use Cake\Event\Event;

class ReportWidget extends AbstractWidget {

    public function __construct($request, $response, $em, $cellOptions = [] )
    {
        parent::__construct($request, $response, $em, $cellOptions);
    }

    /**
    * preparing widgetData for execution by the cells
    * @return Array $this->widgetData
    */
    public function prepareWidget()
    {
        $result = [];
        $event = new Event('Search.Report.getReports', $this);
        $this->em->dispatch($event);

        $widget_id = $this->widgetObject['widget_id'];


        if( !empty($event->result) ) {
            foreach($event->result as $modelName => $reports) {
                foreach($reports as $slug => $reportInfo) {
                    if( $reportInfo['id'] == $widget_id ) {
                        $result = [
                            'modelName' => $modelName,
                            'slug'      => $slug,
                            'info'      => $reportInfo
                        ];

                    }
                }
            }
        }

        $this->setWidgetDisplayMethod();
        $this->widgetData = $result;
    }
}
