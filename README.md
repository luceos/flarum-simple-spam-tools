# Local extenders to prevent spam

This extension adds some advanced protection against spam runs on your community. This is not an extension but a bundle of local extenders. Local extenders need to be added to your `extend.php` in the root of your Flarum installation (next to `flarum` and `composer.json` you will see a file `extend.php`).

Install the extension:

```
composer require luceos/flarum-simple-spam-tools
```

Update the extension:


```
composer require luceos/flarum-simple-spam-tools
```

Make sure to confirm all local extenders still work afterwards.

## Configuration

In your `extend.php` specify some settings which should speak for themselves:
```php
return [
    (new \Luceos\Spam\Filter)
        // use domain name
        ->allowLinksFromDomain('luceos.com')
        // or just a full domain with protocol, only the host name is used
        ->allowLinksFromDomain('http://flarum.org')
        // even a link works, only the domain will be used
        ->allowLinksFromDomain('discuss.flarum.org/d/26095')
        // Specify the number of hours after signup a user will be tested for bad content
        ->checkForUserUpToHoursSinceSignUp(5)
        // Specify the number of posts needed to be ignored for bad content testing
        ->checkForUserUpToPostContribution(5)
        // Specify the user Id of the moderator raising flags for some actions
        ->moderateAsUser(2),
];
```

### Prevent Bio Spam

```php
return [
    // ...
    new \Luceos\Spam\UserBio,
]
```

This will prevent any bad content etc based on the Filter settings from configuration.

### Prevent CommentPost Spam

```php
return [
    // ..
    new \Luceos\Spam\CommentPost,
]
```

This will prevent any bad content in posts based on the Filter settings.

### Prevent Discussion Subject Spam

```php
return [
    // ..
    new \Luceos\Spam\Discussion,
]
```

Prevents URL's in discussion subjects/titles.

### Example full configuration

This could be an example local `extend.php`:

```php
<?php

/*
 * This file is part of Flarum.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

return [
    //.. some other extenders can come here, the last one needs to end with a comma.
    
        (new \Luceos\Spam\Filter)
        ->allowLinksFromDomain('luceos.com')
        ->allowLinksFromDomain('http://flarum.org')
        ->allowLinksFromDomain('discuss.flarum.org/d/26095')
        ->checkForUserUpToHoursSinceSignUp(24)
        ->checkForUserUpToPostContribution(10)
        ->moderateAsUser(10),
    new \Luceos\Spam\UserBio,
    new \Luceos\Spam\CommentPost,
    new \Luceos\Spam\Discussion,
];
```
