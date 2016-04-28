<?= $this->Html->css('Search.dashboard', ['block' => 'cssBottom']) ?>
<?= $this->Html->script('Search.dashboard', ['block' => 'scriptBottom']) ?>
<?php
$existingSavedSearches = [];
$savedSearches = $savedSearches->toArray();
if (!empty($dashboard->saved_searches)) {
    foreach ($dashboard->saved_searches as $search) {
        unset($savedSearches[$search->id]);
        $existingSavedSearches[$search->_joinData->column][$search->_joinData->row] = [$search->id => $search->name];
        ksort($existingSavedSearches[$search->_joinData->column]);
    }
    ksort($existingSavedSearches);
}
?>
<div class="dashboard-saved-searches">
    <div class="row">
        <div class="col-xs-7">
            <p class="h3 text-center">Main Section</p>
            <ul class="savetrue dropable-area" data-column=0>
                <?php if (!empty($existingSavedSearches[0])) : ?>
                    <?php foreach ($existingSavedSearches[0] as $rows) : ?>
                        <?php foreach ($rows as $id => $name) : ?>
                        <li class="dropable" data-id="<?= $id ?>">
                            <div class="header"><?= $name ?></div>
                        </li>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php endif ?>
            </ul>
        </div>
        <div class="col-xs-5">
            <p class="h3 text-center">Side Section</p>
            <ul class="savetrue dropable-area" data-column=1>
                <?php if (!empty($existingSavedSearches[1])) : ?>
                    <?php foreach ($existingSavedSearches[1] as $rows) : ?>
                        <?php foreach ($rows as $id => $name) : ?>
                        <li class="dropable" data-id="<?= $id ?>">
                            <div class="header"><?= $name ?></div>
                        </li>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php endif ?>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <ul class="list-inline dropable-area saved-searches-area">
                <?php foreach ($savedSearches as $id => $name) : ?>
                <li class="dropable" data-id="<?= $id ?>">
                    <div class="header"><?= $name ?></div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>