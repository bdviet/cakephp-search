<?php
use Cake\Event\Event;
use Search\WidgetHandlers\WidgetHandlerFactory;

echo $this->Html->css([
    'AdminLTE./plugins/datatables/dataTables.bootstrap',
    ], [
    'block' => 'css'
    ]);

echo $this->Html->script(
    [
        'AdminLTE./plugins/datatables/jquery.dataTables.min',
        'AdminLTE./plugins/datatables/dataTables.bootstrap.min',
        'Search.view-search-result',
    ],
    [
        'block' => 'scriptBotton'
    ]
);
$scripts = [];
$chartData = [];

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
        <?php $columnsCount = count($columns); for ($col = 0; $col < $columnsCount; $col++) : ?>
            <div class="col-md-<?= 12 / $columnsCount ?>">
            <?php if (!empty($dashboardWidgets)) : ?>
                <?php
                foreach ($dashboardWidgets as $dw) {
                    if ($dw->column !== $col) {
                        continue;
                    }

                    try {
                        $widgetHandler = WidgetHandlerFactory::create($dw->widget_type, [
                            'entity' => $dw,
                            'rootView' => $this,
                        ]);

                        $widgetHandler->getResults(['user' => $user, 'rootView' => $this]);
                        $dataOptions = $widgetHandler->getDataOptions();
                        if (!empty($dataOptions)) {
                            if ($widgetHandler->getType() == 'report') {
                                $chartData[] = $widgetHandler->getData();
                            }
                            $scripts[] = $dataOptions;
                        }

                        echo $this->element('Search.Widgets/' . $widgetHandler->getType(), ['widget' => $widgetHandler]);
                    } catch (\Exception $e) {
                        echo $this->element('Search.missing_element', ['exception' => $e]);
                    }
                }
                ?>
                <?php endif; ?>
            </div>
        <?php endfor; ?>
    </div>
</section>

<?php echo $this->element('Search.footer_libraries', ['scripts' => $scripts, 'chartData' => $chartData]); ?>
