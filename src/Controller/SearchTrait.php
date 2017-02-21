<?php
namespace Search\Controller;

use Cake\Network\Exception\BadRequestException;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Search\Controller\Traits\SearchableTrait;

trait SearchTrait
{
    use SearchableTrait;

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
     * Save action
     *
     * @param string|null $id Search id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function saveSearch($id = null)
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

        return $this->redirect(['action' => 'search', $id]);
    }

    /**
     * Search action
     *
     * @param  string $id Saved search id
     * @return void
     */
    public function search($id = null)
    {
        $model = $this->modelClass;
        if (!$this->_isSearchable($model)) {
            throw new BadRequestException('You cannot search in ' . implode(' ', pluginSplit($model)) . '.');
        }

        $table = TableRegistry::get($this->_tableSearch);

        // get searchable fields
        $searchFields = $table->getSearchableFields($model);

        $data = $this->request->data();

        // is editable flag, false by default
        $isEditable = false;

        // saved search instance, null by default
        $savedSearch = null;

        $isBasicSearch = Hash::get($data, 'criteria.query') ? true : false;

        if ($this->request->is(['post', 'get'])) {
            // basic search query, converted to search criteria
            if ($isBasicSearch) {
                $data['criteria'] = $table->getSearchCriteria(Hash::get($data, 'criteria'), $model);
                $data['aggregator'] = 'OR';
            }

            // id of saved search has been provided
            if (!is_null($id)) {
                $savedSearch = $table->get($id);
                // fetch search conditions from saved search if request data are empty
                // INFO: this is valid on initial saved search load
                if (empty($data)) {
                    $data = json_decode($savedSearch->content, true);
                } else { // INFO: this is valid when a saved search was modified and the form was re-submitted
                    $isEditable = true;
                }
            }

            // set display columns before the pre-saving, fixes bug
            // with missing display columns when saving a basic search
            if (!Hash::get($data, 'display_columns')) {
                $data['display_columns'] = $table->getListingFields($model);
            }
            // use first field of display columns as sort by field, if empty
            if (!Hash::get($data, 'sort_by_field')) {
                $data['sort_by_field'] = current($data['display_columns']);
            }
            // set default sort by order, if empty
            if (!Hash::get($data, 'sort_by_order')) {
                $data['sort_by_order'] = $table->getDefaultSortByOrder();
            }
            // set default limit, if empty
            if (is_null(Hash::get($data, 'limit'))) {
                $data['limit'] = $table->getDefaultLimit();
            }

            $data = $table->validateData($model, $data);

            $search = $table->search($model, $this->Auth->user(), $data);

            if (isset($search['saveSearchCriteriaId'])) {
                $this->set('saveSearchCriteriaId', $search['saveSearchCriteriaId']);
            }

            if (isset($search['saveSearchResultsId'])) {
                $this->set('saveSearchResultsId', $search['saveSearchResultsId']);
            }

            // @todo find out how to do pagination without affecting limit
            if ($search['entities']['result'] instanceof Query) {
                // fetched from new search result
                $data['result'] = $search['entities']['result']->all();
            } else {
                // as taken from a saved search result
                $data['result'] = $search['entities']['result'];
            }
        }

        $savedSearches = $table->getSavedSearches([$this->Auth->user('id')], [$model]);

        $this->set(compact('searchFields', 'savedSearches', 'model'));
        $this->set('searchData', $data);
        $this->set('savedSearch', $savedSearch);
        $this->set('isEditable', $isEditable);
        $this->set('limitOptions', $table->getLimitOptions());
        $this->set('sortByOrderOptions', $table->getSortByOrderOptions());
        $this->set('aggregatorOptions', $table->getAggregatorOptions());

        $this->render($this->_elementSearch);
    }

    /**
     * Edit action
     *
     * @param string|null $preId Presaved Search id.
     * @param string|null $id Search id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function editSearch($preId = null, $id = null)
    {
        $this->request->allowMethod(['patch', 'post', 'put']);

        $table = TableRegistry::get($this->_tableSearch);

        // get pre-saved search
        $preSaved = $table->get($preId);
        // merge pre-saved search and request data
        $data = array_merge($preSaved->toArray(), $this->request->data);

        $search = $table->get($id);
        $search = $table->patchEntity($search, $data);
        if ($table->save($search)) {
            $this->Flash->success(__('The search has been edited.'));
        } else {
            $this->Flash->error(__('The search could not be edited. Please, try again.'));
        }

        return $this->redirect(['action' => 'search', $id]);
    }
    /**
     * Copy action
     *
     * @param string|null $id Search id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function copySearch($id = null)
    {
        $this->request->allowMethod(['patch', 'post', 'put']);

        $table = TableRegistry::get($this->_tableSearch);

        // get saved search
        $savedSearch = $table->get($id);

        $search = $table->newEntity();

        // patch new entity with saved search data
        $search = $table->patchEntity($search, $savedSearch->toArray());
        if ($table->save($search)) {
            $this->Flash->success(__('The search has been copied.'));
        } else {
            $this->Flash->error(__('The search could not be copied. Please, try again.'));
        }

        return $this->redirect(['action' => 'search', $search->id]);
    }

    /**
     * Delete method
     *
     * @param string|null $id Saved search id.
     * @return \Cake\Network\Response|null Redirects to referer.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function deleteSearch($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $table = TableRegistry::get($this->_tableSearch);
        $savedSearch = $table->get($id);
        if ($table->delete($savedSearch)) {
            $this->Flash->success(__('The saved search has been deleted.'));
        } else {
            $this->Flash->error(__('The saved search could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'search']);
    }
}
