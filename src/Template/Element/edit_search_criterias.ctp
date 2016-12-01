<?= $this->Form->create(null, [
    'class' => 'save-search-form',
    'url' => [
        'plugin' => $this->request->plugin,
        'controller' => $this->request->controller,
        'action' => 'edit-search',
        $savedSearch->id,
        $saveSearchCriteriaId
    ]
]); ?>
<div class="input-group">
    <?= $this->Form->input('name', [
        'label' => false,
        'class' => 'form-control input-sm',
        'placeholder' => 'Save criteria name',
        'required' => true,
        'value' => $savedSearch->name
    ]); ?>
    <span class="input-group-btn">
        <?= $this->Form->button(
            '<span class="glyphicon glyphicon-floppy-save"></span>',
            ['class' => 'btn btn-sm btn-warning']
        ) ?>
    </span>
</div>
<?= $this->Form->end(); ?>