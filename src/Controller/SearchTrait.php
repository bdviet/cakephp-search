<?php
namespace Search\Controller;

use Cake\Network\Exception\BadRequestException;
use Cake\ORM\TableRegistry;

trait SearchTrait
{
    /**
     * Table name for Saved Searches model.
     *
     * @var string
     */
    protected $_tableSearch = 'Search.SavedSearches';

    /**
     * Element to be used as Search template.
     *
     * @var string
     */
    protected $_elementSearch = 'Search.Search/search';

    /**
     * Element to be used as Saved Result template.
     *
     * @var string
     */
    protected $_elementSavedResult = 'Search.Search/search_saved_result';

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

        return $this->redirect(['action' => 'search']);
    }

    /**
     * Saved result action
     *
     * @param  string $model model name
     * @param  string $id    record id
     * @return void
     */
    public function search_saved_result($id)
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
     * @return void
     */
    public function search()
    {
        $model = $this->modelClass;
        if (!$this->_isSearchable($model)) {
            throw new BadRequestException('You cannot search in ' . implode(' ', pluginSplit($model)) . '.');
        }

        $table = TableRegistry::get($this->_tableSearch);

        if ($this->request->is('post')) {
            $search = $table->search($model, $this->Auth->user(), $this->request->data);

            if (isset($search['saveSearchCriteriaId'])) {
                $this->set('saveSearchCriteriaId', $search['saveSearchCriteriaId']);
            }

            if (isset($search['saveSearchResultsId'])) {
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

        $this->render($this->_elementSearch);
    }

    /**
     * Returns true if table is searchable, false otherwise.
     *
     * @param  \Cake\ORM\Table|string $table Table object or name.
     * @return bool
     */
    protected function _isSearchable($table)
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

    /**
     * Delete method
     *
     * @param string|null $id Saved search id.
     * @return \Cake\Network\Response|null Redirects to referer.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function search_delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $table = TableRegistry::get($this->_tableSearch);
        $savedSearch = $table->get($id);
        if ($table->delete($savedSearch)) {
            $this->Flash->success(__('The saved search has been deleted.'));
        } else {
            $this->Flash->error(__('The saved search could not be deleted. Please, try again.'));
        }

        return $this->redirect($this->referer());
    }
}