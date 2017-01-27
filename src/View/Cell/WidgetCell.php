<?php
namespace Search\View\Cell;

use Cake\Datasource\ConnectionManager;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Cake\View\Cell;
use Search\Model\Entity\SavedSearch;

/**
 * Widget cell
 */
class WidgetCell extends Cell
{

    const GRAPH_PREFIX = 'graph_';

    /**
     * List of valid options that can be passed into this
     * cell's constructor.
     *
     * @var array
     */
    protected $_validCellOptions = [];

    /**
     * Storing Array dara on the chart settings
     * @var array
     */
    public $chartData = null;

    /**
     * Default display method.
     *
     * @param array $widgets passed
     * @return void
     */
    public function display(array $widgets)
    {
    }

    /**
     * displayReport method for widgets with type 'report'
     *
     * @param array $widgets with all widgets
     * @param array $options with mixed options
     * @return void
     */
    public function displayReport(array $widgets, array $options)
    {
        $renderData = [];
        $renderOptions = [];

        $widget = array_shift($widgets);
        $widgetData = $widget->widgetData;

        $columns = explode(',', $widgetData['info']['columns']);

        $dbh = ConnectionManager::get('default');
        $sth = $dbh->execute($widgetData['info']['query']);
        $resultSet = $sth->fetchAll('assoc');

        if (!empty($resultSet)) {
            foreach ($resultSet as $k => $row) {
                $renderRow = [];
                foreach ($row as $column => $value) {
                    if (in_array($column, $columns)) {
                        $renderRow[$column] = $value;
                    }
                }
                array_push($renderData, $renderRow);
            }
        }

        //setting the data for the graph rendering
        $this->chartData = $this->getChartData($renderData, [
            'data' => $widget->widgetData,
            'renderAs' => $widget->widgetData['info']['renderAs']
        ]);

        $this->set('containerPrefix', self::GRAPH_PREFIX);
        $this->set('chartData', $this->chartData);
        $this->set('widget', $widget);
        $this->set('widgetData', $widget->widgetData);

        $this->set('renderData', $renderData);
        $this->set('renderOptions', $renderOptions);
        $this->set('rootView', $options['rootView']);
    }


    /**
     * displaySavedSearch method for widgets with type 'saved_search'
     * @param array $widgets with all widgets
     * @param array $options with mixed options
     * @return void
     */
    public function displaySavedSearch(array $widgets, array $options = [])
    {
        $widget = array_shift($widgets);

        $renderData = [];
        $renderOptions = [];

        //actual ORM\Entity of savedSearch
        $widgetData = $widget->widgetData;

        $savedSearchTable = TableRegistry::get('Search.SavedSearches');

        switch ($widgetData->type) {
            case $savedSearchTable->getCriteriaType():
                $search = $savedSearchTable->search(
                    $widgetData->model,
                    $options['user'],
                    json_decode($widgetData->content, true),
                    true
                );
                $widgetData->entities = $search['entities'];
                break;
            case $savedSearchTable->getResultType():
                $widgetData->entities = json_decode($widgetData->content, true);
                break;
        }

        $widgetData->entities['display_columns'] = array_diff(
            $widgetData->entities['display_columns'],
            $savedSearchTable->getSkippedDisplayFields()
        );

        $this->set('widget', $widget);
        $this->set('widgetData', $widget->widgetData);
        $this->set('renderData', $widgetData);
        $this->set('renderOptions', $renderOptions);
        $this->set('rootView', $options['rootView']);
    }


    /**
     * In this case we have same displayMethod for all
     * the widgets, when dealing with Drag/Drop jQuery plugin
     *
     * @param array $widget data
     * @return void
     */
    public function displayDroppableBlock(array $widget)
    {
        $this->set('widget', $widget);
    }


    /**
     * getChartData method
     *
     * Assembles all required data for the graph to be drawn.
     *
     * @param array $renderData containing winget information
     * @param array $chartOptions containing rendering and graph data
     *
     * @return array $chartData
     */
    protected function getChartData($renderData, $chartOptions = [])
    {
        $chartData = [
            'chart' => $chartOptions['renderAs'],
            'options' => [
                'element' => self::GRAPH_PREFIX . $chartOptions['data']['slug'],
                'resize' => true,
            ],
        ];

        if ($chartOptions['renderAs'] == 'donutChart') {
            $data = [];

            if (isset($chartOptions['data']['info']['label']) && isset($chartOptions['data']['info']['value'])) {
                foreach ($renderData as $item) {
                    $data[] = [
                        'label' => $item[$chartOptions['data']['info']['label']],
                        'value' => $item[$chartOptions['data']['info']['value']],
                    ];
                }
            }

            $options = [
                'data' => $data,
            ];

            $chartData['options'] = array_merge($chartData['options'], $options);
        } else {
            $labels = [];
            $columns = explode(',', $chartOptions['data']['info']['columns']);

            foreach ($columns as $column) {
                array_push($labels, Inflector::humanize($column));
            }

            $options = [
                'data' => $renderData,
                'barColors' => ['#00a65a', '#f56954'],
                'labels' => $labels,
                'xkey' => explode(',', $chartOptions['data']['info']['x_axis']),
                'ykeys' => explode(',', $chartOptions['data']['info']['y_axis'])
            ];
            $chartData['options'] = array_merge($chartData['options'], $options);
        }

        return $chartData;
    }
}
