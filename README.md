# WP Validator

Validate and sanitize form inputs and API requests with a PHP library inspired by Laravel, designed specifically for WordPress.

[![Latest Stable Version](https://poser.pugx.org/bitapps/wp-validator/v/stable)](https://packagist.org/packages/bitapps/wp-validator) [![Total Downloads](https://poser.pugx.org/bitapps/wp-validator/downloads)](https://packagist.org/packages/bitapps/wp-validator) [![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)

## Overview

WP Validator is a comprehensive PHP package inspired by Laravel. It simplifies the process of data validation and sanitization for WordPress, providing a versatile and user-friendly solution for developers to ensure that user input meets specific criteria and is secure against common vulnerabilities.

## Features

- **Data Validation:** Easily validate user inputs, form submissions, and API requests.
- **Custom Validation Rules:** Define your custom validation rules to meet your application's specific needs.
- **Error Messages:** Detailed error messages to assist users in understanding validation failures.
- **Data Sanitization:** Optional data sanitization functions for cleaning and formatting data.

## Example Usage

To use the wp-validator package for data validation in your PHP application, follow these steps:

### 1. Install the Package

Begin by installing the WP Validator package using Composer:

```bash
composer require bitapps/wp-validator
```

### 2. Initialize the Validator

Create an instance of the Validator class from the package:

```php
use BitApps\WPValidator\Validator;

$validator = new Validator;
```

For public methods that can be used with `$validator`, refer to the [Validator Instance Methods](#validator-instance-methods) section.

### 3. Define Your Data and Validation Rules

Prepare the data you want to validate and define the validation rules. Here's an example:

```php
$data = [
    'first_name' => 'John',
    'last_name' => '',
    'email' => 'email@example',
    'password' => '##112233',
    'confirm_password' => '##112233',
];

$rules = [
    'first_name' => ['required', 'string'],
    'last_name' => ['required', 'string'],
    'email' => ['required', 'email'],
    'password' => ['required', 'min:8'],
    'confirm_password' => ['required', 'min:6', 'same:password'],
];
```

Explore all available validation rules and their usage in the [Available Validation Rules](#available-validation-rules) section.

### 4. Customize Error Messages (Optional)

If you need to customize error messages, you can use the `$customMessages` array. In this example, we leave it empty.

```php
$customMessages = [];
```

Learn more about customizing error messages in the [Customizing Error Messages](#customizing-error-messages) section.

### 5. Map Attribute Names (Optional)

Map your field names to user-friendly labels using the `$attributes` array, these labels will be used for error messages.

```php
$attributes = [
    'first_name' => 'First Name',
    'last_name' => 'Last Name',
    'email' => 'Email',
];
```

### 6. Perform Validation

Execute the validation using the `make` method:

```php
$validation = $validator->make($data, $rules, $customMessages, $attributes);
```

### 7. Handle Validation Results

Check if validation fails and, if so, print out the validation errors:

```php
if ($validation->fails()) {
    echo "<pre>";
    echo print_r($validation->errors(), true);
    echo "</pre>";
} else {
    echo "Success!";
}
```

### Validator Instance Methods

#### `make($data: array, $rules: array[, $customMessages?: array, $attributes?: array])`

This method runs the validations of `$data` based on given `$rules`. Optionally, if you pass `$customMessages` and `$attributes`, it will make the error messages (*if any*) based on that.

#### `fails(): boolean`

This method will return true or false based on the validation status. If it returns true, that means the validator has found errors in data, and you can get those errors by the `errors()` method.

#### `errors(): array`

This method will return the error messages (*if any*) based on the format of the passed `$data` array in the `make()` method.

### Available Validation Rules

WP Validator provides a comprehensive set of validation rules to suit your needs. Here's a list of available rules:

1. **`accepted`**<br/>
Checks if the field under validation is one of the following: `'yes'`, `'on'`, `'1'`, `1`, `'true'`, `true`. This is useful for validating agreement type fields.
2. **`array`**<br/>
Checks if the field under validation is an array.
3. **`between:min,max`**<br/>
Checks if the field under validation falls within the range of `:min` and `:max` (inclusive).
    - For string data, the value corresponds to the number of characters.
    - For numeric data, the value corresponds to a given integer value.
    - For an array, the value corresponds to the count of the array.
4. **`date`**<br/>
Checks if the field under validation is a valid date according to the `strtotime` PHP function.
5. **`digit_between:min,max`**<br/>
Checks if the length of digits for the integer number falls within the range of `:min` and `:max` (inclusive).
6. **`digits:value`**<br/>
Checks if the length of digits for the integer number is exactly the same as `:digits`.
7. **`email`**<br/>
Checks if the field under validation is a valid email address.
8. **`integer`**<br/>
Checks if the field under validation is an integer number.
9. **`ip`**<br/>
Checks if the field under validation is a valid IP (IPv4, IPv6) address.
10. **`ipv4`**<br/>
Checks if the field under validation is a valid IPv4 address.
11. **`ipv6`**<br/>
Checks if the field under validation is a valid IPv6 address.
12. **`json`**<br/>
Checks if the field under validation is a valid JSON string.
13. **`lowercase`**<br/>
Checks if the field under validation consists of all lowercase letters.
14. **`mac_address`**<br/>
Checks if the field under validation is a valid MAC address.
15. **`max:value`**<br/>
Checks if the field under validation is less than or equal to `:max`.
    - For string data, the value corresponds to the number of characters.
    - For numeric data, the value corresponds to a given integer value.
    - For an array, the value corresponds to the count of the array.
16. **`min:value`**<br/>
Checks if the field under validation has a minimum value of `:min`.
    - For string data, the value corresponds to the number of characters.
    - For numeric data, the value corresponds to a given integer value.
    - For an array, the value corresponds to the count of the array.
17. **`nullable`**<br/>
Makes the field under validation as optional (allows to be null), but respects other validation rules if specified and value is not null.
18. **`numeric`**<br/>
Checks if the field under validation is a valid real number.
19. **`required`**<br/>
Checks if the field under validation is present and not empty. A field is "empty" if it meets one of the following criteria:
    - The value is `NULL` or `FALSE`.
    - The value is an empty string.
    - The value is an empty array or empty countable object.

20. **`present`**<br/>
The field must be present in the input data for validation.
21. **`same:field`**<br/>
Checks if the field under validation is equal to the specified `:other` attribute.
22. **`size:value`**<br/>
Checks if the field under validation has exactly the same size as `:size`.
    - For string data, the value corresponds to the number of characters.
    - For numeric data, the value corresponds to a given integer value.
    - For an array, the value corresponds to the count of the array.
23. **`string`**<br/>
Checks if the given value is a string.
24. **`uppercase`**<br/>
Checks if the string value consists of all uppercase letters.
25. **`url`**<br/>
Checks if the value is a valid URL.

Missing any validation rule that you need? Refer to the [Custom Validation Rule](#custom-validation-rule) section to know how you can create and use custom validation rules in your project alongside the library.

### Available sanitization functions
1. **`sanitize_email`**<br/>
Strip out all characters that are not allowable in an email address.<br/>
e.g. `['email' => ['required', 'email', 'sanitize:email']`

2. **`sanitize_file_name`**<br/>
Sanitizes a file name by removing special characters.<br/>
e.g `['file' => ['required', 'string', 'sanitize:file_name']`

3. **`sanitize_html_class`**<br/>
Sanitize content with allowed HTML tags for class attribute.<br/>
e.g `['class' => ['required', 'string', 'sanitize:html_class']`

4. **`sanitize_key`**<br/>
Sanitize content with allowed HTML tags for key attribute.<br/>
e.g `['key' => ['required', 'string', 'sanitize:sanitize_key']`

5. **`sanitize_text`**<br/>
Strip out all characters that are not allowable in a string.<br/>
e.g. `['name' => ['required', 'string', 'sanitize:text']`

6. **`sanitize_textarea_field`**<br/>
Sanitize content with allowed HTML tags for textarea field.<br/>
e.g `['content' => ['required', 'string', 'sanitize:textarea']`

7. **`sanitize_title`**<br/>
Strip out all characters that are not allowable in a title.<br/>
e.g. `['title' => ['required', 'string', 'sanitize:title']`

8. **`sanitize_user`**<br/>
Sanitize a username, striping out unsafe characters.<br/>
e.g `['user' => ['required', 'string', 'sanitize:user']`

9. **`sanitize_url`**<br/>
Sanitizes a URL by removing invalid characters for safe use in HTML attributes.<br/>
e.g. `['url' => ['required', 'url', 'sanitize:url']`

10. **`wp_kses`**<br/>
Sanitize content with allowed HTML tags.<br/>
e.g `['content' => ['required', 'string', 'sanitize:wp_kses|a.href,a.title,br,em,strong']`

11. **`wp_kses_post`**<br/>
Sanitize content with allowed HTML tags for post content.<br/>
e.g `['content' => ['required', 'string', 'sanitize:wp_kses_post']`


### Custom Validation Rule

Create the class of the validation rule into your project:

```php
<?php

use BitApps\WPValidator\Rule;

class BooleanRule extends Rule
{
    // error message if fails...
    private $message = "The :attribute must be a boolean";

    public function validate($value)
    {
        // validation code here...
        return is_bool($value);
    }

    public function message()
    {
        return $this->message;
    }
}
```

Pass them as an instance into the `$rules` array:

```php
$rules = [
    'agreed' => ['required', new BooleanRule],
];
```
### Customizing Error Messages

WP Validator provides default error messages based on validation rules. For added flexibility, you can change these error messages globally or even for specific fields and validation rules:

```php
$customMessages = [
    'required' => ':attribute is missing',
    'string' => ':attribute cannot contain any numerics',
    'between' => 'The :attribute must be given between :min & :max',
    'size' => 'The account number must consist of :size characters',
];
```
Now, for each validation rule, it will return the custom error messages you have set.
**Note:** `:attribute` refers to the field it's currently validating, acting as a placeholder. We have more placeholders like this; explore them in the [List of Placeholders](#list-of-placeholders) section.

If you want more flexibility and wish to customize error messages individually for each validation rule and field, you can also achieve that:

```php
$customMessages = [
    'first_name' => [
        'required' => 'First name must be present',
        'string' => 'You cannot include anything except letters in the first name',
    ],
    'email' => [
        'email' => 'The provided email is not valid',
    ],
];
```

If you use any other validation rules that you didn't mention in the Custom Messages array, WP Validator will follow the default error message.

### List of Placeholders

1. **`:attribute`**<br/>
It will refer to the field name under validation & custom label if changed via `$attributes` array.
2. **`:value`**<br/>
It will refer to the value of the field under validation.
3. **`:min`**<br/>
It will refer to the min value parameter of `between`, `digits_between`, `min` validation rules.
4. **`:max`**<br/>
It will refer to the max value parameter of `between`, `digits_between`, `max` validation rules.
5. **`:digits`**<br/>
It will refer to the value parameter of `digits` validation rule.
6. **`:other`**<br/>
It will refer to the field parameter of `same` validation rule.
7. **`:size`**<br/>
It will refer to the value parameter of `size` validation rule.

# Contributing

We welcome contributions from the community. If you find a bug or have a feature suggestion, please open an issue or submit a pull request.

# License

This package is open-source and available under the MIT License.
