<div class="dashboard-saved-searches">
    <div class="row">
    <?php foreach (array_keys($dashboardLayout) as $col) : ?>
        <div class="col-xs-6">
            <p class="h3 text-center"><?= $columns[$col] ?></p>
            <ul class="savetrue dropable-area" data-column=<?= $col ?>>
            <?php if (!empty($dashboardSavedSearches[$col])) : ?>
            <?php foreach ($dashboardSavedSearches[$col] as $rows) : ?>
                <?php foreach ($rows as $row) : ?>
                <li class="dropable" data-id="<?= $row->id ?>">
                    <div class="header">
                    <?php
                        list($plugin, $model) = pluginSplit($row->model);
                        echo $model;
                    ?>
                    </div>
                    <div class="content"><?= $row->name ?></div>
                </li>
                <?php endforeach; ?>
            <?php endforeach; ?>
            <?php endif ?>
            </ul>
        </div>
    <?php endforeach; ?>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <p class="h3 text-center saved-searches-title"><?= __('Saved Searches') ?></p>
            <ul class="list-inline dropable-area saved-searches-area">
                <?php foreach ($allSavedSearches as $savedSearch) : ?>
                <li class="dropable col-xs-1" data-id="<?= $savedSearch['id'] ?>">
                    <div class="header">
                    <?php
                        list($plugin, $model) = pluginSplit($savedSearch['model']);
                        echo $model;
                    ?>
                    </div>
                    <div class="content"><?= $savedSearch['name'] ?></div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>