# PHP-Proxy
## How it works

1. Open proxy.php
2. Edit $allowedRessources. Put all links in this array, due to security reasons.
3. Replace all links with "http://{YOUR_URL}/proxy.php?u={YOUR_LINK}".
4. Done!

### Enable cache
1. Open proxy.php
2. Edit $allowedRessources. Put all links in this array, due to security reasons.
3. Change $cacheEnable from "false" to "true" ( default: false )
4. Change $cacheDirectory to absolute path ( default: /var/www/cache )
5. Change $refreshCacheTime to number in seconds ( default: 300 )
6. You can use it just by adding "&c" to your link e.g.: "http://{YOUR_URL}/proxy.php?u={YOUR_LINK}&c"
7. Done!

## Parameters
Parameter | Explaination
------------ | -------------
u | URL
c | Cache (false by default)

## Common errors
### Errorcode: "Cachepath not read/writeable"
Make sure the path your cache should be written on is read/writeable.

### Errorcode: "URL invalid"
Make sure your given URL is valid.

### Every url is accessable
If $allowedRessources is empty. Every URL is allowed ;-)