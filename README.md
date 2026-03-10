Likes Extended
==============

![Elgg 6.3](https://img.shields.io/badge/Elgg-6.3-green.svg)
![Lint Checks](https://github.com/ColdTrick/likes_extended/actions/workflows/lint.yml/badge.svg?event=push)
[![Latest Stable Version](https://poser.pugx.org/coldtrick/likes_extended/v/stable.svg)](https://packagist.org/packages/coldtrick/likes_extended)
[![License](https://poser.pugx.org/coldtrick/likes_extended/license.svg)](https://packagist.org/packages/coldtrick/likes_extended)

Offer more Likes options

Extending Like options
----------------------

Register a callback to the event ```'likes:subtypes', 'likes_extended'```. The return must be an array:

```php
elgg_register_event_handler('likes:subtypes', 'likes_extended', function (\Elgg\Event $event): array {
	$result = $event->getValue();
	
	// my custom like
	$result['my_like_type'] = [
		'icon' => 'cheers',
	];

	return $result;
}
```

Also add the following language keys:

- ``likes_extended:<my_like_type>:menu:add`` used in the social menu as title when adding your like type
- ``likes_extended:<my_like_type>:menu:remove`` used in the social menu as title when removing your like type
- ``likes_extended:<my_like_type>:tab`` used when viewing the likes details to generate a tab text
- ``likes_extended:<my_like_type>:annotation`` used when viewing the likes details as a annotation title
- ``likes_extended:<my_like_type>:action:success`` used as a success message after clicking your like type
- ``likes_extended:<my_like_type>:notification:subject`` used as the notification subject for your like type
- ``likes_extended:<my_like_type>:notification:summary`` used as the notification summary for your like type
- ``likes_extended:<my_like_type>:notification:body`` used as the notification body for your like type
