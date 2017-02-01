<?php
namespace Search\WidgetHandlers;

use Search\WidgetHandlers\WidgetInterface;

abstract class BaseWidget implements WidgetInterface
{
    const WIDGET_INTERFACE = 'WidgetInterface';
    const WIDGET_SUFFIX = 'WidgetHandler';
    const GRAPH_PREFIX = 'graph_';

    public function getType()
    {
        return $this->_type;
    }
}
