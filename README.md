# Canvas API via LTI (Starter)

This is a starter template for projects that want to…

  1. Authenticate users' identities
  2. Access the Canvas APIs (either to provide information to the users or _as_ the users)
  3. Embed the presentation layer of the project back into Canvas

This came about as a result of Hack Night at InstructureCon 2015, when it became clear to me that desire to both authenticate
users _and_ access the API using OAuth-provided tokens was more than could be done through simple OAuth authentication. (Well,
it _can_ be done, but it generates two OAuth tokens in the user's account and causes them to think that they are being logged
in twice: once to authenticate their identity (which can be persistent -- "check to remember this login") and once to generate
an API key. This seemed confusing, at best.)

### Usage

1. Start by [forking this repository](https://help.github.com/articles/fork-a-repo/).
2. Load your fork on to your favorite LAMP server (ideally including SSL-authentication -- Canvas plays nicer that way,
  and it's just plain more secure).
2. Be sure to run `composer install` ([Composer rocks](https://getcomposer.org/)). 
3. Point your browser at https://<install-url>/admin/ and you will run the install script. You will need to have your MysQL
4. credentials handy, as well as your Canvas developer credentials. Answer whatever questions it asks.
  (This will generate a secrets.xml file for you, if you don't want to make one yourself, based on
  [secrets-example.xml](https://github.com/smtech/starter-canvas-api-via-lti/blob/master/secrets-example.xml).)
4. Modify the CanvasAPIviaLTI class as needed -- most of your app logic can just go into
  [app.php](https://github.com/smtech/starter-canvas-api-via-lti/blob/master/app.php), which is loaded after a user has been
  authenticated (via [launch.php](https://github.com/smtech/starter-canvas-api-via-lti/blob/master/launch.php)).
5. Including [common.inc.php](https://github.com/smtech/starter-canvas-api-via-lti/blob/master/common.inc.php) will provide
  access to several handy global variables:
  1. `SimpleXMLElement $secrets`, the secrets.xml file.
  2. `mysqli $sql`, a database connection to your MySQL server.
  3. `AppMetadata $metadata`, an associative array bound to the app_metadata table in your database.
  4. `LTI_Tool_Provider $toolProvider`, representing the LTI information in your database.