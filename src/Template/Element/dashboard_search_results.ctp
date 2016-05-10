<?php
use Cake\Event\Event;
use Cake\Utility\Inflector;
?>

<?php
if (!empty($entities)) :
    if (!isset($search_name)) {
        $model_name = isset($model_name) ? $model_name : $this->request->params['pass'][0];
        list($plugin, $name) = pluginSplit($model_name);
        $search_name = '<strong>' . $name . '</strong> ' . __('search results') . ':';
    }
?>
<div class="row">
    <div class="col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= $search_name ?></h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                            <?php foreach ($listingFields as $field) : ?>
                                <th><?php
                                    if (isset($sortable_fields)) {
                                        echo $this->Paginator->sort($field);
                                    } else {
                                        echo Inflector::humanize($field);
                                    }
                                ?></th>
                            <?php endforeach; ?>
                            <th class="actions"><?= __('Actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($entities as $entity) : ?>
                            <tr>
                                <?php foreach ($listingFields as $field) : ?>
                                    <td><?= $entity->{$field} ?></td>
                                <?php endforeach; ?>
                                <td class="actions">
                                    <?php
                                        $event = new Event('Search.View.View.Menu.Actions', $this, [
                                            'request' => $this->request,
                                            'options' => $entity,
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
<?php endif; ?>