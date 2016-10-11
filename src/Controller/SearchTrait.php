<?php
namespace Search\Controller;

use Cake\Network\Exception\BadRequestException;
use Cake\ORM\TableRegistry;

trait SearchTrait
{
    protected $_tableSearch = 'Search.SavedSearches';

    protected $_elementBasic = 'Search.Search/basic';

    protected $_elementAdvanced = 'Search.Search/advanced';

    protected $_elementSavedResult = 'Search.Search/saved_result';

    /**
     * Save action
     *
     * @param string|null $id Search id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function search_save($id = null)
    {
        $this->request->allowMethod(['patch', 'post', 'put']);

        $table = TableRegistry::get($this->_tableSearch);

        $search = $table->get($id);
        $search = $table->patchEntity($search, $this->request->data);
        if ($table->save($search)) {
            $this->Flash->success(__('The search has been saved.'));
        } else {
            $this->Flash->error(__('The search could not be saved. Please, try again.'));
        }

        return $this->redirect(['action' => 'advanced']);
    }

    /**
     * Advanced search action
     *
     * @param string $model model name
     * @return void
     */
    public function advanced()
    {
        $this->_searchAction(true, true);
    }

    /**
     * Basic search action
     *
     * @return void
     */
    public function basic()
    {
        $this->_searchAction();
    }

    /**
     * Saved result action
     *
     * @param  string $model model name
     * @param  string $id    record id
     * @return void
     */
    public function saved_result($id)
    {
        $model = $this->modelClass;
        $table = TableRegistry::get($this->_tableSearch);

        $search = $table->get($id);

        $this->set('searchName', $search->name);

        $content = json_decode($search->content, true);
        $this->set('entities', $content['result']);

        // get listing fields
        if (isset($content['display_columns'])) {
            $listingFields = $content['display_columns'];
        } else {
            $listingFields = $this->SavedSearches->getListingFields($model);
        }

        $this->set('listingFields', $listingFields);

        $this->render($this->_elementSavedResult);
    }

    /**
     * Search action
     *
     * @param  bool   $advanced advanced search flag
     * @param  bool   $preSave pre-save
     * @return void
     */
    protected function _searchAction($advanced = false, $preSave = false)
    {
        $model = $this->modelClass;
        if (!$this->isSearchable($model)) {
            throw new BadRequestException('You cannot search in ' . implode(' ', pluginSplit($model)) . '.');
        }

        $table = TableRegistry::get($this->_tableSearch);

        if ($this->request->is('post')) {
            $search = $table->search($model, $this->Auth->user(), $this->request->data, $advanced, $preSave);

            // if in advanced mode, pre-save search criteria and results
            if ($advanced && !empty($this->request->data['criteria'])) {
                $this->set('saveSearchCriteriaId', $search['saveSearchCriteriaId']);
                $this->set('saveSearchResultsId', $search['saveSearchResultsId']);
            }

            // if (isset($this->request->data['criteria']['query'])) {
            //     $this->request->data['criteria'] = $table->getSearchCriteria($this->request->data['criteria'], $model);
            // }

            // @todo find out how to do pagination without affecting limit
            $entities = $search['entities']['result']->all();
            $this->set('entities', $entities);

            // set listing fields
            if (isset($this->request->data['display_columns'])) {
                $listingFields = $this->request->data['display_columns'];
            } else {
                $listingFields = $table->getListingFields($model);
            }
            $this->set('listingFields', $listingFields);
        }

        $searchFields = [];
        // get searchable fields
        $searchFields = $table->getSearchableFields($model);
        $searchFields = $table->getSearchableFieldProperties($model, $searchFields);
        $searchFields = $table->getSearchableFieldLabels($searchFields);

        $searchOperators = [];
        // get search operators based on searchable fields
        if (!empty($searchFields)) {
            $searchOperators = $this->Searchable->getFieldTypeOperators();
        }

        $savedSearches = $this->Searchable->getSavedSearches([$this->Auth->user('id')], [$model]);

        $this->set(compact('searchFields', 'searchOperators', 'savedSearches'));

        $this->render($advanced ? $this->_elementAdvanced : $this->_elementBasic);
    }

    /**
     * Returns true if table is searchable, false otherwise.
     *
     * @param  \Cake\ORM\Table|string $table Table object or name.
     * @return bool
     */
    public function isSearchable($table)
    {
        $result = false;
        // get Table instance
        if (is_string($table)) {
            $table = TableRegistry::get($table);
        }

        // check if is searchable
        if (method_exists($table, 'isSearchable') && is_callable([$table, 'isSearchable'])) {
            $result = $table->isSearchable();
        }

        return $result;
    }
}