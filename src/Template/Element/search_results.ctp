<?php
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
                <table class="table table-hover">
                    <thead>
                        <tr>
                        <?php foreach ($fields as $field) : ?>
                            <th><?= $this->Paginator->sort($field); ?></th>
                        <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($entities as $entity) : ?>
                        <tr>
                            <?php foreach ($fields as $field) : ?>
                                <td><?= $entity->{$field} ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>