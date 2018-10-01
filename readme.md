# AppBasis
  - backend - ReactPHP Modular server base
  	  - configs, controllers, guards, models, modules, library
      - core -> main functionality
      - configs -> config
      - controllers -> controller logic
      - guards -> secure controllers when they need extra conditioning
      - models -> handle database queries
      - library -> extra functions
  
  - frontend
    - Simple Angular 6 base.
    - Nice Api Structure
    - WebSocket
    
## Road Map
  - todo

## Goals
  - todo
  

[![IMAGE ALT TEXT HERE](https://img.youtube.com/vi/aMkoqsEBoVc/0.jpg)](https://www.youtube.com/watch?v=aMkoqsEBoVc)

## TODO
#### Server:
  -  add: log to file with rotate.
  -  think: of adding base for classes to handle destruct
  -  avoid: this config database, its show protected values when using `debugJson` 
  -  add: switch for server to using allocated memory 
  -  add: switch for favor memory over speed or else
  -  avoid: all core classes should not have direct creation of classes, you `auxiliary` instead.
  -  think: add base class for all core classes 
  -  add: should be some class that will handle service issue that only it self can call __construct
  -  change: check captcha should be async.
  -  avoid: try to avoid as much as possible try and catch. 
  -  check: core\server.php function runProc there is `sleep` in dev its reduce 100% cpu
  -  add: unique email on database tbl: users
  -  issue: security when u have successfully login you delete all `bad` attempts 
  -  re-construct: all config(s) better names.
  -  check-add: check at `controllers/welcome` method `updates` 
    - add method for async remote requests
    - add cache (check-if-good: https://github.com/reactphp/cache)    
  -  re-construct: `core/container` add methods
  -  optimize: logger
  -  change: Config Service logs are not understand able.
  -  check: search for all `mixed` word in project, and be smart.
  -  think: functions like getBlockStatus in model can be part of proc in mysql but its depends on many things so just let you know. 
  -  ack: u load the controller each time, maybe it possible to config it to be loaded one time, depends on the user.
  -  add: in database, to handle created_at and updated_at                                https://medium.com/@bengarvey/use-an-updated-at-column-in-your-mysql-table-and-make-it-update-automatically-6bf010873e6a
  -  add: security `wrk -t4 -c500 -d10s http://localhost:51190/authorization/login/czf.leo123@gmail.com/badpass`
  
#### Client:
  -  logger should be better, its should better demonstrate  client architecture
  -  handle in nice way, situation when server is offline
  -  in api `clients` add socket.ts for live connection
  -  chat at main page.
  -  bug: at register when u click `reset` and then click `submit`, see!

#### Both:
  -  add switch for debug\production mode
  -  add mechanism of regeneration auth tokens 
  -  add debug level
  -  send all backend log to frontend component
