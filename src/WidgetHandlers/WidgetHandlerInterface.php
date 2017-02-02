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

    /**
     * getType method
     * Each widget has its unique type that is assigned
     * to each instance.
     * @return string $type of the current widgetHandler.
     */
    public function getType();

    /**
     * getRenderElement
     * Returns the name of the render element
     * @return string $renderElement
     */
    public function getRenderElement();

    /**
     * getScripts method.
     *
     * Retrieves the list of JS/CSS scripts that might
     * be required for the widget rendering.
     * Examples: dataTables widgets (aka save_search), and
     * report widgets with graph libs.
     *
     * @param array $options for extra settings
     * @return array $content of all widget scripts listed with scriptBlocks.
     */
    public function getScripts(array $options = []);
}
