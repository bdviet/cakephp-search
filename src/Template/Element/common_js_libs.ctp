<?php
echo $this->Html->css('Search.datatables.min', ['block' => 'cssBottom']);
echo $this->Html->script('Search.datatables.min', ['block' => 'scriptBottom']);
echo $this->Html->script('https://d3js.org/d3.v4.min.js', ['block' => 'scriptBottom']);

echo $this->Html->script('Search.view-search-result', ['block' => 'scriptBottom']);
echo $this->Html->scriptBlock(
    'view_search_result.init({
        table_id: \'.table-datatable\'
    });',
    ['block' => 'scriptBottom']
);
