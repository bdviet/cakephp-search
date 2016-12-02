<?php
namespace Search\Controller\Widgets;

use Cake\ORM\TableRegistry;
use Search\Controller\AbstractWidget;

class SavedSearchWidget extends AbstractWidget
{
    /**
     * construct method
     * @param Cake\Request $request from controller
     * @param Cake\Response $response from controler
     * @param Cake\EventManager $em from controller
     * @param array $cellOptions for extras
     */
    public function __construct($request, $response, $em, $cellOptions = [])
    {
        parent::__construct($request, $response, $em, $cellOptions);
    }

    /**
     * preparing widgetData for execution by the cells
     * @return Cake\ORM\Entity $this->widgetData
     */
    public function prepareWidget()
    {
        $savedSearches = TableRegistry::get('Search.SavedSearches');

        $widgetId = $this->widgetObject['widget_id'];

        $savedSearch = $savedSearches->findById($widgetId)->first();

        $this->widgetData = $savedSearch;

        $this->setWidgetDisplayMethod();
    }
}
