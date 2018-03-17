# AppBasis
  - Base for Angular client and PHP server.

[![IMAGE ALT TEXT HERE](https://img.youtube.com/vi/aMkoqsEBoVc/0.jpg)](https://www.youtube.com/watch?v=aMkoqsEBoVc)

# Install
```sh
$ git clone https://github.com/iNewLegend/AppBasis.git
```
### Server
```sh
$ composer update
$ php server.php
```
### Client
```sh
$ ng build
$ cd dist
$ php -S localhost:8080
```


## TODO
#### Server:
  -  if server is non active for long time, the MySQL Connection is gone away.
  -  -  Option #1: make keepAlive method
  -  -  Option #2: something like DBConnectionPool
  -  add log to file with rotate.
  
#### Client:
  -  logger should be better, its should better explain about client architecture
  -  handle in nice way situation when server is offline
  -  in api clients add socket.ts for live connection

#### Both:
  -  add switch for debug\production mode
  -  add mechanism of regeneration auth tokens 
