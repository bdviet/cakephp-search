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
            <div id="<?= $containerPrefix . $widgetData['slug'];?>">
                <div class="row">
                <?php if ($widgetData['info']['renderAs'] == 'knobChart') : ?>
                    <?php foreach ($chartData['options']['data'] as $k => $item) : ?>
                        <div class="col-xs-6 col-md-3 text-center">
                            <input type="text" class="knob-graph knob-<?=$k?>" data-skin="tron" value="<?=$item['value']?>" data-max="<?=$item['max']?>">
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                </div>
            </div>
        </div> <!-- panel-body -->
    </div> <!-- panel-default -->
</div> <!-- dashboard-widget-display_report -->
