<?php
echo $this->Html->css('Search.datatables.min', ['block' => 'cssBottom']);
echo $this->Html->script('Search.datatables.min', ['block' => 'scriptBottom']);
echo $this->Html->script('Search.view-search-result', ['block' => 'scriptBottom']);
echo $this->Html->scriptBlock(
    'view_search_result.init({
        table_id: \'.table-datatable\',
        sort_by_field: \'' . array_search($this->request->data('sort_by_field'), $listingFields) . '\',
        sort_by_order: \'' . $this->request->data('sort_by_order') . '\'
    });',
    ['block' => 'scriptBottom']
);