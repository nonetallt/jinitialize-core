<?php

namespace Tests\Traits;

use Nonetallt\Jinitialize\Plugin\JinitializeCommand;

trait MocksCommands
{
    protected function getMockCommand(string $id)
    {
        $mock =  new class($id) extends JinitializeCommand {

            private $name;
            private $desc;

            public function __construct($id)
            {
                $this->name = "example$id";
                $this->desc = 'description';
                parent::__construct();
            }

            protected function configure()
            {
                $this ->setName($this->name) ->setDescription($this->desc);
            }

            protected function handle() {
                $file = MocksCommands::getOutputFile();
                file_put_contents($file, $this->getName(), FILE_APPEND);
                return true;
            }

            public function revert() {
                $file = MocksCommands::getOutputFile();
                $contents = file_get_contents($file);
                file_put_contents($file, str_replace($this->getName(), '', $contents));
            }
        };
        return $mock;
    }

    public static function cleanOutput()
    {
        $folder = self::getOutputFolder();

        $files = glob("$folder/*.out");

        foreach($files as $file) {
            unlink($file);
        }
    }

    public static function getOutputFolder()
    {
        return __DIR__ . '/../output';
    }

    public static function getOutputFile()
    {
        return self::getOutputFolder() . '/command.out';
    }
}
