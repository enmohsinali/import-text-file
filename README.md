# L1 Challenge Devbox

  

## Summary

  

- Dockerfile & Docker-compose setup with PHP8.1 and MySQL

- Symfony 5.4 installation with a /healthz endpoint and a test for it

- After the image is started the app will run on port 9002 on localhost. You can try the existing

endpoint: http://localhost:9002/healthz

- The default database is called `database` and the username and password are `root` and `root`

respectively

- Makefile with some basic commands

  

## Installation

  

```

make run && make install

```

  

## Run commands inside the container

  

```

make enter

```

  

## Run tests

  

```

make test

```

## Import log file
In order to import your own log file please replace it with **.src/AppBundle/Data/logs.txt**
name of the file must be **logs.txt** make make sure the path is correct.
Then run the command 
```

make enter

bin/console app:import-logs

```
Follow the instructions if you already have imported the log file before.

## Making request

URL : [http://localhost:9002/count](http://localhost:9002/count)
Parameters as follows
|  Parameter | DataType  | Example | Description |
|--|--|--|--|
|serviceNames[]|array|USER-SERVICE|array of service names|
|startDate|dateTime|2020-12-26 17:19:04|only in the format of Y-m-d h:i:s|
|endDate|dateTime|2020-12-26 17:19:04|only in the format of Y-m-d h:i:s|
|statusCode|integer|201|filter on request status code|

**Example:** 
http://localhost:9002/count?serviceNames[]=USER-SERVICE&serviceNames[]=INVOICE-SERVICE&startDate=2020-12-26%2017:19:04&endDate=2022-12-26%2017:19:04&statusCode=201
