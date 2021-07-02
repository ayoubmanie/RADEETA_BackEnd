<?php



function run()
{
    recursive(0);

    echo ' - end';
}
function recursive($i)
{
    $i++;
    if ($i != 10) {

        recursive($i);
    } else {
        echo 'yes i = 10';
        return;
    }
}

run();