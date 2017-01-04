<?= $this->Form->create(null, [
    'class' => 'save-search-form',
    'url' => [
        'plugin' => $this->request->plugin,
        'controller' => $this->request->controller,
        'action' => ($isEditable ? 'edit': 'save') . '-search',
        $saveSearchResultsId,
        $isEditable ? $savedSearch->id : null
    ]
]); ?>
<div class="input-group">
    <?= $this->Form->input('name', [
        'label' => false,
        'class' => 'form-control input-sm',
        'placeholder' => 'Save results name',
        'required' => true,
        'value' => $isEditable ? $savedSearch->name : ''
    ]); ?>
    <span class="input-group-btn">
        <?= $this->Form->button(
            '<span class="glyphicon glyphicon-floppy-save"></span>',
            ['class' => 'btn btn-primary btn-sm']
        ) ?>
    </span>
</div>
<?= $this->Form->end(); ?>