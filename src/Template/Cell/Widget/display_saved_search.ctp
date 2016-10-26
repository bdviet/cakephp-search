<div class='dashboard-widget-saved_search'>
    <div class="col-md-6">
    <?php echo $this->element('Search.search_results', [
        'searchType' => $renderData->type,
        'searchName' => $renderData->name,
        'model'      => $renderData->model,
        'entities'   => $renderData->entities['result'],
        'listingFields' => $renderData->entities['display_columns']
    ]);?>
    </div>
</div>
