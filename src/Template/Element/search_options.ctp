<?php
echo $this->Html->css('Search.search_options', ['block' => 'cssBottom']);
echo $this->Html->script('Search.search_options', ['block' => 'scriptBottom']);

$availableColumns = [];
$displayColumns = [];
// get display and available columns
foreach ($searchFields as $k => $v) {
    if (in_array($k, $searchData['display_columns'])) {
        $displayColumns[$k] = $v['label'];
    } else {
        $availableColumns[$k] = $v['label'];
    }
}

// alphabetically sort available columns
asort($availableColumns);

// sort display columns based on saved search display_columns order
$displayColumns = array_merge(array_flip($searchData['display_columns']), $displayColumns);

$sortByOptions = array_merge($availableColumns, $displayColumns);
// alphabetically sort sortByOptions
asort($sortByOptions);
?>
<div class="row">
    <div class="col-md-4">
    <?= $this->Form->label(__('Available Columns')) ?>
        <ul id="availableColumns" class="connectedSortable">
        <?php foreach ($availableColumns as $k => $v) : ?>
            <li data-id="<?= $k ?>">
                <?= $v ?>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
    <div class="col-md-4">
    <?= $this->Form->label(__('Display Columns')) ?>
        <ul id="displayColumns" class="connectedSortable">
        <?php foreach ($displayColumns as $k => $v) : ?>
            <li data-id="<?= $k ?>">
                <?= $v ?>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
    <div class="col-md-4">
    <?php
        echo $this->Form->label(__('Sort Field'));
        echo $this->Form->select(
            'sort_by_field',
            $sortByOptions,
            [
                'default' => isset($searchData['sort_by_field']) ? $searchData['sort_by_field'] : key($sortByOptions),
                'class' => 'form-control input-sm'
             ]
        );
        echo $this->Form->label(__('Sort Order'));
        echo $this->Form->select(
            'sort_by_order',
            ['asc' => 'Ascending', 'desc' => 'Descending'],
            [
                'default' => isset($searchData['sort_by_order']) ? $searchData['sort_by_order'] : 'asc',
                'class' => 'form-control input-sm'
             ]
        );
        echo $this->Form->label(__('Limit results'));
        echo $this->Form->select(
            'limit',
            [0 => 'Unlimited', 1 => 1, 3 => 3, 5 => 5, 10 => 10, 20 => 20, 50 => 50, 100 => 100],
            [
                'default' => isset($searchData['limit']) ? $searchData['limit'] : 10,
                'class' => 'form-control input-sm'
             ]
        );
    ?>
    </div>
</div>
