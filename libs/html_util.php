<?php

function js_redirect_to($url, $delay_ms=0)
{
    $path = explode("/", $_SERVER["REQUEST_URI"]);
    unset($path[count($path) - 1]);
    $path = implode("/", $path);

    echo "<script>setTimeout(function(){window.location='".$path."/".$url."'}, ".$delay.");</script>";
    die();
}

function js_redirect_back($delay_ms = null)
{
    if(!is_numeric($delay_ms)) $delay_ms = 0;
    
    echo '
        <script>
            setTimeout(function(){
                window.history.back();
            },'; echo $delay_ms; echo ');
        </script>
    ';
    die();
}

function js_alert($str)
{
    echo "<script>alert('".$str."');</script>";
}

// ob_start() must be called before this function. And ob_end_clean() must be called if there will be no more calls to this function.
function dynamic_echo($str)
{
    echo "<script class='__php_echo_rem_js'>try{document.querySelector('.__php_dyn_echo').remove();}catch(ex){}document.querySelector('.__php_echo_rem_js').remove();</script>";
    echo "<span class='__php_dyn_echo'>".$str."</span>";
    
    ob_end_flush(); 
    ob_flush(); 
    flush(); 
    ob_start(); 
}

?>
