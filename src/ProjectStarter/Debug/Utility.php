<?php
namespace ProjectStarter\Debug;

    /**
     * Function for debug
     *
     * Display all recived parameters with
     * good formating in Browser and exit after
     *
     * @param mixed
     */
    function dump()
    {
        $p = func_get_args();
        foreach ($p as $arg) {
            dumpAlive($arg);
        }
        exit(0);
    }

    /**
     * @see dump()
     * no exit at the end
     */
    function dumpAlive()
    {
        echo "<pre>";

        $i = 1;
        $args = func_get_args();
        array_walk_recursive($args, function(&$item, $key){
            if ($item === "") {
                $item = '""';
            } elseif ($item === true) {
                $item = 'true (boolean)';
            } elseif  ($item === false) {
                $item = 'false (boolean)';
            } elseif (is_null($item)) {
                $item = 'NULL';
            }
        });

        foreach ($args as $arg) {
            echo "VAR ".$i++."\n";
            print_r($arg);
            echo "\n\n";
        }
        echo "</pre>";
    }