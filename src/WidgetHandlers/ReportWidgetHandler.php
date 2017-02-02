<?php
namespace Search\WidgetHandlers;

use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\Utility\Inflector;
use Search\WidgetHandlers\BaseWidgetHandler;

class ReportWidgetHandler extends BaseWidgetHandler
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

    public function prepareChartOptions($options = [])
    {
        return $this->_instance->prepareChartOptions($options);
    }

    public function prepareChartData($data = [])
    {
        return $this->_instance->prepareChartData($data);
    }

    public function setReport($report)
    {
        $this->_instance->_report = $report;
    }

    public function setData($data = [])
    {
        $this->_instance->_data = $data;

        return $this->_instance->_data;
    }

    public function setDataOptions($data = [])
    {
        $this->_instance->_dataOptions = $data;

        return $this->_instance->_dataOptions;
    }

    public function getReportConfig($options = [])
    {
        $config = [];
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
            $interface = __NAMESPACE__ . '\\' . self::WIDGET_INTERFACE;

            if (class_exists($className) && in_array($interface, class_implements($className))) {
                return new $className($options);
            }
        }
    }



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
            $data = $this->prepareChartData($result);
            $dataOptions = $this->prepareChartOptions($data);

            $this->setData($data);
            $this->setDataOptions($dataOptions);
        }

        return $this->getData();
    }
}
