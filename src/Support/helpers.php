<?php

use smartQQ\Core\App;

if (!function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param string $abstract
     *
     * @return mixed
     */
    function app($abstract = null)
    {
        if (is_null($abstract)) {
            return App::getInstance();
        }

        return (App::getInstance())->$abstract;
    }
}

if (!function_exists('credential_store')) {
    /**
     * Get the available container instance.
     *
     * @param array $abstract
     *
     * @return mixed
     */
    function credential_store(array $data)
    {
        if ($data) {
            $str = "<?php\r\n";
            $str .= "\$config = array(\r\n";
            foreach ($data as $name => $value) {
                $str .= '\''.$name . '\'=>\'' . $value . "',\r\n";
            }
            $str .= ")\r\n";
            file_put_contents(app('config')['credential_file'], $str);
        }

    }
}

if (!function_exists('p')) {

    function p($data)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}

