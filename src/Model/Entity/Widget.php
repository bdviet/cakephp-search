<?php
namespace Search\Model\Entity;

use Cake\ORM\Entity;

/**
 * Widget Entity
 *
 * @property string $id
 * @property string $dashboard_id
 * @property string $widget_id
 * @property string $widget_type
 * @property string $widget_options
 * @property int $column
 * @property int $row
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property \Cake\I18n\Time $trashed
 *
 * @property \Search\Model\Entity\Dashboard $dashboard
 * @property \Search\Model\Entity\Widget[] $widgets
 */
class Widget extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
