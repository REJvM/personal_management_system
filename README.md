# Personal website

## Setup project parameters

Change the parameters in the services file and remove ".dist" extention
.\config\services.yaml.dist

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