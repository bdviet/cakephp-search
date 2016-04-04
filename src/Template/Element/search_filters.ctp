<?php if (!empty($searchFields) && !empty($searchOperators)) : ?>

    <?= $this->Html->script('Search.search', ['block' => 'scriptBottom']); ?>

    <?= $this->Html->scriptBlock(
        'search.setFieldTypeOperators(' . json_encode($searchOperators) . ');',
        ['block' => 'scriptBottom']
    ); ?>

    <?= $this->Html->scriptBlock(
        'search.setFieldProperties(' . json_encode($searchFields) . ');',
        ['block' => 'scriptBottom']
    ); ?>

    <div class="row">
        <div class="col-xs-12">
            <?= $this->Form->create(null, [
                'id' => 'SearchFilterForm',
                'url' => [
                    'plugin' => 'Search',
                    'controller' => 'Search',
                    'action' => 'advanced',
                    $this->request->params['pass'][0]
                ]
            ]) ?>
            <fieldset>
                <legend><?= __('Filters') ?></legend>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="body"></div>
                    </div>
                    <div class="col-xs-offset-3 col-xs-3">
                        <?= $this->Form->label(__('Add Filter')) ?>
                        <?= $this->Form->select('fields', array_combine(array_keys($searchFields), array_keys($searchFields)), [
                            'class' => 'form-control',
                            'id' => 'addFilter',
                            'empty' => true
                        ]) ?>
                    </div>
                </div>
            </fieldset>
            <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
<?php endif; ?>
