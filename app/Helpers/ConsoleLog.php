<?php

use Symfony\Component\Console\Output\ConsoleOutput;

if ( ! function_exists ( 'console' ) )
{
    function console ( $message )
    {
        $output = new ConsoleOutput();
        $output->writeln ( $message );
    }
}
