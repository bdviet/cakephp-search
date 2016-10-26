<?= $this->Html->css('Search.dashboard', ['block' => 'cssBottom']) ?>
<?= $this->Html->script('Search.dashboard', ['block' => 'scriptBottom']) ?>

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
            <?= $this->cell('Search.Dashboard::savedSearches', [$dashboard]); ?>
        </fieldset>
        <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary']) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
