<?php

namespace App\Services;


class Slug
{

    /**
     * Create a slug from a string
     *
     * @param string $name The name
     * @return string The name converted to slug
     */
    public static function createSlug($name)
    {
        $name = htmlentities( $name, ENT_NOQUOTES, 'utf-8' );
        $name = preg_replace( '#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $name );
        $name = preg_replace( '#&([A-za-z]{2})(?:lig);#', '\1', $name );
        $name = preg_replace( '#([^A-za-z0-9])#', '-', $name );
        $name = strtolower($name);
        $name = trim($name);        
        return $name;       
    }

}
