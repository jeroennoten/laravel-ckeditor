# Easy CKEditor integration with Laravel 5

[![Build Status](https://travis-ci.org/jeroennoten/laravel-ckeditor.svg?branch=master)](https://travis-ci.org/jeroennoten/laravel-ckeditor)

This package provides an easy way to set up [CKEditor](http://ckeditor.com/) with Laravel 5.
I think CKEditor is the best free WYSIWYG editors available.
This package makes it super easy to use the editor with Laravel 5.
It provides a custom blade directive `@ckeditor('textareaId')` to quickly integrate it in your forms.

- [Installation](#installation)
- [Updating](#updating)
- [Usage](#usage)
- [Configuration](#configuration)

## Installation

1. Require the package using composer:

    ```
    composer require jeroennoten/laravel-ckeditor
    ```

2. Add the service provider to the `providers` in `config/app.php`:

    ```php
    JeroenNoten\LaravelCkEditor\ServiceProvider::class,
    ```

3. Publish the public assets:

    ```
    php artisan vendor:publish --tag=ckeditor-assets
    ```

## Updating

1. To update this package, first update the composer package:

    ```
    composer update jeroennoten/laravel-ckeditor
    ```

2. Then, publish the public assets with the `--force` flag to overwrite existing files

    ```
    php artisan vendor:publish --tag=ckeditor-assets --force
    ```

## Usage

The package provides a custom blade directive `@ckeditor('textareaId')` that transforms a `<textarea>` into a CkEditor instance.
Give your `<textarea>` an `id` attribute and add the blade directive at the bottom of your page, with the identifier of the `<textarea>`.

Example:

```html
<textarea id="bodyField"></textarea>

@ckeditor('bodyField')
```

## Configuration

If you need to configure the CkEditor instance, you can do that by passing a second argument with all options into the blade directive.
Refer to the [CkEditor config documentation](http://docs.ckeditor.com/#!/api/CKEDITOR.config) to discover all possible options.

Example: 

```html
<textarea id="bodyField"></textarea>

@ckeditor('bodyField', ['height' => 500])
```
