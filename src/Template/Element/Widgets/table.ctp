<?php
use Cake\Event\Event;
use Cake\Utility\Inflector;

$savedSearch = $widget->getData();
$searchData = $savedSearch->entities;
$savedSearchType = $widget->getSavedSearchType();

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

$uid = md5($savedSearch->id);
?>
<?php if (!empty($searchData['result'])) : ?>
<div class="dashboard-widget-saved_search">
    <div class="box box-default">
        <div class="box-header">
            <h3 class="box-title"><?= $title; ?></h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="box-body">
            <table id="table-datatable-<?= $uid ?>" class="table table-hover table-condensed table-vertical-align">
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
                            ?>
                            <div class="btn-group btn-group-xs" role="group">
                            <?= $event->result; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>
