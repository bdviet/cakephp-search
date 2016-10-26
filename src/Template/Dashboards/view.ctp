<?php
use Cake\Event\Event;
?>

<div class="row">
    <div class="col-xs-6">
        <h3><strong><?= h($dashboard->name) ?></strong></h3>
    </div>
    <div class="col-xs-6">
    <?php
    $event = new Event('Search.Dashboards.View.View.Menu.Top', $this, [
        'request' => $this->request,
        $dashboard
    ]);
    $this->eventManager()->dispatch($event);
    if (!empty($event->result)) : ?>
        <div class="h3 text-right">
            <?= $event->result; ?>
        </div>
    <?php endif; ?>
    </div>
</div>

<div class="row">
    <?php if( !empty($widgets) ) : ?>
        <?php for($i=0; $i < $columns; $i++) { ?>
            <div class="col-md-6">
                <?php if (!empty($widgets[$i])): ?>
                    <?php for ($j = 0; $j < $rows; $j++): ?>
                        <?php if (!empty($widgets[$i][$j])): ?>
                            <?php echo $this->cell("Search.Widget::{$widgets[$i][$j]->widgetDisplayMethod}" , [ [$widgets[$i][$j]], ['user' => $user] ]); ?>
                        <?php endif; ?>
                    <?php endfor;?>
                <?php endif;?>
            </div>
        <?php }?>
    <?php endif; ?>
</div>

<?= $this->element('Search.common_js_libs'); ?>
