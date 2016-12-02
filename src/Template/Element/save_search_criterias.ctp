<?= $this->Form->create(null, [
    'class' => 'save-search-form',
    'url' => [
        'plugin' => $this->request->plugin,
        'controller' => $this->request->controller,
        'action' => ($isEditable ? 'edit': 'save') . '-search',
        $saveSearchCriteriaId,
        $isEditable ? $savedSearch->id : null
    ]
]); ?>
<div class="input-group">
    <?= $this->Form->input('name', [
        'label' => false,
        'class' => 'form-control input-sm',
        'placeholder' => 'Save criteria name',
        'required' => true,
        'value' => $isEditable ? $savedSearch->name : ''
    ]); ?>
    <span class="input-group-btn">
        <?= $this->Form->button(
            '<span class="glyphicon glyphicon-floppy-save"></span>',
            ['class' => 'btn btn-sm ' . ($isEditable ? 'btn-warning' : 'btn-primary')]
        ) ?>
    </span>
</div>
<?= $this->Form->end(); ?>