<?php
//humanizing Column Heads
use Cake\Utility\Inflector;
//getting column heads
$listingFields = explode(',',$widgetData['info']['columns']);
?>
<div class='dashboard-widget-display_report'>
    <div class="col-md-6">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><strong> <?= $widgetData['info']['name'] ?></strong></h3>
                    </div> <!-- panel-heading -->

                    <div class="panel-body">
                        <?php if($widgetData['info']['renderAs'] == 'table') : ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-datatable">
                                <thead>
                                    <tr>
                                    <?php foreach($listingFields as $field) : ?>
                                        <th><?= Inflector::humanize($field) ?></th>
                                    <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php //pr($renderData); ?>
                                    <?php foreach($renderData as $k => $row): ?>
                                    <tr>
                                        <?php foreach($row as $field): ?>
                                            <td><?= $field ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div> <!-- table-responsive -->
                        <?php endif; ?>
                    </div> <!-- panel-body -->

                </div> <!-- panel-default -->

            </div> <!-- col-xs-12 -->
        </div> <!-- row -->
    </div> <!-- col-md-6 -->
</div> <!-- dashboard-widget-display_report -->
