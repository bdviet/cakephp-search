<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Dashboard'), ['action' => 'edit', $dashboard->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Dashboard'), ['action' => 'delete', $dashboard->id], ['confirm' => __('Are you sure you want to delete # {0}?', $dashboard->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Dashboards'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Dashboard'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Roles'), ['controller' => 'Roles', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Role'), ['controller' => 'Roles', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Saved Searches'), ['controller' => 'SavedSearches', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Saved Search'), ['controller' => 'SavedSearches', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="dashboards view large-9 medium-8 columns content">
    <h3><?= h($dashboard->name) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= h($dashboard->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Name') ?></th>
            <td><?= h($dashboard->name) ?></td>
        </tr>
        <tr>
            <th><?= __('Role') ?></th>
            <td><?= $dashboard->has('role') ? $this->Html->link($dashboard->role->name, ['controller' => 'Roles', 'action' => 'view', $dashboard->role->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Created') ?></th>
            <td><?= h($dashboard->created) ?></td>
        </tr>
        <tr>
            <th><?= __('Modified') ?></th>
            <td><?= h($dashboard->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Saved Searches') ?></h4>
        <?php if (!empty($dashboard->saved_searches)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Name') ?></th>
                <th><?= __('Type') ?></th>
                <th><?= __('User Id') ?></th>
                <th><?= __('Model') ?></th>
                <th><?= __('Shared') ?></th>
                <th><?= __('Content') ?></th>
                <th><?= __('Created') ?></th>
                <th><?= __('Modified') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($dashboard->saved_searches as $savedSearches): ?>
            <tr>
                <td><?= h($savedSearches->id) ?></td>
                <td><?= h($savedSearches->name) ?></td>
                <td><?= h($savedSearches->type) ?></td>
                <td><?= h($savedSearches->user_id) ?></td>
                <td><?= h($savedSearches->model) ?></td>
                <td><?= h($savedSearches->shared) ?></td>
                <td><?= h($savedSearches->content) ?></td>
                <td><?= h($savedSearches->created) ?></td>
                <td><?= h($savedSearches->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'SavedSearches', 'action' => 'view', $savedSearches->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'SavedSearches', 'action' => 'edit', $savedSearches->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'SavedSearches', 'action' => 'delete', $savedSearches->id], ['confirm' => __('Are you sure you want to delete # {0}?', $savedSearches->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
