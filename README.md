Gravatar API
============

The goal of this package is to provide a simple object-oriented interface for working with Gravatar.

Simply call the method with the user's email and get back an object that describes all the data we can get about the user.

It is relatively easy to use:

```php
$gravatar = new Gravatar;

// Get icon URL - default icon size
$gravatar->getIcon('jan@barasek.com');

// small icon size
$gravatar->getIcon('jan@barasek.com', 32);

// big icon size
$gravatar->getIcon('jan@barasek.com', 255);

// Get user full info as GravatarResponse
$gravatar->getUserInfo('jan@barasek.com');
```
