<?php
$groupedAndSorted = [];
$gridRows = 0;
$gridColumns = 0;

foreach ($savedSearches as $savedSearch) {
    $groupedAndSorted[$savedSearch['row']][$savedSearch['column']] = $savedSearch;
    ksort($groupedAndSorted[$savedSearch['row']]);
}
ksort($groupedAndSorted);

/*
get grid total rows
 */
$gridRows = count($groupedAndSorted);

/*
get grid total columns
 */
foreach ($groupedAndSorted as $rowSearches) {
    $colsCount = count($rowSearches);
    if ($colsCount > $gridColumns) {
        $gridColumns = $colsCount;
    }
}
?>

<h3><?= h($dashboard->name) ?></h3>
<?php for ($i = 0; $i < $gridRows; $i++) : ?>
    <div class="row">
    <?php if (!empty($groupedAndSorted[$i])) : ?>
        <?php for ($x = 0; $x < $gridColumns; $x++) : ?>
            <div class="col-md-6">
            <?php if (!empty($groupedAndSorted[$i][$x])) : ?>
                <?= $this->element('Search.dashboard_search_results', [
                    'search_name' => $groupedAndSorted[$i][$x]['search_name'],
                    'entities' => $groupedAndSorted[$i][$x]['entities']->result,
                    'listingFields' => $groupedAndSorted[$i][$x]['entities']->display_columns
                ]); ?>
            <?php endif; ?>
            </div>
        <?php endfor; ?>
    <?php endif; ?>
    </div>
<?php endfor; ?>
