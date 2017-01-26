<?php
//humanizing Column Heads
use Cake\Utility\Inflector;
use Search\Helper\ChartHelper;

//getting column heads
$listingFields = explode(',', $widgetData['info']['columns']);
?>
<div class='dashboard-widget-display_report'>
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title"><?= $widgetData['info']['name'] ?></h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="box-body">
            <?php if ($widgetData['info']['renderAs'] == 'barChart') : ?>
                <div id="<?= $containerPrefix . $widgetData['slug'];?>"></div>
            <?php endif; ?>
        </div> <!-- panel-body -->
    </div> <!-- panel-default -->
</div> <!-- dashboard-widget-display_report -->
