<?php
use Cake\Utility\Inflector;
?>
<li class="droppable col-xs-2" data-id="<?= $widget['data']['id'] ?>" data-type="<?= $widget['type'] ?>">
    <div class="header text-center">
    <?php
        list($plugin, $model) = pluginSplit($widget['data']['model']);
        echo $model;
    ?>
    </div>
    <div class="content text-center">
        <h4><?= Inflector::humanize($widget['type']) ?></h4>
        <?= $widget['data']['name'] ?>
    </div>
</li>
