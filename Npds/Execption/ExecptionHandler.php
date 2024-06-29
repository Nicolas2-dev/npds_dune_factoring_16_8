<?php

namespace Npds\Execption;

use Exception;
use Throwable;
use ErrorException;
use Npds\Execption\FatalThrowableError;


/**
 * ExecptionHandler class
 */
class ExecptionHandler
{

    /**
     * L’instance actuelle du gestionnaire.
     */
    protected static $instance;

    /**
     * Créez une nouvelle instance de gestionnaire d'exceptions.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Amorcez le gestionnaire d’exceptions.
     *
     * @return void
     */
    public static function initialize()
    {
        static::$instance = $instance = new static();

        // Configurez les gestionnaires d’exceptions.
        set_error_handler(array($instance, 'handleError'));

        set_exception_handler(array($instance, 'handleException'));

        register_shutdown_function(array($instance, 'handleShutdown'));
    }

    /**
     * Convertissez une erreur PHP en ErrorException.
     *
     * @param  int  $level
     * @param  string  $message
     * @param  string  $file
     * @param  int  $line
     * @param  array  $context
     * @return void
     *
     * @throws \ErrorException
     */
    public function handleError($level, $message, $file = '', $line = 0, $context = array())
    {
        if (error_reporting() & ($level > 0)) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Gérez une exception non interceptée de l’application.
     *
     * @param  \Throwable  $e
     * @return void
     */
    public function handleException($e)
    {
        if (! $e instanceof Exception) {
            $e = new FatalThrowableError($e);
        }

        // on inscrit l'erreur dans le journal slogs/npds_error.log
        $this->report($e);

        // on retourne l'erreur entre le header et footer
        $this->render($e);
    }

    /**
     * Signalez ou enregistrez une exception.
     *
     * @param  Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        $message = $e->getMessage();

        $code = $e->getCode();
        $file = $e->getFile();
        $line = $e->getLine();

        $trace = $e->getTraceAsString();

        $date = date('M d, Y G:iA');

        $message = "Exception information:\n
    Date: {$date}\n
    Message: {$message}\n
    Code: {$code}\n
    File: {$file}\n
    Line: {$line}\n
    Stack trace:\n
{$trace}\n
---------\n\n";

        //
        $path = 'storage/error/npds.log';

        file_put_contents($path, $message, FILE_APPEND);
    }

    /**
     * Renvoyez une exception.
     *
     * @param  Exception  $e
     * @return void
     */
    public function render(Exception $e)
    {   
        $this->error_debug($e);
    }

    /**
     * [debug description]
     *
     * @param   Exception  $e
     *
     * @return  void
     */
    public function error_debug(Exception  $e) 
    {
        global $pdst;

        $pdst = 1;
        include("header.php");

        echo '<div class="col-lg-6">
            <h1>Npds Whoops!</h1>
        </div>
        
        <div class=col-lg-6">
            <p>
                '.$e->getMessage() .' in '. $e->getFile() .' on line '. $e->getLine() .'
            </p>
            <br>
            <pre>'.  $e->getTraceAsString() .'</pre>
        </div>';

        include("footer.php"); 
    }

    /**
     * Gérez l'événement d'arrêt de PHP.
     *
     * @return void
     */
    public function handleShutdown()
    {
        if (! is_null($error = error_get_last()) && $this->isFatal($error['type'])) {
            $this->handleException($this->fatalExceptionFromError($error));
        }
    }

    /**
     * Créez une nouvelle instance d'exception fatale à partir d'un tableau d'erreurs.
     *
     * @param  array  $error
     * @param  int|null  $traceOffset
     * @return \ErrorException
     */
    protected function fatalExceptionFromError(array $error)
    {
        return new ErrorException(
            $error['message'], $error['type'], 0, $error['file'], $error['line']
        );
    }

    /**
     * Déterminez si le type d’erreur est fatal.
     *
     * @param  int  $type
     * @return bool
     */
    protected function isFatal($type)
    {
        return in_array($type, array(E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE));
    }

}