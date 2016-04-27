<div class="row">
    <div class="col-xs-12">
        <h3><?= h($dashboard->name) ?></h3>
        <?php
        foreach ($savedSearches as $savedSearch) {
            echo $this->element('search_results', $savedSearch);
        }
        ?>
    </div>
</div>
