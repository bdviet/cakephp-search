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

    public $chartData = [];
    public $containerId = '';
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
     * setContainerId method.
     * Sets the placeholder unique identifier for
     * the widget.
     * @param array $data of the config.
     * @return string $containerId of the object.
     */
    public function setContainerId($data = [])
    {
        $config = empty($data) ? $this->getConfig() : $data;

        $this->containerId = self::GRAPH_PREFIX . $config['slug'];

        return $this->containerId;
    }

    /**
     * @return string $containerId property of the widget.
     */
    public function getContainerId()
    {
        return $this->containerId;
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
