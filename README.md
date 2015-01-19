# Imagify
Laravel package for images manipulation, built on top of imagine


# Installation

To install through composer, simply put the following in your composer.json file:

```
{
    "require": {
        "skmail/imagify": "dev-development"
    }
}
```
And  run composer install from the terminal.


# Configuration

Add 'Skmail\Imagify\ImagifyServiceProvider' service provider in app/config/app.php.

```
'providers' => array(

    // ...

    'Skmail\Imagify\ImagifyServiceProvider',
),
```

Add Imagify alias in app/config/app.php.

```
'aliases' => array(

    // ...

    'Imagify' => 'Skmail\Imagify\Facades\Imagify'
),
```
# Usage

Here an example how to crop or resize an image:

### Crop

```
<img src="{{ Imagify::crop('image/image.jpg',200,200) }}"/>
```

### Resize
```
<img src="{{ Imagify::resize('image/image.jpg',200,200) }}"/>
```
