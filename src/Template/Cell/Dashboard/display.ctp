<?php for ($i = 0; $i < $gridColumns; $i++) : ?>
    <div class="col-md-6">
    <?php if (!empty($savedSearches[$i])) : ?>
        <?php for ($x = 0; $x < $gridRows; $x++) : ?>
            <?php if (!empty($savedSearches[$i][$x])) : ?>
                <?= $this->element('Search.search_results', [
                    'searchType' => $savedSearches[$i][$x]['search_type'],
                    'searchName' => $savedSearches[$i][$x]['search_name'],
                    'model' => $savedSearches[$i][$x]['model'],
                    'entities' => $savedSearches[$i][$x]['entities']['result'],
                    'listingFields' => $savedSearches[$i][$x]['entities']['display_columns']
                ]); ?>
            <?php endif; ?>
        <?php endfor; ?>
    <?php endif; ?>
    </div>
<?php endfor; ?>