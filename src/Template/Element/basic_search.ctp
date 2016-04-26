<?php
use Cake\Utility\Inflector;
?>

<?php
if ('Search' === $this->request->params['plugin']) {
    $lookInModel = $this->request->params['pass'][0];
} else {
    $modelName = $this->request->params['controller'];
    if (!is_null($this->request->params['plugin'])) {
        $modelName = $this->request->params['plugin'] . '.' . $modelName;
    }
    $lookInModel = $modelName;
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
