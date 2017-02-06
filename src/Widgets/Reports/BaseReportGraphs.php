<?php
namespace Search\Widgets\Reports;

use Search\Widgets\Reports\ReportGraphsInterface;

abstract class BaseReportGraphs implements ReportGraphsInterface
{
    const GRAPH_PREFIX = 'graph_';

    public $containerId = '';
    public $type = null;
    public $config = [];
    public $options = [];
    public $data = [];
    public $errors = [];
    /**
     * getType
     *
     * Returns Chart type
     * @return string $type of the report instance.
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array $_config of the reports.
     */
    public function getConfig()
    {
        return $this->config;
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
        $this->config = $data;
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

    /**
     * @return array $errors in case any exists.
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * validate method.
     * Checks all the required fields of the report if any.
     */
    public function validate(array $data = [])
    {
        $validated = false;
        $errors = [];

        if (!isset($this->requiredFields)) {
            $errors[] = "Missing requiredFields in the report object";
        }

        foreach ($this->requiredFields as $field) {
            if (!isset($data['info'][$field])) {
                $errors[] = "Required field [$field] must be set";
            }

            if (empty($data['info'][$field])) {
                $errors[] = "Required Field [$field] cannot be empty";
            }
        }

        if (empty($errors)) {
            $validated = true;
            $this->errors = [];
        } else {
            $this->errors = $errors;
        }

        return ['status' => $validated, 'messages' => $errors];
    }
}
