<?php
namespace Search\WidgetHandlers;

use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\Utility\Inflector;
use Search\WidgetHandlers\BaseReportWidget;

class ReportWidgetHandler extends BaseReportWidget
{
    /** @TODO: this begs for exceptions and data checks */
    public function __construct($options = [])
    {
        $report = $this->getReportConfig($options);

        $this->_instance = $this->getReportInstance(['report' => $report]);
        $this->_instance->setReport($report);
        $this->_instance->setEntity($options['entity']);
    }

    public function getResults(array $options = [])
    {
        return $this->_instance->getResults($options);
    }
}
