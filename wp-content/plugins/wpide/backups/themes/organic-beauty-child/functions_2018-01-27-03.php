<?php /* start WPide restore code */
                                    if ($_POST["restorewpnonce"] === "88aa95d660284829a70544f4ddbbd5dbb15a79907e"){
                                        if ( file_put_contents ( "/var/www/html/scent.fantasylab.no/wp-content/themes/organic-beauty-child/functions.php" ,  preg_replace("#<\?php /\* start WPide(.*)end WPide restore code \*/ \?>#s", "", file_get_contents("/var/www/html/scent.fantasylab.no/wp-content/plugins/wpide/backups/themes/organic-beauty-child/functions_2018-01-27-03.php") )  ) ){
                                            echo "Your file has been restored, overwritting the recently edited file! \n\n The active editor still contains the broken or unwanted code. If you no longer need that content then close the tab and start fresh with the restored file.";
                                        }
                                    }else{
                                        echo "-1";
                                    }
                                    die();
                            /* end WPide restore code */ ?><?php
/**
 * Child-Theme functions and definitions
 */

function henry_megamenu_javascript_localisation( $args ) {
    $args['timeout'] = 0;
    return $args;
}
add_filter( 'megamenu_javascript_localisation', 'henry_megamenu_javascript_localisation', 10 );
