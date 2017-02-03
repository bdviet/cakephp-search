<?php
namespace Search\WidgetHandlers\Reports;

use Search\WidgetHandlers\Reports\ReportGraphsInterface;

abstract class BaseReportGraphs implements ReportGraphsInterface
{
    const GRAPH_PREFIX = 'graph_';

    public $_type;
    public $_data = [];
    public $_config = [];
    public $_dataOptions = [];

    /**
     * getType
     *
     * Returns Chart type
     * @return string $type of the report instance.
     */
    public function getType()
    {
        return $this->_type;
    }

    public function getConfig()
    {
        return $this->_config;
    }

    public function setConfig($data = [])
    {
        $this->_config = $data;
    }
}
