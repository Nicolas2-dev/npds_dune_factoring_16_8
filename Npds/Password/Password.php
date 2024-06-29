<?php

namespace Npds\Password;

use Npds\Contracts\Password\PasswordInterface;


/**
 * Password class
 */
class Password implements PasswordInterface
{
    /**
     * [$instance description]
     *
     * @var [type]
     */
    protected static $instance;

    /**
     * [ALGO_CRYPT description]
     *
     * @var [type]
     */
    protected const ALGO_CRYPT = PASSWORD_BCRYPT;

    /**
     * [MIN_MS description]
     *
     * @var [type]
     */
    protected const MIN_MS = 100;


    /**
     * [getInstance description]
     *
     * @return  [type]  [return description]
     */
    public static function getInstance()
    {
        if (isset(static::$instance)) {
            return static::$instance;
        }

        return static::$instance = new static();
    }

    /**
     * permet de calculer le coût algorythmique optimum pour la procédure de hashage ($AlgoCrypt) d'un mot de pass ($pass) avec un temps minimum alloué ($min_ms)
     *
     * @param   [type]  $pass       [$pass description]
     * @param   [type]  $AlgoCrypt  [$AlgoCrypt description]
     * @param   [type]  $min_ms     [$min_ms description]
     *
     * @return  [type]              [return description]
     */
    public static function getOptimalBcryptCostParameter($pass, $min_ms = null)
    {
        for ($i = 8; $i < 13; $i++) {
            //
            $calculCost = [
                'cost' => $i
            ];

            $time_start = microtime(true);

            password_hash($pass, static::ALGO_CRYPT, $calculCost);

            $time_end = microtime(true);

            //
            if (is_null($min_ms)) {
                $min_ms = static::MIN_MS;
            }

            if (($time_end - $time_start) * 1000 > $min_ms) {
                return $i;
            }
        }
    }

    /**
     * [password_crypt description]
     *
     * @param   [type]  $pwd  [$pwd description]
     *
     * @return  [type]        [return description]
     */
    public function password_crypt($pwd)
    {
        //
        $options = [
            'cost' => static::getOptimalBcryptCostParameter($pwd)
        ];

        $hashpass = password_hash($pwd, static::ALGO_CRYPT, $options);
        
        return crypt($pwd, $hashpass);
    }

}