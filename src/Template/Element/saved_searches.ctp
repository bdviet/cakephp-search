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
                            <?php if ('result' === $type) {
                                echo $this->Html->link($search->name, ['action' => 'saved_result', $search->model, $search->id]);
                            } else {
                                echo $this->Html->link($search->name, ['action' => 'advanced', $search->model, $search->id]);
                            }
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
            <?php endif; ?>
        </div>
    </div>
</div>
