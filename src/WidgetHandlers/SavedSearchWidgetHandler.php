<?php
namespace Search\WidgetHandlers;

use Cake\ORM\TableRegistry;
use Search\WidgetHandlers\BaseWidgetHandler;

class SavedSearchWidgetHandler extends BaseWidgetHandler
{
    protected $_type = 'saved_search';

    protected $_entity = null;

    protected $_tableName = 'Search.SavedSearches';

    protected $_data = [];

    protected $_tableInstance = null;

    protected $_dataOptions = [];

    /**
     * construct method
     * @param array $options containing widget entity.
     * @return void.
     */
    public function __construct($options = [])
    {
        if (!empty($options['entity'])) {
            $this->_entity = $options['entity'];
        }
        $this->_tableInstance = TableRegistry::get($this->_tableName);
    }

    /**
     * @return array $_dataOptions containing JS/CSS libs.
     */
    public function getDataOptions()
    {
        return $this->_dataOptions;
    }

    /**
     * @return string $type of the widget.
     */
    public function getSavedSearchType()
    {
        return $this->getData()->type;
    }

    /**
     * @return array $_data of the widget.
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * getResults method
     * @param array $options containing entity and view params.
     * @return array $results from $this->_data.
     */
    public function getResults(array $options = [])
    {
        $results = [];

        try {
            $query = $this->_tableInstance->findById($this->_entity->widget_id);
            $resultSet = $query->first();
            if (!empty($resultSet)) {
                $results = $resultSet;
            }
        } catch (\Exception $e) {
            debug($e->getMessage());
        }

        if (empty($this->_entity)) {
            return $results;
        }

        switch ($results->type) {
            case $this->_tableInstance->getCriteriaType():
                $search = $this->_tableInstance->search(
                    $results->model,
                    $options['user'],
                    json_decode($results->content, true),
                    true
                );
                $results->entities = $search['entities'];
                break;
            case $this->_tableInstance->getResultType():
                $results->entities = json_decode($results->content, true);
                break;
        }
        $results->entities['display_columns'] = array_diff(
            $results->entities['display_columns'],
            $this->_tableInstance->getSkippedDisplayFields()
        );

        $this->_data = $results;

        return $results;
    }
}
