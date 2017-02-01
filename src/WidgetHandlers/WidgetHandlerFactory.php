<?php
namespace Search\WidgetHandlers;

use Cake\Utility\Inflector;

class WidgetHandlerFactory
{
    const WIDGET_INTERFACE = 'WidgetInterface';
    const WIDGET_VIEW_BLOCK = 'WidgetViewBlock';

    public static $suffix = 'WidgetHandler';

    public static function create($type, array $options = [])
    {
        $handlerName = Inflector::camelize($type);

        $className = __NAMESPACE__ . '\\' . $handlerName . self::$suffix;
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
