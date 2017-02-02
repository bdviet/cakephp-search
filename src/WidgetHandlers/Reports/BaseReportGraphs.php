<?php
namespace Search\WidgetHandlers\Reports;

use Search\WidgetHandlers\Reports\ReportGraphsInterface;

abstract class BaseReportGraphs implements ReportGraphsInterface
{
    const GRAPH_PREFIX = 'graph_';

    public $_type;
    public $_data = [];
    public $_report = [];
    public $_dataOptions = [];

    public function getType()
    {
        return $this->_type;
    }
}
