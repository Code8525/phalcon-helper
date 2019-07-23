<?php
declare(strict_types=1);

namespace Phalcon;

/**
 * Class ResponseJson
 */
class ApiResponse
{

    static private $jsonData = [
        "ok" => true
    ];

    /**
     * @param $name
     * @param $value
     */
    static public function set($name, $value)
    {
        self::$jsonData[$name] = $value;
    }

    /**
     * @param $name
     * @param $value
     */
    static public function setDataItem($name, $value)
    {
        if (isset(self::$jsonData["data"]) === false)
            self::$jsonData["data"] = [];
        self::$jsonData["data"][$name] = $value;
    }

    /**
     * @param $value
     */
    static public function setData($value)
    {
        self::$jsonData["data"] = $value;
    }

    /**
     * @param $value
     * @return array
     */
    static public function setResults($value)
    {
        self::$jsonData["results"] = $value;
        return self::$jsonData;
    }

    /**
     * @param $name
     * @param $value
     */
    static public function setResultsItem($name, $value)
    {
        if (!isset(self::$jsonData["results"]))
            self::$jsonData["results"] = [];
        self::$jsonData["results"][$name] = $value;
    }

    /**
     * @param string $value
     */
    static public function setMessage(string $value)
    {
        self::$jsonData["message"] = $value;
    }

    /**
     * @param string $value
     * @param string|null $message
     * @return array
     */
    static public function setError(string $value, string $message = null)
    {
        self::$jsonData["ok"] = false;
        self::$jsonData["error"] = $value;

        if (is_null($message) === false) {
            self::$jsonData["message"] = $message;
        }

        return self::$jsonData;
    }

    /**
     * @return string
     */
    static public function error(): string
    {
        return self::$jsonData["error"];
    }

    /**
     * @return array
     */
    static public function getData(): array
    {
        return self::$jsonData["data"] ?? [];
    }

    /**
     * @return array
     */
    static public function getAll(): array
    {
        return self::$jsonData;
    }

    /**
     * @param string $name
     * @return string|array
     */
    static public function get(string $name)
    {
        return self::$jsonData[$name] ?? "";
    }


}
