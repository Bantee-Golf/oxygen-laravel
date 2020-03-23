<?php


namespace Tests\Console;


use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
    }

    public function call($command, array $parameters = [], $outputBuffer = null)
    {


    }
}