<?php
use Cake\Event\Event;

echo $this->Html->css('Search.datatables.min', ['block' => 'cssBottom']);
echo $this->Html->script('Search.datatables.min', ['block' => 'scriptBottom']);
echo $this->Html->script('Search.view-search-result', ['block' => 'scriptBottom']);
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
    <?php if (!empty($widgets)) : ?>
        <?php for ($col = 0; $col < count($columns); $col++) : ?>
            <div class="col-md-<?= 12 / count($columns) ?>">
                <?php foreach ($widgets as $widget) {
                    if ($widget->widgetObject->column !== $col) {
                        continue;
                    }
                    echo $this->cell("Search.Widget::{$widget->widgetDisplayMethod}" , [
                        [$widget],
                        ['user' => $user, 'rootView' => $this]
                    ]);
                } ?>
            </div>
        <?php endfor; ?>
    <?php endif; ?>
</div>
