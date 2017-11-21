## Job interview assignment
The following describes how to run and use the solution for CEGO job interview assignment.

### Getting started
Before cloning the repository make sure the following dependencies are installed.

* PHP 7
* php-sqlite3
* sqlite3
* composer

Clone this repository

```
git clone https://github.com/cjonstrup/assignment.git
```

Install the project dependencies via composer

```
composer install
```

Run the unit tests to verify the system is working

```
./vendor/bin/phpunit
```

Run the app and the test command for yourself

```
php app user 'select * from users limit 10' users.csv
```

### How it works
1. A query is read from the cmd line and executed on the database.
2. Before the query is executed its is checked for bad sql etc. "delete from users" and we are querying the correct table.
3. Query is read to memory and then checked for the availability of the "id" column (which will be needed for deletion later on).
4. Rows are written to file in CSV format (semi-colon separated) with first row as column names. File is  <output.csv> If writing error should fail, a cleanup will be attempted and <output.csv> will be deleted.

### Discussion
1. Min. fields must be defined, I only check for "Id" because it is used for deletion.
2. Before deleting from database, exported csv file should be loaded and verified against the select query.

### Notes
* This has been tested on my mac, other platforms with php7 and sqlite3 should work fine.