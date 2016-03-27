
## Meterpost

Meterpost is a web application for monitoring the quantities stored as data series. Meterpost receives readings from 
instruments and stores them in the database. Example views are provided to visualize the readings over time. The 
readings are stored as text but the example views assume that they can be converted to numbers.   

### Configuration

* Clone the repository
* Create a database and make a note of the database name, database user and his password
* Copy the file `env.php.example` as `.env.php` in the meterpost root directory and edit it. Replace the strings 
  surrounded in square brackets with actual values.
* Run `composer install`. If you do not have composer available, please refer to the [composer documentation](https://getcomposer.org)
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
* run `composer dump-autoload` to be able to access the classes in the new directory

To create tables using migrations, run
`php artisan migrate`

To create sample data for demo/testing, run 
`php artisan db:seed`

To display all the routes this app provides, run
`php artisan routes`

### Technology

Meterpost is built with PHP 5 & Laravel 4.2 (to support PHP 5.4). Tested with MySQL database, probably needs work
in order to make it work with PostgreSQL.

### Future development ideas

* User registration and authentication
* Admin UI for managing dataseries and API keys
