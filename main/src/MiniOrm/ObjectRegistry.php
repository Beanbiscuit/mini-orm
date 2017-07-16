<?php

namespace MiniOrm;

/**
 * This trait enables any implementing class to be used as a singleton.
 * 
 * @author Bruce Silver <beanbiscuit@gmail.com>
 */
trait ObjectRegistry
{

    /**
     * 
     */
    private static $oInstance;

    /**
     * Returns a single instance of the implementing class.
     * 
     * @return \self
     */
    public static function getInstance()
    {

        if (is_null(self::$oInstance)) {

            self::$oInstance = new self();
        }

        return self::$oInstance;
    }

    /**
     * Returns a new instance of the implementing class.
     * 
     * @return \self
     */
    public static function newInstance()
    {

        self::$oInstance = new self();
        return self::$oInstance;
    }

}
