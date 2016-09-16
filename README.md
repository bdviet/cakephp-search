# Search plugin for CakePHP

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

## Setup

Install plugin
```
composer require qobo/cakephp-search
```

Load plugin
```
bin/cake plugin load Search
```

Load required plugin(s)
```
bin/cake plugin load Muffin/Trash
```

Load Component

In your AppController add the following:
```php
    $this->loadComponent('Search.Searchable');
```
