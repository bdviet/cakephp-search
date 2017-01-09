<div class='dashboard-widget-saved_search'>
    <?php echo $rootView->element('Search.search_results', [
        'savedSearch' => $renderData,
        'searchData' => $renderData->entities
    ]);?>
</div>
