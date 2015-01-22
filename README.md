Go Pro with Doctrine Queries
============================

Well hi there! This repository holds the code and script
for the KnpUniversity lesson called:

[Go Pro with Doctrine Queries](http://knpuniversity.com/screencast/doctrine-queries)

Project Setup:

1) Configure `parameters.yml`:

Copy `app/config/parameters.yml.dist` to `app/config/parameters.yml` and
configure any of the `database_` options.

2) [Download Composer](https://getcomposer.org/)

3) Install the vendor libraries

```
php composer.phar install
```

4) Create your database and load some fixtures!

```
php app/console doctrine:database:create
php app/console doctrine:schema:update --force
php app/console doctrine:fixtures:load
```

5) Start the web server:

```
php app/console server:run
```

6) Go to `http://localhost:8000` and query for your fortune!
