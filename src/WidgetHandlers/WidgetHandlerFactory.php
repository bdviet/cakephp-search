<?php
namespace Search\WidgetHandlers;

use Cake\Utility\Inflector;

class WidgetHandlerFactory
{
    const WIDGET_SUFFIX = 'WidgetHandler';
    const WIDGET_INTERFACE = 'WidgetHandlerInterface';

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
