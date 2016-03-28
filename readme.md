
## Meterpost

Meterpost is a web application for monitoring the quantities stored as data series. Meterpost receives readings from 
instruments and stores them in the database. Example views are provided to visualize the readings over time. The 
readings are stored as text but the example views assume that they can be converted to numbers.   

### Configuration

* Clone the repository
* Create a database and make a note of the database name, database user and his password
* Copy the file `env.php.example` as `.env.php` in the meterpost root directory and edit it. Replace the strings 
  surrounded in square brackets with actual values.
* Run `php composer.phar install`. If you do not have composer available, please refer to the [composer documentation](https://getcomposer.org)
* Run `php artisan migrate` to create the required tables
* There is no admin features yet so use your favorite SQL tool to insert the dataseries into the database. Don't forget
  to specify an API key for each dataseries.

### Posting readings

Program your thing to post the readings to your server. You need to provide the ID of the dataseries (which you created
before), the API key (which you specified when you created your dataseries) and the reading value.

Here's a pseudo-code example in jQuery:

```javascript
$.post('https://www.example.com/meterpost/dataseries/1/reading', 
        {'api_key': 'MyVerySecretKey1', 'value': '3.14' },
        function(res) { console.log(res); });
```


### Useful notes

When adding a new directory containing classes into the project:

* add the directory in composer.json -> autoload / classmap
* run `php composer dump-autoload` to be able to access the classes in the new directory

To create tables using migrations, run
```
$ php artisan migrate
```

To create sample data for demo/testing, run 
```
$ php artisan db:seed
```

To display all the routes this app provides, run
```
$ php artisan routes
```

When installing to a live server under a subdirectory, I found these 
[instructions](https://github.com/petehouston/laravel-deploy-on-shared-hosting) very helpful. The gist of it is that 
you do not put the whole project directory under the www root but cleverly link a directory from your www root back 
to the cloned meterpost repo directory somewhere else. So instead of trying to break your head with hellish Apache 
mod_rewrite directives, just

* create an empty directory under your www root (for example 'meterpost')
* under your repo clone dir, move the public directory out of the way: `$ mv public public_bak`
* create a (symbolic) link called 'public' into your repo clone dir to point to the new empty directory under your www root, for example
  `$ ln -s /var/www/userhome/sites/www.mysite.com/www/meterpost public`
* under your repo clone dir, copy everything (including .htaccess) from public_bak to public (which now points to
  a directory under your web server's www root)
* under your repo clone dir, edit public/index.php and add a backtrack from the ACTUAL www root subdirectory to your 
  repo clone dir boostrap directory (notice that you are actually editing the file /var/www/userhome/sites/www.mysite.com/www/meterpost/index.php)
  * there are two places in the index.php which you need to edit, the require for autoload.php and the require start.php
* finally, allow the web server/php interpreter to write to the storage directory (under your repo clone dir): 'chmod -R o+w storage'

### Technology

Meterpost is built with PHP 5 & Laravel 4.2 (to support PHP 5.4). Tested with MySQL database, probably needs work
in order to make it work with PostgreSQL.

### Future development ideas

* User registration and authentication
* Admin UI for managing dataseries and API keys
