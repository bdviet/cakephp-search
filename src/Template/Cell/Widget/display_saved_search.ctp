<div class='dashboard-widget-saved_search'>
    <?php echo $rootView->element('Search.search_results', [
        'searchType' => $renderData->type,
        'searchName' => $renderData->name,
        'model'      => $renderData->model,
        'searchData' => $renderData->entities
    ]);?>
</div>
