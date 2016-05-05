<?php if (isset($saveSearchCriteriaId)) : ?>
    <?= $this->Form->create(null, [
        'class' => 'save-search-form',
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