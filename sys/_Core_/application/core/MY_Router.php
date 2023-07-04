<?php
/*
======================================================================
Project Name    : Mishima T.CMS  
 
Copyright © <2016> Teruhiko Mishima All rights reserved.
 
This source code or any portion thereof must not be  
reproduced or used in any manner whatsoever.
本ソースコードを無断で転用・転載することを禁じます。
======================================================================
*/
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Extending Router Class
 */
class MY_Router extends CI_Router {

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Set default controller
     *
     * @return    void
     */
    protected function _set_default_controller()
    {
        if (empty($this->default_controller))
        {
            show_error('Unable to determine what should be displayed. A default route has not been specified in the routing file.');
        }

        // Is the method being specified?
        if (!sscanf($this->default_controller, '%[^/]/%[^/]/%s', $directory, $class, $method) !== 2)
        {
            $method = 'index';
        }

        if ( ! file_exists(APPPATH.'controllers'.DIRECTORY_SEPARATOR.$directory.DIRECTORY_SEPARATOR.ucfirst($class).'.php'))
        {
            // This will trigger 404 later
            return;
        }

        $this->set_directory($directory);
        $this->set_class($class);
        $this->set_method($method);

        // Assign routed segments, index starting from 1
        $this->uri->rsegments = array(
            1 => $class,
            2 => $method
        );

        log_message('debug', 'No URI present. Default controller set.');
    }

}
