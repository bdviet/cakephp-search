<?php
    $config = $widget->getConfig();
    $data = $widget->getData();
    $type = $widget->getType();
?>
<div class='dashboard-widget-display_config'>
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title"><?= $config['info']['name'] ?></h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="box-body">
            <div id="<?= $widget->getContainerId()?>">
                <?php if ($type == 'knobChart') : ?>
                    <div class="row">
                    <?php foreach ($data['options']['data'] as $k => $item) : ?>
                        <div class="col-xs-6 col-md-3 text-center">
                            <input type="text" class="knob-graph knob-<?=$k?>" data-skin="tron" value="<?=$item['value']?>" data-max="<?=$item['max']?>">
                            <div class="knob-label"><?= $item['label']?></div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div> <!-- .graph_ container -->
        </div>
    </div>
</div>
