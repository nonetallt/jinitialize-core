# TODO

## jinitialize

* move jinitialize file from core
* project structure
* git setup
* packagist setup
* every command should be wrapped in a procedure
* register procedures from the local folder


## jinitialize-core

* recommendsExecuting
* print suggested settings list
    * on composerScripts
    * on application start
    * .env interactive editor

* use values from other modules when possible ()
* procedure
    * when plugin is not found, use composer search to suggest packages to install


## jinitialize-plugin

* DOC: runProcedure, runCommandAsProcedure
* DOC: recommend documentation on imported and exported variables
* DOC: recommend display which module value comes from when using imported values
* autogenerate procedures command?


## jinitialize-plugin-project

* refactor composer.json (namespace?)
* project structure package (from create plugin structure)
* stub wrapper convert 'plugin name' to [PLUGIN_NAME]


Refactor
* TestCase
* Procedure

NEEDED FOR php project
* project
* git
* shell




# Planned modules

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


# Tools
* site status checker with guzzle
* .env editor

#GLOBAL .ENV
* site directory
* author name
* author email
* preferred psr
* preferred stability
* preferred license


## jinitialize-plugin-mysql

* refactor from main project
* check if database already exists before trying to create it
* check if user exists
* set db default collation
* set db default driver innoDB

