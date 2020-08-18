# REST-symfony5

Un ejemplo de un PHP REST API utilizando el framework Symfony 5.0

Para poder correrlo primero ejecutamos el siguiente para instalar las dependencias a traves de [composer](https://getcomposer.org/download/):

```sh
$ composer install
```

Luego debemos configurar los siguentes parametro en el archivo **.env**.

```
DATABASE_URL=mysql://username:password@127.0.0.1:3306/database_name?serverVersion=5.7
FOOTBALL_API_TOKEN=xxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```
El API Token se puede obtener de [https://www.football-data.org/](https://www.football-data.org/).

Creamos una base de datos y por último corremos los siguientes comandos para poder crear las tablas:

```sh
$ bin/console make:migration
$ bin/console doctrine:migrations:migrate
```

Si tenemos [Symfony](https://symfony.com/download) instalado podemos utlizar el siguiente comando para levantar la aplicacion.

```sh
$ symfony server:start
```

Pueden ver mas detalles aquí:

[API REST con Symfony5](https://medium.com/@valentinomantovani/api-rest-con-symfony5-9fdd26e3b7b1)
