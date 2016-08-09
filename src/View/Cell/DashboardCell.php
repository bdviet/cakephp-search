<?php
namespace Search\View\Cell;

use Cake\Core\Configure;
use Cake\View\Cell;
use Search\Model\Entity\Dashboard;

class DashboardCell extends Cell
{
    /**
     * Cell for dashboard viewing.
     *
     * @param  array  $savedSearches dashboard's saved searches
     * @return void
     */
    public function display(array $savedSearches)
    {
        $gridRows = 0;
        $gridColumns = count(Configure::read('Search.dashboard.columns'));
        $result = [];
        foreach ($savedSearches as $savedSearch) {
            if ($savedSearch['row'] + 1 > $gridRows) {
                $gridRows = $savedSearch['row'] + 1;
            }
            $result[$savedSearch['column']][$savedSearch['row']] = $savedSearch;
            ksort($result[$savedSearch['column']]);
        }
        ksort($result);

        $this->set('savedSearches', $result);
        $this->set('gridRows', $gridRows);
        $this->set('gridColumns', $gridColumns);
    }

    /**
     * Cell for dashboard saved searches.
     *
     * @param  Search\Model\Entity\Dashboard $dashboard Dashboard Entity
     * @return void
     */
    public function savedSearches(Dashboard $dashboard)
    {
        $this->loadModel('Search.Dashboards');

        /*
        get all saved searches
         */
        $allSavedSearches = $this->Dashboards->SavedSearches->find('all')
            ->where(['SavedSearches.name IS NOT' => null])
            ->order(['SavedSearches.model', 'SavedSearches.name']);
        $allSavedSearches = $this->Dashboards->SavedSearches
            ->find()
            ->select()
            ->where(['SavedSearches.name IS NOT' => null])
            ->hydrate(false)
            ->indexBy('id')
            ->toArray();

        /*
        get dashboard columns
         */
        $columns = Configure::read('Search.dashboard.columns');

        /*
        get dashboard layout
         */
        $dashboardLayout = $this->_getDashboardLayout($columns);

        $dashboardSavedSearches = [];
        /*
        get dashboard saved searches and detach them from the saved searches array
         */
        if (!empty($dashboard->saved_searches)) {
            $allSavedSearches = $this->_filterOutDashboardSavedSearches($allSavedSearches, $dashboard->saved_searches);
            $dashboardSavedSearches = $this->_sortDashboardSavedSearches($dashboardLayout, $dashboard);
        }

        $this->set([
            'columns' => $columns,
            'dashboardLayout' => $dashboardLayout,
            'allSavedSearches' => $allSavedSearches,
            'dashboardSavedSearches' => $dashboardSavedSearches
        ]);
    }

    /**
     * Filters out dashboard's saved searches.
     *
     * @param  array $allSavedSearches       all saved searches
     * @param  array $dashboardSavedSearches dashboard's saved searches
     * @return array                         filtered saved searches
     */
    protected function _filterOutDashboardSavedSearches(array $allSavedSearches, array $dashboardSavedSearches)
    {
        foreach ($dashboardSavedSearches as $search) {
            unset($allSavedSearches[$search->id]);
        }

        return $allSavedSearches;
    }

    /**
     * Generate's dashboard layout based on pre-defined dashboard columns.
     *
     * @param array $columns Columns
     * @return array
     */
    protected function _getDashboardLayout($columns)
    {
        $result = [];

        $count = 0;
        $totalColumns = count($columns);
        while ($count < $totalColumns) {
            $result[$count] = [];
            $count++;
        }

        return $result;
    }

    /**
     * Sorts dashboard's saved searches to its layout.
     *
     * @param  array $dashboardLayout dashboard layout
     * @param  Search\Model\Entity\Dashboard $dashboard Dashboard Entity
     * @return array
     */
    protected function _sortDashboardSavedSearches(array $dashboardLayout, Dashboard $dashboard)
    {
        foreach ($dashboard->saved_searches as $search) {
            $dashboardLayout[$search->_joinData->column][$search->_joinData->row][] = $search;
            ksort($dashboardLayout[$search->_joinData->column]);
        }
        ksort($dashboardLayout);

        return $dashboardLayout;
    }
}
