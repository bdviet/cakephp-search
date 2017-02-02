<?php
namespace Search\WidgetHandlers;

use Search\WidgetHandlers\WidgetHandlerInterface;

abstract class BaseWidgetHandler implements WidgetHandlerInterface
{
    const WIDGET_SUFFIX = 'WidgetHandler';
    const WIDGET_INTERFACE = 'WidgetHandlerInterface';
    const WIDGET_REPORT_SUFFIX = 'ReportWidgetHandler';

    const GRAPH_PREFIX = 'graph_';

    /**
     * @return string $type of the WidgetHandler.
     */
    public function getType()
    {
        return $this->_type;
    }
}
