<?php if (!empty($searchFields) && !empty($listingFields)) : ?>
<?= $this->Html->css('Search.search_options', ['block' => 'cssBottom']) ?>
<?= $this->Html->script('Search.search_options', ['block' => 'scriptBottom']) ?>
<?php
$availableColumns = [];
$displayColumns = [];
foreach ($searchFields as $k => $v) {
    if (in_array($k, $listingFields)) {
        $displayColumns[$k] = $v;
    } else {
        $availableColumns[$k] = $v;
    }
}
?>
    <div class="row">
        <div class="col-md-4">
        <?= $this->Form->label(__('Available Columns')) ?>
            <ul id="availableColumns" class="connectedSortable">
            <?php foreach ($availableColumns as $k => $v) : ?>
                <li data-id="<?= $k ?>">
                    <?= $v['label'] ?>
                </li>
            <?php endforeach; ?>
            </ul>
        </div>
        <div class="col-md-4">
        <?= $this->Form->label(__('Display Columns')) ?>
            <ul id="displayColumns" class="connectedSortable">
            <?php foreach ($displayColumns as $k => $v) : ?>
                <li data-id="<?= $k ?>">
                    <?= $v['label'] ?>
                </li>
            <?php endforeach; ?>
            </ul>
        </div>
        <div class="col-md-4">
        <?= $this->Form->label(__('Sort Field')) ?>
        <?php
        $sortByOptions = array_combine(
            array_keys($searchFields),
            array_map(function ($v) { return $v['label']; }, $searchFields)
        );
        echo $this->Form->select(
            'sort_by_field',
             $sortByOptions, [
                'class' => 'form-control input-sm'
            ]
        ) ?>
        <?= $this->Form->label(__('Sort Order')) ?>
        <?php
        echo $this->Form->select(
            'sort_by_order',
             ['asc' => 'Ascending', 'desc' => 'Descending'],
             [
                'class' => 'form-control input-sm'
            ]
        ) ?>
        <?= $this->Form->label(__('Limit results')) ?>
        <?php
        echo $this->Form->select(
            'limit',
             [0 => 'Unlimited', 1 => 1, 3 => 3, 5 => 5, 10 => 10, 20 => 20, 50 => 50, 100 => 100],
             [
                'default' => 10,
                'class' => 'form-control input-sm'
            ]
        ) ?>
        </div>
    </div>
<?php endif; ?>
