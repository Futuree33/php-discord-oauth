# php-discord-oauth
A PHP discord oauth wrapper.


 ```php
// must require this file
require "./discord/autoload.php";
 
$discord = new discord\oauth(client_id: "", client_secret: "", redirect_uri: "", scopes: []);
 
// Header user to discord authorize and redirects to redirect uri with "code" param
$discord->authorize();
 
// get access_token and refresh_token
$discord->getTokens(code: "");
 
// get user object
$discord->getUser(access_token: "");
 
// refresh tokens using refresh_token 
$discord->getTokens(refresh_token: "");

// revoke tokens
$discord->revokeTokens(access_token: "");
```
