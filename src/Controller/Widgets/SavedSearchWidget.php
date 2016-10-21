<?php
namespace Search\Controller\Widgets;

use Search\Controller\AbstractWidget;
use Cake\ORM\TableRegistry;
class SavedSearchWidget extends AbstractWidget {

	public function __construct($request, $response, $em, $cellOptions = [] )
    {
        parent::__construct($request, $response, $em, $cellOptions);
    }

	/**
	* preparing widgetData for execution by the cells
	* @return Cake\ORM\Entity $this->widgetData
	*/
    public function prepareWidget()
    {
		$result = [];
		$savedSearches = TableRegistry::get('SavedSearches');

		$widget_id = $this->widgetObject['widget_id'];

		$savedSearch = $savedSearches->findById($widget_id)->toArray();

		if( !empty($savedSearch) ) {
			$result = array_shift($savedSearch);
		}

        $this->setWidgetDisplayMethod();
		$this->widgetData = $result;
    }
}
