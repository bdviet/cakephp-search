<?php
namespace Search\WidgetHandlers;

use Search\WidgetHandlers\WidgetHandlerInterface;

abstract class BaseWidgetHandler implements WidgetHandlerInterface
{
    const WIDGET_INTERFACE = 'WidgetHandlerInterface';

    const WIDGET_SUFFIX = 'WidgetHandler';

    /** @const WIDGET_REPORT_SUFFIX file naming suffix of widget files */
    const WIDGET_REPORT_SUFFIX = 'ReportWidgetHandler';

    /** @const GRAPH_PREFIX for prefixing div containers of graphs */
    const GRAPH_PREFIX = 'graph_';

    /**
     * getType method
     *
     * Widget $type specifies the type of handler
     * we're dealing with - whether it's a barChart/SavedSearch.
     *
     * @return string $type of the WidgetHandler.
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * getRenderElement
     *
     * renderElement variable is used to specify CakePHP
     * element that should be used for rendering the widget.
     *
     * @return string $renderElement name.
     */
    public function getRenderElement()
    {
        return $this->renderElement;
    }
}
