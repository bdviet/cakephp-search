<?php
namespace Search\Controller;

use Cake\View\Cell;
use Cake\Utility\Inflector;

abstract class AbstractWidget extends Cell {

    /** @var Cake\Model\Entity of the widget record */
    public $widgetObject;

    /** @var Array $widgetData containing all required info for widget */
    public $widgetData;

    public $widgetDisplayMethod;

    /** @var Cake\Event\EventManager deriving from the controller */
    public $em; //eventManager()

    /**
    * Passing default constructor parameters to Cell constructor
    * in case we'll need them later.
    */
    public function __construct($req, $res, $em, $cellOptions = []) {
        parent::__construct($req, $res, $em, $cellOptions);

        $this->em = $em;
    }

    /**
    * passing WidgetObject from the Controller
    * and storing it in the AbstractWidget
    * @param Cake\Model\Entity $w
    * @return Cake\Model\Entity $this->widgetObject
    */
    public function setWidget($w) {
        $this->widgetObject = $w;
    }


    /**
    * setWidgetDisplayMethod
    * Following Cake naming conventions we setup
    * Cell view diplay method that will be used for the
    * rendering.
    * @return String $this->widgetDisplayMethod
    */
    public function setWidgetDisplayMethod($options =[])
    {
        $displayAs = $this->widgetObject['widget_type'];

        //used for 'droppable_block';
        if( !empty($options['displayAs']) ) {
            $displayAs = $options['displayAs'];
        }

        $this->widgetDisplayMethod = sprintf("display%s", Inflector::classify($displayAs));
    }


    abstract protected function prepareWidget();
}
