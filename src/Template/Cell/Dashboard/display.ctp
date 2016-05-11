<?php for ($i = 0; $i < $gridRows; $i++) : ?>
    <div class="row">
    <?php if (!empty($savedSearches[$i])) : ?>
        <?php for ($x = 0; $x < $gridColumns; $x++) : ?>
            <div class="col-md-6">
            <?php if (!empty($savedSearches[$i][$x])) : ?>
                <?= $this->element('Search.search_results', [
                    'search_name' => $savedSearches[$i][$x]['search_name'],
                    'model' => $savedSearches[$i][$x]['model'],
                    'entities' => $savedSearches[$i][$x]['entities']['result'],
                    'listingFields' => $savedSearches[$i][$x]['entities']['display_columns']
                ]); ?>
            <?php endif; ?>
            </div>
        <?php endfor; ?>
    <?php endif; ?>
    </div>
<?php endfor; ?>
