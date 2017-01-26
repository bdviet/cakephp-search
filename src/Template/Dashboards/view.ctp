<?php
use Cake\Event\Event;

echo $this->Html->css([
    'AdminLTE./plugins/datatables/dataTables.bootstrap',
    'AdminLTE./plugins/morris/morris'
    ], [
    'block' => 'css'
    ]);

echo $this->Html->script(
    [
        'AdminLTE./plugins/datatables/jquery.dataTables.min',
        'AdminLTE./plugins/datatables/dataTables.bootstrap.min',
        //raphael is required for morris js
        'https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js',
        'AdminLTE./plugins/morris/morris.min',
        'Search.view-search-result',
        'Search.reportGraphs'
    ],
    [
        'block' => 'scriptBotton'
    ]
);
$chartOptions = [];
$event = new Event('Search.Dashboards.View.View.Menu.Top', $this, [
    'request' => $this->request,
    $dashboard
]);
$this->eventManager()->dispatch($event);
?>
<section class="content-header">
    <h1>
        Dashboard
        <small><?= h($dashboard->name) ?></small>
        <div class="pull-right">
            <div class="btn-group btn-group-sm" role="group">
                <?= $event->result; ?>
            </div>
        </div>
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
                $cell = $this->cell("Search.Widget::{$widget->widgetDisplayMethod}", [
                    [$widget],
                    ['user' => $user, 'rootView' => $this]
                ]);

                echo $cell;

                if (!empty($cell->chartData)) {
                    array_push($chartOptions, $cell->chartData);
                }
            }
            ?>
            </div>
        <?php endfor; ?>
    <?php endif; ?>
    </div>
</section>
<?php
//after we collected all required graph data we can do the rendering.
echo $this->Html->scriptBlock('var chartsData = ' . json_encode($chartOptions) . ';');
?>
