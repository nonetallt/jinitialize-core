# TODO

## jinitialize

* project structure
* git setup
* packagist setup
* every command should be wrapped in a procedure


## jinitialize-core

* make sure optional methods are public
* composerScripts: print suggested settings list
* procedure: print suggested settings list?
* use values from other modules when possible (display which module value comes from)
* exported settings
* exported procedures
* procedure factory
    * when plugin is not found, use composer search to suggest packages to install


## jinitialize-plugin

* git rename
* packagist rename (composer.json ?)
* testing documentation
* recommend documentation on imported and exported variables
* update jinitialize command documenation (plugin-new)


## jinitialize-plugin-project

* refactor composer.json
* project structure package (from create plugin structure)
* stub wrapper convert 'plugin name' to [PLUGIN_NAME]


## jinitialize-plugin-mysql

* refactor from main project
* check if database already exists before trying to create it
* check if user exists
* set db default collation
* set db default driver innoDB





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
