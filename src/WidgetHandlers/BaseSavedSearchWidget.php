<?php
namespace Search\WidgetHandlers;

use Search\WidgetHandlers\BaseWidget;

class BaseSavedSearchWidget extends BaseWidget
{
    protected $_type = 'saved_search';

    public function getSavedSearchType()
    {
        return $this->getData()->type;
    }

    public function getData()
    {
        return $this->_data;
    }

    public function getResults(array $options = [])
    {
    }

    /** @TODO: check if we need it really?! */
    public function getDataOptions(array $options = [])
    {
        return;
    }
}
