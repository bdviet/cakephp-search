<?php
use Cake\Utility\Inflector;

if ('Search' === $this->request->params['plugin'] && 'Search' === $this->request->params['controller']) {
    $lookInModel = $this->request->params['pass'][0];
} else {
    $modelName = $this->request->params['controller'];
    if (!is_null($this->request->params['plugin'])) {
        $modelName = $this->request->params['plugin'] . '.' . $modelName;
    }
    $lookInModel = $modelName;
}
echo $this->Form->create(null, [
    'class' => 'navbar-form navbar-right',
    'url' => [
        'plugin' => $this->request->plugin,
        'controller' => $this->request->controller,
        'action' => 'search'
    ]
]);

echo $this->Form->input('criteria[query]', [
        'label' => false,
        'class' => 'form-control',
        'placeholder' => 'Search'
]);

echo $this->Form->end();
