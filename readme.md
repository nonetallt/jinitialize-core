#TODO

* generic
    * generic php library module
    * project structure package (from create plugin structure)
    * stub wrapper convert 'plugin name' to [PLUGIN_NAME]

* procedure
    * create container that allows storing of values from earlier commands
    * on failure, all executed commands should be reverted, command class can
    revert all subclasses in its revert method
    * every command should be wrapped in a procedure


* db module
    * check if database already exists before trying to create it
    * check if user exists
    * set db default collation
    * set db default driver innoDB



* use values from other modules when possible (display which module value comes
  from)


#MODULES

* new laravel project installer
* apache2 local
* existing laravel project installer
* tool: git project (bitbucket and github)
* tool: tags
* tool: laravel-routes
* tool: laravel-schema
* tool: gulp
* tool: robo
* tool: phpunit
* laravel mix setup
* laravel new package installer
* php new package installer
* npm new package installer
* phaser setup (mix loaders option if webpack.mix.js is found)
* babel boilerplate setup
* react boilerplate setup


#TOOLS
* site status checker with guzzle
* .env editor

#GLOBAL .ENV
* site directory
* author name
* author email
* preferred psr
* preferred stability
* preferred license
