#DataURI Twig Extension

[![Build Status](https://secure.travis-ci.org/romainneutron/TwigExtension-DataUri.png?branch=master)](http://travis-ci.org/romainneutron/TwigExtension-DataUri)

This extension makes it easy to use the dataURI scheme as specified in RFC 2397
(see https://www.ietf.org/rfc/rfc2397.txt).

Be carefull, as explained in the RFC, it can not use as a replacement for
traditionnal sources. dataURI scheme is length limited and can not handle all
files / ressources.

##Install

This extension requires PHP 5.3.2.

Download composer following instructions at http://getcomposer.org/, then add
this to your ``composer.json`` :

```json

    "require": {
        "data-uri/twig-extension": "dev-master"
    },

```

##Usage

In the following example, image can be either a stream ressource, a scalar value,
a binary string, or a pathname for a file.

```php

$twig->addExtension(new \DataURI\TwigExtension());

$twig->render('<img title="hello" src="{{ image | dataUri }}" />', array('image' => '/path/to/image.jpg');

```

will render something like :


```html

<img title="hello" src="data:image/jpeg;base64,iVBORw0KGgoAAAANSUhEUgAAAB...SUhEU==" />

```


##License

This extension is released under the MIT License
