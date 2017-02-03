<?php
namespace Search\Widgets\Reports;

use Search\Widgets\Reports\ReportGraphsInterface;

abstract class BaseReportGraphs implements ReportGraphsInterface
{
    const GRAPH_PREFIX = 'graph_';

    public $containerId = '';
    public $_type = null;
    public $_data = [];
    public $_config = [];
    public $options = [];
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
     * @return array $options
     */
    public function getOptions()
    {
        return $this->options;
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

    /**
     * setData method.
     * @param array $data for the report widget.
     * @return void.
     */
    public function setData($data = [])
    {
        $this->data = $data;
    }

    /**
     * @return array $data of the widget.
     */
    public function getData()
    {
        return $this->data;
    }
}
