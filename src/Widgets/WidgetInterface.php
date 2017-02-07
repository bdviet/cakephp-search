<?php
namespace Search\Widgets;

/**
 * WidgetHandlerInterface
 *
 * Contracts global WidgetHandlers.
 */
interface WidgetInterface
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
     * getContainerId
     * Each widget contains unique identifier
     * for the DOM object, by which JS/CSS styling
     * is applied.
     * @return string $containerId of the widget.
     */
    public function getContainerId();

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
     * getOptions method.
     *
     * @return array $content of all widget scripts listed with scriptBlocks.
     */
    public function getOptions();
}
