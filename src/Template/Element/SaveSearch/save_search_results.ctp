<?= $this->Form->create(null, [
    'class' => 'save-search-form',
    'url' => [
        'plugin' => $this->request->plugin,
        'controller' => $this->request->controller,
        'action' => 'save-search',
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