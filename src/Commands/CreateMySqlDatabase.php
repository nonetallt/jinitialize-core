<?php
namespace Nonetallt\Jinitialize\Commands;

class CreateMySqlDatabase extends RevertableCommand
{
    protected $name = 'mysql';
    protected $description = 'Create a new mysql database.';

    protected function handle()
    {
        $this->loginToMySql();       
        $this->createDatabase();
        $this->createUser();
        return $this->validateSuccess();
    }

    private function loginToMySql()
    {
        $this->section('Login to mysql:');
        $loginInvalid = true;

        while($loginInvalid)
        {
            $this->askFor('username', env('DB_USER'));

            /* Ask user if they want to use .env value */
            if(! $this->confirmEnv('password', 'DB_PASSWORD')) {

                /* Ask for password if env value is not confirmed */
                $this->askForHidden('password');
            }

            $loginInvalid  = ! $this->validateLogin();

            if($loginInvalid) {
                $this->getIo()->warning('Invalid login credentials');
            }
        }
    }

    private function createDatabase()
    {
        $this->section('Creating a new mysql database:');
        $this->askFor('dbname', 'testi');
        

        $sql  = "create database $this->dbname;";
        $command = "mysql --user=$this->username --password=$this->password -e \"$sql\"";

        shell_exec($command);

        /* $this->failureMessage = [ */
        /*     "Could not create a new database called '{$this->dbname}'", */
        /*     $command */
        /* ]; */
    }

    private function createUser()
    {
        $this->section('Creating a user for the new database:');

        $this->askFor('dbuser', "{$this->dbname}_admin");
        $this->askFor('dbpassword', 'testi1234');
        /* $this->askForHidden('dbpassword'); */

        $sql  = "create user '$this->dbuser'@'localhost' identified by '$this->dbpassword';";
        $sql .= "grant all privileges on $this->dbname.* to '$this->dbuser'@'localhost'";
        $command = "mysql --user=$this->username --password=$this->password -e \"$sql\"";
        shell_exec($command);
    }

    protected function revert()
    {
        $sql = "drop database $this->dbname;";
        $sql .= "drop user '$this->dbuser'@'localhost'";

        $msg = "Reverting command, are you sure you want to execute the following command: $sql";
        $confirm = $this->getIo()->confirm($msg, false);

        if(! $confirm) return;

        $command = "mysql --user=$this->username --password=$this->password -e \"$sql\"";
        shell_exec($command);
    }

    private function validateLogin()
    {
        try {
            $conn = new \PDO("mysql:host=localhost", $this->username, $this->password);
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return true;

        } catch(\Exception $e) {
            return false;
        }
    }

    private function validateSuccess()
    {
        try {
            $conn = new \PDO("mysql:host=localhost; dbname={$this->dbname}", $this->dbuser, $this->dbpassword);
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return true;

        } catch(\Exception $e) {
            return false;
        }
    }
}
