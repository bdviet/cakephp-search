<?php
use Cake\Event\Event;
use Search\WidgetHandlers\WidgetHandlerFactory;

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
                        $widgetHandler = WidgetHandlerFactory::create($dw->widget_type, ['entity' => $dw]);

                        $widgetHandler->getResults(['entity' => $dw, 'user' => $user, 'rootView' => $this]);

                        if ($widgetHandler->getType() == 'knobChart') {
                            //debug($widgetHandler);
                        }

                        $dataOptions = $widgetHandler->getDataOptions();
                        if (!empty($dataOptions)) {
                            if ($widgetHandler->getRenderElement() == 'report') {
                                $chartData[] = $widgetHandler->getData();
                            }
                            $scripts[] = $dataOptions;
                        }

                        echo $this->element('Search.Widgets/' . $widgetHandler->getRenderElement(), ['widget' => $widgetHandler]);
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

<?php echo $this->element('Search.widget_libraries', ['scripts' => $scripts, 'chartData' => $chartData]); ?>
