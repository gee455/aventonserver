<?php
/*
 * function that generate the action buttons edit, delete
 * This is just showing the idea you can use it in different view or whatever fits your needs
 */
function get_lat($id, $latlong) {

        $ci = & get_instance();
        return $latlong[$id][0]; //$latlong[$id.'lat'];
    }

    function get_log($id, $latlong) {
        $ci = & get_instance();
        return $latlong[$id][1];
    }