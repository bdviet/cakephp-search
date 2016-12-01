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

        if ($this->request->is(['post', 'get'])) {
            // basic search query, converted to search criteria
            if (Hash::get($data, 'criteria.query')) {
                $data['criteria'] = $table->getSearchCriteria(Hash::get($data, 'criteria'), $model);
            }

            // if id of saved search is provided, fetch search conditions from there
            if (!is_null($id)) {
                $search = $table->get($id);
                $this->set('savedSearch', $search);
                $data = json_decode($search->content, true);
            }

            // set display columns before the pre-saving, fixes bug
            // with missing display columns when saving a basic search
            if (!Hash::get($data, 'display_columns')) {
                $data['display_columns'] = $table->getListingFields($model);
            }

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
                $entities = $search['entities']['result']->all();
            } else {
                // as taken from a saved search result
                $entities = $search['entities']['result'];
            }
            $this->set('entities', $entities);
        }

        $savedSearches = $table->getSavedSearches([$this->Auth->user('id')], [$model]);

        $this->set(compact('searchFields', 'savedSearches', 'model'));
        $this->set('searchData', $data);

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

        return $this->redirect(['action' => 'search']);
    }
}
