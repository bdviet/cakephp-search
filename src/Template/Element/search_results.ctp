<?php
use Cake\Event\Event;
use Cake\Utility\Inflector;

// get search information from the saved search (if is set) to construct search results title
if (!empty($savedSearch)) {
    $searchId = $savedSearch->id;
    $searchName = $savedSearch->name;
    $model = $savedSearch->model;
}

// search title
$title = $this->name;
if (!empty($searchName)) {
    $title = $searchName;
}

//search url if is a saved one
$url = null;
if (!empty($model) && !empty($searchId)) {
    list($plugin, $controller) = pluginSplit($model);
    $url = [
        'plugin' => $plugin,
        'controller' => $controller,
        'action' => 'search',
        $searchId
    ];
} elseif (!empty($searchId)) {
    $url = $this->request->here;
}

if (!empty($url)) {
    $title = '<a href="' . $this->Url->build($url) . '">' . $title . '</a>';
}

$uid = uniqid();
?>
<?php if (!empty($searchData['result'])) : ?>
<div class="row">
    <div class="col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <strong><?= $title; ?></strong>
                </h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="table-datatable-<?= $uid ?>" class="table table-hover">
                        <thead>
                            <tr>
                            <?php foreach ($searchData['display_columns'] as $field) : ?>
                                <th><?= Inflector::humanize($field); ?></th>
                            <?php endforeach; ?>
                                <th class="actions"><?= __('Actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($searchData['result'] as $entity) : ?>
                            <tr>
                                <?php foreach ($searchData['display_columns'] as $field) : ?>
                                    <td><?= isset($entity[$field]) ? $entity[$field] : null; ?></td>
                                <?php endforeach; ?>
                                <td class="actions">
                                    <?php
                                    $event = new Event('Search.View.View.Menu.Actions', $this, [
                                        'entity' => $entity,
                                        'model' => $model
                                    ]);
                                    $this->eventManager()->dispatch($event);
                                    if (!empty($event->result)) {
                                        echo $event->result;
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->Html->scriptBlock(
                            'view_search_result.init({
        table_id: \'#table-datatable-' . $uid . '\',
        sort_by_field: \'' . (int)array_search($searchData['sort_by_field'], $searchData['display_columns']) . '\',
        sort_by_order: \'' . $searchData['sort_by_order'] . '\'
    });',
                            ['block' => 'scriptBottom']
);
?>
<?php endif; ?>
