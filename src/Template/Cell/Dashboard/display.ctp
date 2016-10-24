<?php for ($i = 0; $i < $gridColumns; $i++) : ?>
    <div class="col-md-6">
    <?php if (!empty($savedSearches[$i])) : ?>
        <?php for ($x = 0; $x < $gridRows; $x++) : ?>
            <?php if (!empty($savedSearches[$i][$x])) : ?>
                <?= $this->element('Search.search_results', ['savedSearch' => $savedSearches[$i][$x]]); ?>
            <?php endif; ?>
        <?php endfor; ?>
    <?php endif; ?>
    </div>
<?php endfor; ?>