<<<<<<< HEAD
NITM Widgets
============
Widgets created for Ninjas in the Machine

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist nitm/yii2-widgets "*"
```

or add

```
"nitm/yii2-widgets": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed enable the module:

```php
'nitm-widgets' => [
    'class' => '/nitm/widgets/Module'
]
<?=

Then simply use it in your code by  :

```php
<?= \nitm\widgets\<group>\<widget>::widget(); ?>```
=======
yii2-nitm-widgets
=================

```php
<?=

	/**
	 * NITM reply widget routes
	 */
	'<controller:(reply|alerts|vote|issue)/<action>' => 'nitm-widgets/<controller>/<action>',
	'<controller:(reply|alerts|vote|issue)/<action>/<type>' => 'nitm-widgets/<controller>/<action>',
	'<controller:(reply|alerts|vote|issue)/<action>/<type></id>' => 'nitm-widgets/<controller>/<action>',

?>```