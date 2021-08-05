Gravatar API
============

The goal of this package is to provide a simple object-oriented interface for working with Gravatar.

Simply call the method with the user's email and get back an object that describes all the data we can get about the user.

It is relatively easy to use:

```php
$gravatar = new Gravatar;

// Get icon URL
$gravatar->getIcon('jan@barasek.com');

// Get user full info as GravatarResponse
$gravatar->getUserInfo('jan@barasek.com');
```
