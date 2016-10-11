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
                                    'action' => 'search_saved_result',
                                    $search->id
                                ], [
                                    'id' => 'view_' . $search->id,
                                    'class' => 'hidden'
                                ]);
                                break;

                            case 'criteria':
                                $criterias[$search->id] = $search->name;
                                echo $this->Form->create(null, [
                                    'id' => 'view_' . $search->id,
                                    'class' => 'saved-criteria-form hidden',
                                    'url' => [
                                        'plugin' => $this->request->plugin,
                                        'controller' => $this->request->controller,
                                        'action' => 'search',
                                    ]
                                ]);

                                $savedSearchContent = json_decode($search->content);
                                /*
                                saved search criterias fields
                                 */
                                foreach ($savedSearchContent->criteria as $fieldName => $properties) {
                                    foreach ($properties as $k => $property) {
                                        echo $this->Form->hidden('criteria[' . $fieldName . '][' . $k . '][type]', [
                                            'value' => $property->type,
                                        ]);
                                        echo $this->Form->hidden('criteria[' . $fieldName . '][' . $k . '][operator]', [
                                            'value' => $property->operator,
                                        ]);
                                        echo $this->Form->hidden('criteria[' . $fieldName . '][' . $k . '][value]', [
                                            'value' => $property->value,
                                        ]);
                                    }
                                }
                                /*
                                saved search display columns
                                 */
                                foreach ($savedSearchContent->display_columns as $k => $display_column) {
                                    echo $this->Form->hidden('display_columns[' . $k . ']', [
                                        'value' => $display_column,
                                    ]);
                                }
                                /*
                                saved search sort by field
                                 */
                                echo $this->Form->hidden('sort_by_field', [
                                    'value' => $savedSearchContent->sort_by_field,
                                ]);
                                /*
                                saved search sort by order
                                 */
                                echo $this->Form->hidden('sort_by_order', [
                                    'value' => $savedSearchContent->sort_by_order,
                                ]);
                                /*
                                saved search limit
                                 */
                                echo $this->Form->hidden('limit', [
                                    'value' => $savedSearchContent->limit,
                                ]);

                                echo $this->Form->button(
                                    $search->name,
                                    ['class' => 'btn btn-link']
                                );

                                echo $this->Form->end();
                                break;
                        }
                        echo $this->Form->postLink(
                            '<span class="glyphicon glyphicon-minus"></span>',
                            [
                                'action' => 'search_delete',
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
