<?php



function function1($test1 = null)
{
    try {
        function2();
        if (empty($test1)) {
            throw new Exception("Test exception 1\n");
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}


function function2($test2 = null)
{
    try {
        if (empty($test2)) {
            throw new Exception("Test exception 2\n");
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

function1();
