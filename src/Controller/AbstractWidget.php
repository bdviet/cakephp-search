<?php
namespace Search\Controller;

use Cake\Utility\Inflector;
use Cake\View\Cell;

abstract class AbstractWidget extends Cell
{

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
     * @param Cake\Request $req from controller
     * @param Cake\Response $res from controller
     * @param Cake\EventManager $em from controller
     * @param array $cellOptions for extras
     */
    public function __construct($req, $res, $em, $cellOptions = [])
    {
        parent::__construct($req, $res, $em, $cellOptions);

        $this->em = $em;
    }

    /**
     * passing WidgetObject from the Controller
     * and storing it in the AbstractWidget
     * @param Cake\Model\Entity $w widget
     * @return void
     */
    public function setWidget($w)
    {
        $this->widgetObject = $w;
    }


    /**
     * setWidgetDisplayMethod
     * Following Cake naming conventions we setup
     * Cell view diplay method that will be used for the
     * rendering.
     * @param array $options Options
     * @return void
     */
    public function setWidgetDisplayMethod($options = [])
    {
        $displayAs = $this->widgetObject['widget_type'];

        //used for 'droppable_block';
        if (!empty($options['displayAs'])) {
            $displayAs = $options['displayAs'];
        }

        $this->widgetDisplayMethod = sprintf("display%s", Inflector::classify($displayAs));
    }

    /**
     * Each child class should implement prepareWidget method
     *
     * @return void
     */
    abstract protected function prepareWidget();
}
