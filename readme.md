# TODO

## jinitialize

* readme picture
* register commands from the local folder
* register procedures from the local folder


## jinitialize-core

* git plugin

* parse [ENV PLACEHOLDERS] from procedure.json to allow usage of settings and exported variables as arguments and options

* remove settings: use above instead, update doc

* [?] JinitializeCommand: executesOther method, lists commands that are ran by the
  command

* [REQUIRES ENV EDITOR] print placeholder setting list when running procedure -> .env interactive editor

* refactor most of required/recommend method to JinitializeCommand class, use in both
  procedure and command (check on execute)

* procedure: when plugin is not found, use composer search to suggest packages to install


## jinitialize-plugin

* DOC: reserved names: core, test, show list of others?

* move isset from UnitTest to some helper lib
* generate command stub command
* autogenerate procedures command?
* autogenerate doc command (autoschema)


# Planned procedures

* php-package
* laravel-local
* laravel-package
* laravel-remote


# Planned modules

* apache2
* laravel
* tool: git project (bitbucket and github)
* tool: laravel-routes
* tool: laravel-schema
* tool: gulp
* tool: robo
* tool: phpunit
* laravel mix setup
* npm new package installer
* phaser setup (mix loaders option if webpack.mix.js is found)
* babel boilerplate setup
* react boilerplate setup


# Tools
* site status checker with guzzle
* .env editor


## jinitialize-plugin-mysql

* refactor from main project
* check if database already exists before trying to create it
* check if user exists
* set db default collation
* set db default driver innoDB
