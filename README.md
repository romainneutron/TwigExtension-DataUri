#DataURI Twig Extension

[![Build Status](https://secure.travis-ci.org/romainneutron/TwigExtension-DataUri.png?branch=master)](http://travis-ci.org/romainneutron/TwigExtension-DataUri)

This is an extension for [Twig Templating engine][1]

This extension makes easy to use the dataURI scheme as specified in [RFC 2397][2].

Be carefull, as explained in the RFC, it can not be used as a replacement for
traditionnal URI sources all the time. DataURI scheme is length limited and can
not handle all files / ressources.

##Install

This extension requires PHP 5.3.2.

[Download and Install composer][3], then add this to your ``composer.json``:

```json
{
    "require": {
        "data-uri/twig-extension": "dev-master",
        "data-uri/data-uri": "dev-master",
        "twig/extensions": "dev-master"
    }
}
```

Then, register the extension in your twig environment:

```php
$twig->addExtension(new \DataURI\TwigExtension());
```

##Usage

DataURI extension works with **stream ressource**, **scalar value**, **binary string**, or a **pathname** for a file.

###Pathname

```php
$twig->render('<img title="hello" src="{{ image | dataUri }}" />', array('image' => '/path/to/image.jpg'));
```

will render something like:

```html
<img title="hello" src="data:image/jpeg;base64,iVBORw0KGgoAAAANSUhEUgAAAB...SUhEU==" />
```

###Ressource

```php
$file = fopen('/path/to/image.jpg', 'r');
$twig->render('<img title="hello" src="{{ image | dataUri }}" />', array('image' => $file));
```

###Binary string

```php
$file = file_get_contents('/path/to/image.jpg');
$twig->render('<img title="hello" src="{{ image | dataUri(true, \'image/jpeg\') }}" />', array('image' => $file));
```

##Options

DataUri can take up to 3 parameters:

``dataUri(strictMode, mimeType, parameters)``

**strictMode** default value us `true`
**mimeType** default value is `null` (autodetected when passing a pathname)
**parameters** is an array of key/value parameters


###Unlock data length restriction

As said above, the RFC is quite strict on the output max length. By default, the
extension is 100% RFC compliant and the extension will log warnings if you render
data too large, but you can unlock the limit with the first option of the filter:

```php
$twig->render('<img title="hello" src="{{ image | dataUri(false) }}" />', array('image' => '/path/to/BIGPICTURE.jpg'));
```

**note**: If you display errors, warning message will result in Twig throws
Twig_Error_Runtime exception.

###Example Mimetype

```php
$file = fopen('bunny.png', 'r');
$twig->render("{{ file | dataUri(false, 'image/png') }}", array('file' => $file));
```

will render something like:

```
data:image/png;base64,oAYTUKHJKPPZ...F873=/SO
```

###Example Parameters

```php
$json = '{"Hello":"World !"}';
$twig->render( '{{ json | dataUri(false, "application/json", {"charset":"utf-8"}) }}', array('json' => $json));
```

will render:

```
data:application/json;charset=utf-8,%7B%22Hello%22%3A%22World%20%21%22%7D
```

##License

This extension is released under the MIT License

[1]: http://twig.sensiolabs.org/
[2]: https://www.ietf.org/rfc/rfc2397.txt
[3]: http://getcomposer.org/download/
