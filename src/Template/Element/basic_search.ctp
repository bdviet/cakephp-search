<?php
use Cake\Utility\Inflector;
?>

<?php
if ('Search' === $this->request->params['plugin'] && 'Search' === $this->request->params['controller']) {
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
        'plugin' => $this->request->plugin,
        'controller' => $this->request->controller,
        'action' => 'basic'
    ]
]); ?>

    <?= $this->Form->input('criteria[query]', [
        'label' => false,
        'class' => 'form-control',
        'placeholder' => 'Search'
    ]); ?>

<?= $this->Form->end(); ?>
