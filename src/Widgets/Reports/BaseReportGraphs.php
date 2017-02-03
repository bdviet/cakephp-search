<?php
namespace Search\Widgets\Reports;

use Search\Widgets\Reports\ReportGraphsInterface;

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

    /**
     * @return array $_config of the reports.
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * setConfig.
     * Setting report configurations
     * @param array $data of report.
     * @return array $_config of the report.
     */
    public function setConfig($data = [])
    {
        $this->_config = $data;
    }
}
