<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Dashboards'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Roles'), ['controller' => 'Roles', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Role'), ['controller' => 'Roles', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Saved Searches'), ['controller' => 'SavedSearches', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Saved Search'), ['controller' => 'SavedSearches', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="dashboards form large-9 medium-8 columns content">
    <?= $this->Form->create($dashboard) ?>
    <fieldset>
        <legend><?= __('Add Dashboard') ?></legend>
        <?php
            echo $this->Form->input('name');
            echo $this->Form->input('role_id', ['options' => $roles, 'empty' => true]);
            echo $this->Form->input('saved_searches._ids', ['options' => $savedSearches]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
