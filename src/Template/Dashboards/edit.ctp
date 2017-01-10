<?php
echo $this->Html->css('Search.dashboard', ['block' => 'cssBottom']);
echo $this->Html->script('Search.dashboard', ['block' => 'scriptBottom']);
?>
<div class="row">
    <div class="col-xs-12">
        <?= $this->Form->create($dashboard, ['id' => 'dashboardForm']) ?>
        <fieldset>
            <legend><?= __('Edit Dashboard') ?></legend>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">&nbsp;</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <?= $this->Form->input('name'); ?>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <?= $this->Form->input('role_id', ['options' => $roles, 'empty' => true]); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dashboard-saved-searches">
            <div class="row">
            <?php
            $columnsCount = count($columns);
            for ($col = 0; $col < $columnsCount; $col++) :
                ?>
                <div class="col-xs-<?= 12 / $columnsCount ?>">
                <p class="h3 text-center"><?= $columns[$col] ?></p>
                <ul class="savetrue droppable-area" data-column=<?= $col ?>>
                <?php
                foreach ($savedWidgetData as $savedWidget) {
                    if ($savedWidget['data']['column'] !== $col) {
                        continue;
                    }
                    echo $this->cell('Search.Widget::displayDroppableBlock', [$savedWidget]);
                }
                ?>
                </ul>
                </div>
            <?php
            endfor;
            ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <p class="h3 text-center saved-searches-title"><?= __('Widgets') ?></p>
                <ul class="list-inline droppable-area saved-searches-area">
                    <?php foreach ($widgets as $widget) : ?>
                        <?php echo $this->cell('Search.Widget::displayDroppableBlock', [$widget]);?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        </fieldset>
        <?php
        echo $this->Form->button(__('Submit'), ['class' => 'btn btn-primary']);
        echo $this->Form->end();
        ?>
    </div>
</div>
