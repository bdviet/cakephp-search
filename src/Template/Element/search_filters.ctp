<?php
if (!empty($searchFields)) :
    echo $this->Html->css('Search.search', ['block' => 'css']);
    echo $this->Html->script('Search.search', ['block' => 'scriptBotton']);

    echo $this->Html->scriptBlock(
        'search.setFieldProperties(' . json_encode($searchFields) . ');',
        ['block' => 'scriptBotton']
    );
    if (isset($searchData['criteria'])) {
        echo $this->Html->scriptBlock(
            'search.generateCriteriaFields(' . json_encode($searchData['criteria']) . ');',
            ['block' => 'scriptBotton']
        );
    }
?>
<div class="box box-default collapsed-box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __('Advanced Search') ?></h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
                <h4><?= __('Filters') ?></h4>
            </div>
            <div class="col-lg-3 col-lg-push-9">
                <?php
                echo $this->Form->label(__('Add Filter'));
                $selectOptions = array_combine(
                    array_keys($searchFields),
                    array_map(function ($v) {
                        return $v['label'];
                    }, $searchFields)
                );
                //sort the list alphabetically for dropdown
                asort($selectOptions);

                echo $this->Form->select(
                    'fields',
                    $selectOptions,
                    [
                        'class' => 'form-control input-sm',
                        'id' => 'addFilter',
                        'empty' => true
                     ]
                ); ?>
            </div>
            <?= $this->Form->create(null, [
                'id' => 'SearchFilterForm',
                'class' => 'search-form',
                'novalidate' => 'novalidate',
                'url' => [
                    'plugin' => $this->request->plugin,
                    'controller' => $this->request->controller,
                    'action' => 'search',
                    $this->request->param('pass.0')
                ]
            ]); ?>
            <hr class="visible-xs visible-sm visible-md" />
            <div class="col-lg-9 col-lg-pull-3">
                <fieldset></fieldset>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <h4><?= __('Options') ?></h4>
            </div>
            <div class="col-md-8 col-lg-9">
                <?php
                if (!empty($searchFields)) {
                    echo $this->element('Search.search_options');
                }
                echo $this->Form->button(__('Search'), ['class' => 'btn btn-primary']);
                echo $this->Form->end();
                ?>
            </div>
            <div class="col-md-4 col-lg-3">
                <div class="row">
                    <div class="col-sm-6 col-md-12">
                        <?= $this->Form->label(__('Save search')) ?>
                    </div>
                    <div class="col-sm-6 col-md-12">
                    <?php
                    if (isset($saveSearchCriteriaId)) {
                        echo $this->element('Search.SaveSearch/save_search_criterias', [
                        'saveSearchCriteriaId' => $saveSearchCriteriaId,
                        'savedSearch' => $savedSearch,
                        'isEditable' => $isEditable && 'criteria' === $savedSearch->type
                        ]);
                    }
                    if (isset($saveSearchResultsId)) {
                        echo $this->element('Search.SaveSearch/save_search_results', [
                        'saveSearchCriteriaId' => $saveSearchResultsId,
                        'savedSearch' => $savedSearch,
                        'isEditable' => $isEditable && 'result' === $savedSearch->type
                        ]);
                    }
                    ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$scripts = [];

foreach ($searchFields as $searchField) {
    if (empty($searchField['input']['post'])) {
        continue;
    }
    array_push($scripts, ['post' => $searchField['input']['post']]);
}

echo $this->element('Search.widget_libraries', ['scripts' => $scripts]);
?>
<?php endif;
