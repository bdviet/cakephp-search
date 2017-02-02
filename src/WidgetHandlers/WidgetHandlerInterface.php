<?php
namespace Search\WidgetHandlers;

/**
 * WidgetHandlerInterface
 *
 * Contracts global WidgetHandlers.
 */
interface WidgetHandlerInterface
{
    /**
     * getResults method
     * Prepares internal _data property of the
     * widgetHandlers for being used for tables/graphs
     * rendering.
     *
     * @param array $options passed into the WidgetHandler.
     * @return mixed $result with $_data param.
     */
    public function getResults(array $options = []);
}
