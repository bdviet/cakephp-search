<?php
use Cake\Utility\Inflector;
?>

<?php
if ('Search' === $this->request->params['plugin']) {
    $lookInModel = $this->request->params['pass'][0];
} else {
    $lookInModel = Inflector::tableize($this->request->params['controller']);
}
?>

<?= $this->Form->create(null, [
    'class' => 'navbar-form navbar-right',
    'url' => [
        'plugin' => 'Search',
        'controller' => 'Search',
        'action' => 'basic',
        $lookInModel
    ]
]); ?>

    <?= $this->Form->input('query', [
        'label' => false,
        'class' => 'form-control',
        'placeholder' => 'Search'
    ]); ?>

<?= $this->Form->end(); ?>
