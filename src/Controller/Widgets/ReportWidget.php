<?php
namespace Search\Controller\Widgets;

use Cake\Event\Event;
use Cake\View\Cell;
use Search\Controller\AbstractWidget;

class ReportWidget extends AbstractWidget
{

    /**
     * Default construct
     *
     * @param Cake\Request $request from controller
     * @param Cake\Response $response from controller
     * @param Cake\EventManager $em from controller
     * @param array $cellOptions for extras if any
     * @return
     */
    public function __construct($request, $response, $em, $cellOptions = [])
    {
        parent::__construct($request, $response, $em, $cellOptions);
    }

    /**
     * preparing widgetData for execution by the cells
     * @return void
     */
    public function prepareWidget()
    {
        $result = [];
        $event = new Event('Search.Report.getReports', $this);
        $this->em->dispatch($event);

        $widgetId = $this->widgetObject['widget_id'];


        if (!empty($event->result)) {
            foreach ($event->result as $modelName => $reports) {
                foreach ($reports as $slug => $reportInfo) {
                    if ($reportInfo['id'] == $widgetId) {
                        $result = [
                            'modelName' => $modelName,
                            'slug' => $slug,
                            'info' => $reportInfo
                        ];
                    }
                }
            }
        }

        $this->setWidgetDisplayMethod();
        $this->widgetData = $result;
    }
}
