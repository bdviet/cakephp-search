<?php
namespace Search\WidgetHandlers;

/**
 * BaseReportWidget is responsible for all the
 * report widgets that render graphs.
 * We interact with the charts via $_instance
 * property which is an actual report chart (lineChart,barChart,etc.)
 *
 */

use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\Utility\Inflector;
use Search\WidgetHandlers\BaseWidget;

class BaseReportWidget extends BaseWidget
{
    protected $_type = 'report';

    public function getReport()
    {
        return $this->_instance->_report;
    }

    public function getData()
    {
        return $this->_instance->_data;
    }

    public function getDataOptions()
    {
        return $this->_instance->_dataOptions;
    }

    public function getChartType()
    {
        return $this->_instance->_type;
    }

    public function getReportConfig($options = [])
    {
        $config = [];
        $rootView = $options['rootView'];
        $event = new Event('Search.Report.getReports', $rootView);
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

    public function getReportInstance($options = [])
    {
        $result = null;

        if (empty($options['report'])) {
            return $result;
        }
        $renderAs = $options['report']['info']['renderAs'];

        if (!empty($renderAs)) {
            $handlerName = Inflector::camelize($renderAs);

            $className = __NAMESPACE__ . '\\' . $handlerName . self::WIDGET_SUFFIX;
            $interface = __NAMESPACE__ . '\\' . self::WIDGET_INTERFACE;
            if (class_exists($className) && in_array($interface, class_implements($className))) {
                return new $className($options);
            }
        }
    }


    public function setReport($report)
    {
        $this->_report = $report;
    }

    public function setEntity($entity)
    {
        $this->_entity = $entity;
    }

    public function getResults(array $options = [])
    {
        $result = [];
        $report = $this->_report;
        $columns = explode(',', $report['info']['columns']);

        $dbh = ConnectionManager::get('default');
        $sth = $dbh->execute($report['info']['query']);
        $resultSet = $sth->fetchAll('assoc');

        if (!empty($resultSet)) {
            foreach ($resultSet as $k => $row) {
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
            $this->_data = $this->prepareChartData($result);
        }

        $this->_dataOptions = $this->prepareChartOptions($this->_data);

        return $this->_data;
    }
}
