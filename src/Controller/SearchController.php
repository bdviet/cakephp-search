<?php
namespace Search\Controller;

use Cake\Network\Exception\BadRequestException;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Search\Controller\AppController;

class SearchController extends AppController
{
    /**
     * Advanced search action
     *
     * @param string $model model name
     * @return void
     */
    public function advanced($model = null)
    {
        $this->_searchAction($model, true);
    }

    /**
     * Basic search action
     *
     * @param string $model model name
     * @return void
     */
    public function basic($model = null)
    {
        $this->_searchAction($model);
    }

    /**
     * Search action
     *
     * @param  string $model model name
     * @param  bool   $advanced advanced search flag
     * @return void
     */
    protected function _searchAction($model, $advanced = false)
    {
        if (is_null($model)) {
            throw new BadRequestException();
        }
        $model = Inflector::pluralize(Inflector::classify($model));

        if ($this->request->is('post')) {
            $where = $this->Searchable->prepareWhereStatement($this->request->data, $model, $advanced);
            $table = TableRegistry::get($model);
            $query = $table->find('all')->where($where);
            $this->set('entities', $this->paginate($query));
            $this->set('fields', $this->Searchable->getListingFields($model));
        }

        $searchFields = [];
        if ($this->Searchable->isSearchable($model)) {
            $searchFields = $this->Searchable->getSearchableFields($model);
            $searchFields = $this->Searchable->getSearchableFieldProperties($model, $searchFields);
        }

        $searchOperators = [];
        if (!empty($searchFields)) {
            $searchOperators = $this->Searchable->getFieldTypeOperators();
        }

        $this->set(compact('searchFields', 'searchOperators'));
    }
}
