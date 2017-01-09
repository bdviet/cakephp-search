<?php
use Cake\Event\Event;

echo $this->Html->css('Search.datatables.min', ['block' => 'css']);
echo $this->Html->script('Search.datatables.min', ['block' => 'scriptBotton']);
echo $this->Html->script('Search.view-search-result', ['block' => 'scriptBotton']);
$event = new Event('Search.Dashboards.View.View.Menu.Top', $this, [
    'request' => $this->request,
    $dashboard
]);
$this->eventManager()->dispatch($event);
?>
<section class="content-header">
    <h1>
        Dashboard
        <small><?= h($dashboard->name) ?> <?= $event->result; ?></small>
    </h1>
</section>
<section class="content">
    <div class="row">
    <?php if (!empty($widgets)) : ?>
        <?php $columnsCount = count($columns); for ($col = 0; $col < $columnsCount; $col++) : ?>
            <div class="col-md-<?= 12 / $columnsCount ?>">
            <?php
            foreach ($widgets as $widget) {
                if ($widget->widgetObject->column !== $col) {
                    continue;
                }
                echo $this->cell("Search.Widget::{$widget->widgetDisplayMethod}", [
                    [$widget],
                    ['user' => $user, 'rootView' => $this]
                ]);
            }
            ?>
            </div>
        <?php endfor; ?>
    <?php endif; ?>
    </div>
</section>