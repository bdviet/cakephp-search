<?php
namespace Search\Controller;

use Cake\Network\Exception\BadRequestException;
use Cake\ORM\TableRegistry;
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

        return $this->redirect(['action' => 'search']);
    }

    /**
     * Saved result action
     *
     * @param  string $id    record id
     * @return void
     */
    public function saveSearchResult($id)
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
            // basic search query, coverted to search criteria
            if (isset($this->request->data['criteria']['query'])) {
                $this->request->data['criteria'] = $table->getSearchCriteria(
                    $this->request->data['criteria'],
                    $model
                );
            }

            // set display columns before the pre-saving, fixes bug
            // with missing display columns when saving a basic search
            if (!$this->request->data('display_columns')) {
                $this->request->data('display_columns', $table->getListingFields($model));
            }

            $search = $table->search($model, $this->Auth->user(), $this->request->data);

            if (isset($search['saveSearchCriteriaId'])) {
                $this->set('saveSearchCriteriaId', $search['saveSearchCriteriaId']);
            }

            if (isset($search['saveSearchResultsId'])) {
                $this->set('saveSearchResultsId', $search['saveSearchResultsId']);
            }

            if (isset($this->request->data['criteria']['query'])) {
                $this->request->data['criteria'] = $table->getSearchCriteria($this->request->data['criteria'], $model);
            }

            // @todo find out how to do pagination without affecting limit
            $entities = $search['entities']['result']->all();
            $this->set('entities', $entities);

            // set listing fields
            $listingFields = $this->request->data('display_columns');
        }

        $searchFields = [];
        // get searchable fields
        $searchFields = $table->getSearchableFields($model);
        $searchFields = $table->getSearchableFieldProperties($model, $searchFields);
        $searchFields = $table->getSearchableFieldLabels($searchFields);

        $searchOperators = [];
        // get search operators based on searchable fields
        if (!empty($searchFields)) {
            $searchOperators = $table->getFieldTypeOperators();
        }

        $savedSearches = $table->getSavedSearches([$this->Auth->user('id')], [$model]);

        $this->set(compact('searchFields', 'searchOperators', 'savedSearches', 'listingFields'));

        $this->render($this->_elementSearch);
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

        return $this->redirect($this->referer());
    }
}
