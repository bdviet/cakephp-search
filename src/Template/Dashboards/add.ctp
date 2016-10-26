<?= $this->Html->css('Search.dashboard', ['block' => 'cssBottom']) ?>
<?= $this->Html->script('Search.dashboard', ['block' => 'scriptBottom']) ?>
<div class="row">
    <div class="col-xs-12">
        <?= $this->Form->create($dashboard, ['id' => 'dashboardForm']) ?>
        <fieldset>
            <legend><?= __('Add Dashboard') ?></legend>
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
            <?php foreach (array_keys($dashboardLayout) as $col) : ?>
                <div class="col-xs-6">
                    <p class="h3 text-center"><?= $columns[$col] ?></p>
                    <ul class="savetrue droppable-area" data-column=<?= $col ?>>
                    <?php if (!empty($dashboardSavedSearches[$col])) : ?>
                    <?php foreach ($dashboardSavedSearches[$col] as $rows) : ?>
                        <?php foreach ($rows as $row) : ?>
                         <li class="droppable" data-id="<?= $row->id ?>">
                            <div class="header">
                            <?php
                                list($plugin, $model) = pluginSplit($row->model);
                                echo $model;
                            ?>
                            </div>
                            <div class="content"><?= $row->name ?></div>
                        </li>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                    <?php endif ?>
                    </ul>
                </div>
            <?php endforeach; ?>
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
        <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary']) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
