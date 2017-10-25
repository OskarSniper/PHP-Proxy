#PHP-Proxy
##How it works

Go into proxy.php and edit $allowedRessources. Put all links in this array, due to security reasons.

Afterwards just replace all links with "http://{YOUR_URL}/proxy.php?u={YOUR_LINK}" if you want to enable cache, which is disabled by default just go to proxy.php and set $enableCache to true.

If you enabled cache you can use it just by adding "&c" to your link e.g.: "http://{YOUR_URL}/proxy.php?u={YOUR_LINK}&c"