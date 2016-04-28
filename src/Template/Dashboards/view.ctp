<?php
$groupedAndSorted = [];
foreach ($savedSearches as $savedSearch) {
    $groupedAndSorted[$savedSearch['row']][] = $savedSearch;
}
ksort($groupedAndSorted);

foreach ($groupedAndSorted as &$r) {
    usort($r, function($a, $b) {
            return $a['column'] - $b['column'];
    });
}
?>
<div class="row">
    <div class="col-xs-12">
        <h3><?= h($dashboard->name) ?></h3>
        <?php foreach ($groupedAndSorted as $k) : ?>
        <div class="row">
            <?php foreach ($k as $savedSearch) : ?>
                <div class="col-md-6">
                    <?= $this->element('Search.search_results', $savedSearch); ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>
