<?php
namespace Search\WidgetHandlers;

interface WidgetHandlerInterface
{
    public function getResults(array $options = []);
}
