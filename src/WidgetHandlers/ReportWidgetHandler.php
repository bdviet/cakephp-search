<?php
namespace Search\WidgetHandlers;

use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\Utility\Inflector;
use Search\WidgetHandlers\BaseWidgetHandler;

class ReportWidgetHandler extends BaseWidgetHandler
{
    public $renderElement = 'graph';

    /**
     * @return array $report configuration.
     */
    public function getReport()
    {
        return $this->_instance->_report;
    }

    /**
     * @return array $data of the report.
     */
    public function getData()
    {
        return $this->_instance->_data;
    }

    /**
     * @return array $_dataOptions of the widget for rendering.
     */
    public function getDataOptions()
    {
        return $this->_instance->_dataOptions;
    }

    /**
     * @return string $type of the Report widget.
     */
    public function getType()
    {
        return $this->_instance->_type;
    }

    /**
     * getScripts method.
     *
     * @param array $options with data.
     * @return array $_dataOptions.
     */
    public function getScripts(array $options = [])
    {
        return $this->_instance->getScripts(['data' => $options]);
    }

    /**
     * @return array $chartData of the instance.
     */
    public function getChartData(array $data = [])
    {
        return $this->_instance->getChartData($data);
    }

    /**
     * Setting report configuration to the report instance.
     *
     * @param array $report to be set for _report property.
     * @retrun array $report config of the widget.
     */
    public function setReport($report)
    {
        $this->_instance->_report = $report;
    }

    /**
     * setData method
     * @param array $data containing widget data.
     * @return array $_data after being set.
     */
    public function setData($data = [])
    {
        $this->_instance->_data = $data;

        return $this->_instance->_data;
    }

    /**
     * setDataOptions method
     * Setting up report JS/CSS libs.
     * @param array $data for being set.
     * @return array $_dataOptions property.
     */
    public function setDataOptions($data = [])
    {
        $this->_instance->_dataOptions = $data;

        return $this->_instance->_dataOptions;
    }

    /**
     * getReportConfig method
     * Parses the config of the report for widgetHandler.
     * @param array $options with entity data.
     * @return array $config of the $_report.
     */
    public function getReportConfig($options = [])
    {
        $config = [];

        if (empty($options['rootView'])) {
            return $config;
        }
        $rootView = $options['rootView'];
        $event = new Event('Search.Report.getReports', $rootView->request);
        $rootView->EventManager()->dispatch($event);

        $widgetId = $options['entity']->widget_id;

        if (!empty($event->result)) {
            foreach ($event->result as $modelName => $reports) {
                foreach ($reports as $slug => $reportInfo) {
                    if ($reportInfo['id'] == $widgetId) {
                        $config = [
                            'modelName' => $modelName,
                            'slug' => $slug,
                            'info' => $reportInfo
                        ];
                    }
                }
            }
        }

        return $config;
    }

    /**
     * ReportWidgetHandler operates via $_instance variable
     * that we set based on the renderAs parameter of the report.
     * @param array $options containing reports
     * @return mixed $className of the $_instance.
     */
    public function getReportInstance($options = [])
    {
        $result = null;

        $options['report'] = $this->getReportConfig($options);

        if (empty($options['report'])) {
            return $result;
        }
        $renderAs = $options['report']['info']['renderAs'];

        if (!empty($renderAs)) {
            $handlerName = Inflector::camelize($renderAs);

            $className = __NAMESPACE__ . '\\Reports\\' . $handlerName . self::WIDGET_REPORT_SUFFIX;
            $interface = __NAMESPACE__ . '\\Reports\\' . 'ReportGraphsInterface';

            if (class_exists($className) && in_array($interface, class_implements($className))) {
                return new $className($options);
            }
        }
    }

    /**
     * getResults method
     *
     * Establish report data for the widgetHandler.
     *
     * @param array $options with entity and view data.
     * @return array $result containing $_data.
     */
    public function getResults(array $options = [])
    {
        $result = [];
        $this->_instance = $this->getReportInstance($options);

        $report = $this->getReportConfig($options);

        $this->setReport($report);

        $columns = explode(',', $report['info']['columns']);

        $dbh = ConnectionManager::get('default');
        $sth = $dbh->execute($report['info']['query']);
        $resultSet = $sth->fetchAll('assoc');

        if (!empty($resultSet)) {
            foreach ($resultSet as $row) {
                $renderRow = [];
                foreach ($row as $column => $value) {
                    if (in_array($column, $columns)) {
                        $renderRow[$column] = $value;
                    }
                }
                array_push($result, $renderRow);
            }
        }

        if (!empty($result)) {
            $data = $this->getChartData($result);
            $dataOptions = $this->getScripts(['data' => $data]);

            $this->setData($data);
            $this->setDataOptions($dataOptions);
        }

        return $this->getData();
    }
}
