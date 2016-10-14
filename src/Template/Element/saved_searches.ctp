<?php use Cake\Utility\Inflector; ?>
<?php if (!empty($savedSearches)) : ?>
<?= $this->Html->script('Search.saved_searches', ['block' => 'scriptBottom']); ?>

<div class="well">
    <h4><?= __('Saved Searches') ?></h4>
    <hr />
    <div class="row">
        <div class="col-md-8 col-lg-6">
            <div class="row">
            <?php
            $groupedSavedSearches = [];
            foreach ($savedSearches as $savedSearch) {
                $groupedSavedSearches[$savedSearch->type][] = $savedSearch;
            }
            ksort($groupedSavedSearches);

            foreach ($groupedSavedSearches as $type => $searches) :
            $count = 12 / count($groupedSavedSearches);
            ?>
                <div class="col-sm-<?= $count ?> saved-searches">
                    <strong><?= Inflector::pluralize(Inflector::humanize($type)) ?>:</strong>
                    <?php
                    $criterias = [];
                    $results = [];
                    foreach ($searches as $search) :
                        switch ($type) {
                            case 'result':
                                $results[$search->id] = $search->name;
                                echo $this->Html->link($search->name, [
                                    'action' => 'save-search-result',
                                    $search->id
                                ], [
                                    'id' => 'view_' . $search->id,
                                    'class' => 'hidden'
                                ]);
                                break;

                            case 'criteria':
                                $criterias[$search->id] = $search->name;
                                break;
                        }
                        echo $this->Form->postLink(
                            '<span class="glyphicon glyphicon-minus"></span>',
                            [
                                'action' => 'delete-search',
                                $search->id
                            ],
                            [
                                'id' => 'delete_' . $search->id,
                                'confirm' => __('Are you sure you want to delete {0}?', $search->name),
                                'title' => __('Delete'),
                                'class' => 'saved-search-delete-form hidden',
                                'escape' => false
                            ]
                        );
                    ?>
                    <?php endforeach; ?>
                    <div class="input-group">
                    <?php
                    switch ($type) {
                        case 'result':
                            $selectFieldName = 'results';
                            $selectFieldOptions = $results;
                            $selectFieldId = 'savedResultsSelect';
                            $buttonViewId = 'savedResultsView';
                            $buttonDeleteId = 'savedResultsDelete';
                            break;

                        case 'criteria':
                            $selectFieldName = 'criterias';
                            $selectFieldOptions = $criterias;
                            $selectFieldId = 'savedCriteriasSelect';
                            $buttonViewId = 'savedCriteriasView';
                            $buttonDeleteId = 'savedCriteriasDelete';
                            break;
                    }
                    echo $this->Form->select($selectFieldName, $selectFieldOptions, [
                        'id' => $selectFieldId,
                        'class' => 'form-control input-sm'
                    ]);
                    ?>
                        <span class="input-group-btn">
                        <?php
                        echo $this->Form->button('<span class=" glyphicon glyphicon-eye-open"></span>', [
                            'id' => $buttonViewId,
                            'class' => 'btn btn-default btn-sm'
                        ]);
                        echo $this->Form->button('<span class="glyphicon glyphicon-trash"></span>', [
                            'id' => $buttonDeleteId,
                            'class' => 'btn btn-danger btn-sm'
                        ]);
                        ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
