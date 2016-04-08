<?php use Cake\Utility\Inflector; ?>
<div class="well">
    <h4><?= __('Saved Searches') ?></h4>
    <hr />
    <div class="row">
        <div class="col-xs-12 col-sm-6">
        <?php if (!empty($savedSearches)) : ?>
            <div class="row">
            <?php
            $groupedSavedSearches = [];
            foreach ($savedSearches as $savedSearch) {
                $groupedSavedSearches[$savedSearch->type][] = $savedSearch;
            }
            ksort($groupedSavedSearches);
            ?>
            <?php foreach ($groupedSavedSearches as $type => $searches) : $count = 12 / count($groupedSavedSearches) ?>
                <div class="col-xs-<?= $count ?> saved-searches">
                    <strong><?= Inflector::pluralize(Inflector::humanize($type)) ?>:</strong>
                    <?php foreach ($searches as $search) : ?>
                        <samp>
                        <?php
                        switch ($type) {
                            case 'result':
                                echo $this->Html->link($search->name, [
                                    'action' => 'saved_result',
                                    $search->model,
                                    $search->id
                                ]);
                                break;

                            case 'criteria':
                                $savedSearchContent = json_decode($search->content);
                                echo $this->Form->create(null, [
                                    'class' => 'saved-criteria-form',
                                    'url' => [
                                        'plugin' => 'Search',
                                        'controller' => 'Search',
                                        'action' => 'advanced',
                                        $search->model
                                    ]
                                ]);

                                foreach ($savedSearchContent as $fieldName => $properties) {
                                    foreach ($properties as $k => $property) {
                                        echo $this->Form->hidden($fieldName . '[' . $k . '][type]', [
                                            'value' => $property->type,
                                        ]);
                                        echo $this->Form->hidden($fieldName . '[' . $k . '][operator]', [
                                            'value' => $property->operator,
                                        ]);
                                        echo $this->Form->hidden($fieldName . '[' . $k . '][value]', [
                                            'value' => $property->value,
                                        ]);
                                    }
                                }

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
                                'action' => 'delete',
                                $search->id
                            ],
                            [
                                'confirm' => __('Are you sure you want to delete {0}?', $search->name),
                                'title' => __('Delete'),
                                'class' => 'saved-search-delete-form',
                                'escape' => false
                            ]
                        );
                        ?>
                        </samp>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
        </div>
        <div class="col-xs-12 col-sm-6">
            <?php if (isset($saveSearchCriteriaId) && isset($saveSearchResultsId)) : ?>
                <?= $this->Form->create(null, [
                    'class' => 'form-inline pull-right save-search-form',
                    'url' => [
                        'plugin' => 'Search',
                        'controller' => 'Search',
                        'action' => 'save',
                        $saveSearchResultsId
                    ]
                ]); ?>
                <div class="input-group">
                    <?= $this->Form->input('name', [
                        'label' => false,
                        'class' => 'form-control input-sm',
                        'placeholder' => 'Save results name',
                        'required' => true,
                        'value' => false
                    ]); ?>
                    <span class="input-group-btn">
                        <?= $this->Form->button(
                            '<span class="glyphicon glyphicon-floppy-save"></span>',
                            ['class' => 'btn btn-primary btn-sm']
                        ) ?>
                    </span>
                </div>
                <?= $this->Form->end(); ?>

                <?= $this->Form->create(null, [
                    'class' => 'form-inline pull-right save-search-form',
                    'url' => [
                        'plugin' => 'Search',
                        'controller' => 'Search',
                        'action' => 'save',
                        $saveSearchCriteriaId
                    ]
                ]); ?>
                <div class="input-group">
                    <?= $this->Form->input('name', [
                        'label' => false,
                        'class' => 'form-control input-sm',
                        'placeholder' => 'Save criteria name',
                        'required' => true,
                        'value' => ''
                    ]); ?>
                    <span class="input-group-btn">
                        <?= $this->Form->button(
                            '<span class="glyphicon glyphicon-floppy-save"></span>',
                            ['class' => 'btn btn-primary btn-sm']
                        ) ?>
                    </span>
                </div>
                <?= $this->Form->end(); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
