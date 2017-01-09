<?php
use Cake\Utility\Inflector;

list($plugin, $model) = pluginSplit($widget['data']['model']);
?>
<li class="droppable col-xs-6 col-sm-4 col-md-3 col-lg-2" data-id="<?= $widget['data']['id'] ?>" data-type="<?= $widget['type'] ?>">
    <div class="header text-center">
        <strong><?= $model ?></strong>
    </div>
    <div class="body text-center">
        <h4><?= Inflector::humanize($widget['type']) ?></h4>
        <?= $widget['data']['name'] ?>
    </div>
</li>
