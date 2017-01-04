<div class="row">
    <div class="col-xs-12">
        <p class="h3 text-center saved-searches-title"><?= __('Reports') ?></p>
        <ul class="list-inline droppable-area reports-area">
            <?php if (!empty($reports)) { ?>
                <?php foreach ($reports as $model => $items) { ?>
                    <?php foreach ($items as $slug => $item) : ?>
                    <li class="droppable col-xs-1" data-id="<?= $item['id'] ?>" data-type='<?= $item['widget_type'] ?>'>
                        <div class="header"><?= $model?></div>
                        <div class="content"><?= $item['title'] ?></div>
                        </li>

                    <?php endforeach; ?>
                <?php } ?>
            <?php } ?>
        </ul>
    </div>
</div>
