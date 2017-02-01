<?php
namespace Search\WidgetHandlers;

interface WidgetInterface
{
    public function getResults(array $options = []);
}
