<?php
echo $this->Html->css('Search.datatables.min', ['block' => 'cssBottom']);
echo $this->Html->script('Search.datatables.min', ['block' => 'scriptBottom']);
echo $this->Html->script('Search.view-search-result', ['block' => 'scriptBottom']);

echo $this->element('Search.saved_searches');
echo $this->element('Search.search_filters');
echo $this->element('Search.search_results');
