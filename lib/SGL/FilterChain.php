<?php

/**
 * Manages an array of filters.
 *
 * @package SGL
 * @author  Demian Turner <demian@phpkitchen.com>
 */
class SGL_FilterChain
{
    var $aFilters;

    function __construct($aFilters)
    {
        $this->aFilters = array_map('trim', $aFilters);
    }

    function doFilter($input, $output)
    {
        $this->loadFilters();

        $filters = '';
        $closeParens = '';

        $code = '$process = ';
        foreach ($this->aFilters as $filter) {
            if (class_exists($filter)) {
                $filters .= "new $filter(\n";
                $closeParens .= ')';
            }
        }
        $code = $filters . $closeParens;
        eval("\$process = $code;");

        $process->process($input, $output);
    }

    function loadFilters()
    {
        foreach ($this->aFilters as $filter) {
            if (!class_exists($filter)) {
                echo $path = trim(preg_replace('/_/', '/', $filter)) . '.php';exit
                require_once $path;
            }
        }
    }
}
?>