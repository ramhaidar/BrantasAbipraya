<?php

use Symfony\Component\Console\Output\ConsoleOutput;

if ( ! function_exists ( 'console' ) )
{
    function console ( $message )
    {
        $output = new ConsoleOutput();
        $output->writeln ( "___\n" );
        $output->writeln ( $message );
        $output->writeln ( "___\n" );
    }
}
