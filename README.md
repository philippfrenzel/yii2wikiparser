yii2wikiparser
==============

Yii2 Creole Wiki Parser

Intro
=====

With the help of this class you can parse TEXT to HTML-Markup

Installation
============

Package is although registered at packagist.org - so you can just add one line of code, to let it run!

```json
"require": {
        "yiisoft/yii2": "dev-master",
        "yiisoft/yii2-composer":"dev-master",
        "philippfrenzel/yii2wikiparser":"*"
},
```

Usage
=====

```php

$WikiHtml = new yii2wikiparser();
$WikiHtml->parse($TEXT,array());

```

As it's ported from an historical class, the next step will be to substitute the "old" tag generators with the new
ones delivered by yii2. I'm waiting for the latest yii2 release so I can upgrade!
