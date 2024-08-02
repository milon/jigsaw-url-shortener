# jigsaw-url-shortener

![banner](/jigsaw-url-shortener.png)

## Getting Started

This can be used to host several link if you already have a jigsaw powered site. Let's say you have various social links and you want a easy way to share all of those. Rather than remember all the different username and url patterns or these different website, you can now have a consistent format.

For example, you have a twitter, linkedin and facebook page. Each of them has different url pattern, but now you can use a common pattern. For instance, if my website's url is `http://milon.im`, now I can have `http://milon.im/twitter`, `http://milon.im/facebook` and `http://milon.im/linkedin` and these will redirect to the origin website.

#### Requirements

- PHP 8.1 or higher
- Jigsaw 1.7 or higher

#### Installation

```
composer require milon/jigsaw-url-shortener
```

## Setup and Usage

First you need to create a new entry in config file named `urlRedirects`.

```
[
    ...,
    'urlRedirects' => [
        [
            'filename' => 'twitter',
            'url' => 'https://twitter.com/to_milon',
        ],
        [
            'filename' => 'github',
            'url' => 'https://github.com/milon',
        ],
    ],
    ...
]
```

#### Alternative way

For easier readability, I recommend export this config to a separate file. You can create a file named `redirects.php` in the root folder, which should look like this-

```
<?php

return [
    [
        'filename' => 'twitter',
        'url' => 'https://twitter.com/to_milon',
    ],
    [
        'filename' => 'github',
        'url' => 'https://github.com/milon',
    ],
];
```

And the config file should look like this-

```
[
    ...,
    'urlRedirects' => require_once(__DIR__ . '/redirects.php'),
    ...
]
```

Then in the `bootstarp.php` file, register a new listener to `afterBuild` event.

```
$events->afterBuild(Milon\JigsawUrlShortener\GenerateUrlRedirect::class);
```

Now, build the site like normal.

## License

This package is published under `MIT` license.

## Author

- [Nuruzzaman Milon](https://milon.im)

Feel free to email me, if you have any question.