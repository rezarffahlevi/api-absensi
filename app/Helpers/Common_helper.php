<?php

if (!function_exists('admin_url')) {
    /**
     * Return the base URL to use in views
     * 
     * @param string $title
     *
     * @return string
     * added by ilham 08.10.2020
     */
    function admin_url($uri = '', string $protocol = null): string
    {
        return base_url("admin/".$uri, $protocol);
    }
}

if (!function_exists('create_folder')) {
    /**
     * Return the base URL to use in views
     * 
     * @param string $dir
     *
     * @return string
     * added by ilham 03.11.2020
     */
    function create_folder($dir='')
    {
        $result = true;

        if($dir != '') {
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
        } else {
            $result = false;
        }

        return $result;
    }
}


if (!function_exists('format_date')) {
    /**
     * Return the base URL to use in views
     * 
     * @param string $datestr
     *
     * @return string
     * added by rahmat1929 11.12.2020
     */
    
    function format_date($datestr)
    {
        return date('d F Y', strtotime($datestr));
    }
}

if (!function_exists('format_currency')) {
    /**
     * Return the base URL to use in views
     * 
     * @param string $number
     *
     * @return string
     * added by rahmat1929 12.12.2020
     */
    
    function format_currency($number)
    {
        return "Rp. ". number_format($number, 2);
    }
}