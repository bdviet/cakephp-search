<?php
namespace Search\Widgets;

use Cake\Utility\Inflector;

class WidgetFactory
{
    const WIDGET_SUFFIX = 'Widget';
    const WIDGET_INTERFACE = 'WidgetInterface';

    /**
     * create method
     *
     * Factory method to initialize widget handler instance
     * base on the widget type field.
     *
     * @param string $type containing the widget handler type.
     * @param array $options containing entity and view data.
     * @return mixed $className of the widgetHandler.
     */
    public static function create($type, array $options = [])
    {
        $handlerName = Inflector::camelize($type);

        $className = __NAMESPACE__ . '\\' . $handlerName . self::WIDGET_SUFFIX;
        $interface = __NAMESPACE__ . '\\' . self::WIDGET_INTERFACE;

        try {
            if (class_exists($className) && in_array($interface, class_implements($className))) {
                return new $className($options);
            }
        } catch (\Exception $e) {
            debug($e->getMessage());
        }
    }
}
