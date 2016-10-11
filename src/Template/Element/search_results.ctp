<?php
use Cake\Event\Event;
use Cake\Utility\Inflector;
?>

<?php if (!empty($entities)) : ?>
<div class="row">
    <div class="col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <strong><?= isset($searchName) ? $searchName : $this->name; ?></strong> <?= __('search results'); ?>:
                </h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover table-datatable">
                        <thead>
                            <tr>
                            <?php foreach ($listingFields as $field) : ?>
                                <th><?= Inflector::humanize($field); ?></th>
                            <?php endforeach; ?>
                                <th class="actions"><?= __('Actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($entities as $entity) : ?>
                            <tr>
                                <?php foreach ($listingFields as $field) : ?>
                                    <td><?= isset($entity[$field]) ? $entity[$field] : null; ?></td>
                                <?php endforeach; ?>
                                <td class="actions">
                                    <?php
                                        $event = new Event('Search.View.View.Menu.Actions', $this, [
                                            'request' => $this->request,
                                            'entity' => $entity
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