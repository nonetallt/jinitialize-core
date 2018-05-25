<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
    // define public methods as commands

    public function watchTests()
    {
        $this->taskWatch()
            ->monitor(['src', 'tests/Feature', 'tests/Unit', 'tests/Classes', 'stubs'], function() {
                echo 'test';
                $this->taskExec('phpunit')->run();
            })->run();
    }
}
