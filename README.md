# Personal website

## Installing and starting the project

Install missing assets
```
symfony console importmap:install
```

Create the database
```
symfony console doctrine:migrations:migrate
```

Fill database with dummy data:
```
symfony console doctrine:fixtures:load
```

and start the symfony server
```
symfony serve
```