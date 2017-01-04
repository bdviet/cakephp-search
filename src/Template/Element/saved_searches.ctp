<?php
use Cake\Utility\Inflector;

if (!empty($savedSearches)) :
    echo $this->Html->script('Search.saved_searches', ['block' => 'scriptBottom']);
?>

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
                                break;

                            case 'criteria':
                                $criterias[$search->id] = $search->name;
                                break;
                        }

                        echo $this->Html->link($search->name, [
                            'action' => 'search',
                            $search->id
                        ], [
                            'id' => 'view_' . $search->id,
                            'class' => 'hidden'
                        ]);

                        echo $this->Form->postLink(
                            null,
                            [
                                'action' => 'copy-search',
                                $search->id
                            ],
                            [
                                'id' => 'copy_' . $search->id,
                                'title' => __('Copy'),
                                'class' => 'saved-search-copy-form hidden',
                                'escape' => false
                            ]
                        );

                        echo $this->Form->postLink(
                            null,
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
                    endforeach; ?>
                    <div class="input-group">
                    <?php
                    switch ($type) {
                        case 'result':
                            $selectFieldName = 'results';
                            $selectFieldOptions = $results;
                            $selectFieldId = 'savedResultsSelect';
                            $buttonViewId = 'savedResultsView';
                            $buttonDeleteId = 'savedResultsDelete';
                            $buttonCopyId = 'savedResultsCopy';
                            break;

                        case 'criteria':
                            $selectFieldName = 'criterias';
                            $selectFieldOptions = $criterias;
                            $selectFieldId = 'savedCriteriasSelect';
                            $buttonViewId = 'savedCriteriasView';
                            $buttonDeleteId = 'savedCriteriasDelete';
                            $buttonCopyId = 'savedCriteriasCopy';
                            break;
                    }
                    echo $this->Form->select($selectFieldName, $selectFieldOptions, [
                        'id' => $selectFieldId,
                        'default' => $this->request->param('pass.0'),
                        'class' => 'form-control input-sm'
                    ]);
                    ?>
                        <span class="input-group-btn">
                        <?php
                        echo $this->Form->button('<span class=" glyphicon glyphicon-eye-open"></span>', [
                            'id' => $buttonViewId,
                            'class' => 'btn btn-default btn-sm'
                        ]);
                        echo $this->Form->button('<span class=" glyphicon glyphicon-copy"></span>', [
                            'id' => $buttonCopyId,
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
            <?php
            endforeach;
            ?>
            </div>
        </div>
    </div>
</div>
<?php endif;
