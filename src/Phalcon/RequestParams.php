<?php
declare(strict_types=1);

namespace Phalcon;

/**
 * Class Request
 */
class RequestParams
{
    static private $json_data = [];
    static private $filter = null;

    /**
     * @return array
     */
    static public function getJson(): array
    {
        if (count(self::$json_data) === 0)
            self::$json_data = json_decode(file_get_contents("php://input"), true) ?? [];
        return self::$json_data;
    }

    /**
     * @return \Phalcon\Filter
     */
    static public function getFilter()
    {
        if (self::$filter === null)
            self::$filter = new \Phalcon\Filter();
        return self::$filter;
    }

    /**
     * @param string $name
     * @return string
     */
    static public function getString(string $name): string
    {
        $val = self::get($name);
        // var_dump($val);
        return (string)(self::getFilter()->sanitize($val, 'string'));
    }

    /**
     * @param string $name
     * @return string
     */
    static public function getAlphaNum(string $name): string
    {
        $val = self::get($name);
        // var_dump($val);
        return (string)(self::getFilter()->sanitize($val, 'alphanum'));
    }

    /**
     * 'Y' => array('year', '\d{4}'),
     * 'y' => array('year', '\d{2}'),
     * 'm' => array('month', '\d{2}'),
     * 'n' => array('month', '\d{1,2}'),
     * 'M' => array('month', '[A-Z][a-z]{3}'),
     * 'F' => array('month', '[A-Z][a-z]{2,8}'),
     * 'd' => array('day', '\d{2}'),
     * 'j' => array('day', '\d{1,2}'),
     * 'D' => array('day', '[A-Z][a-z]{2}'),
     * 'l' => array('day', '[A-Z][a-z]{6,9}'),
     * 'u' => array('hour', '\d{1,6}'),
     * 'h' => array('hour', '\d{2}'),
     * 'H' => array('hour', '\d{2}'),
     * 'g' => array('hour', '\d{1,2}'),
     * 'G' => array('hour', '\d{1,2}'),
     * 'i' => array('minute', '\d{2}'),
     * 's' => array('second', '\d{2}')
     *
     * d.m.Y H:i:s
     *
     * @param string $name
     * @param string $format
     * @return \DateTime|null
     */
    static public function getDate(string $name, string $format = 'd.m.Y'): ?\DateTime
    {
        $date = \DateTime::createFromFormat($format, self::get($name));
        if ($date === false)
            return null;
        return $date;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    static public function get(string $name)
    {
        $val = null;
        if (isset($_REQUEST[$name]))
            $val = $_REQUEST[$name];
        else if (isset(self::getJson()[$name]))
            $val = self::getJson()[$name];
        if ($val === null)
            return $val;
        return $val;
    }

    /**
     * @param string $name
     * @return int|null
     */
    static public function getInit(string $name): ?int
    {
        $val = self::get($name);
        if ($val === null)
            return null;
        $val = self::getFilter()->sanitize($val, 'int');
        if ($val === '')
            return null;
        return (int)$val;
    }

    /**
     * @param string $name
     * @return int
     */
    static public function getInitNoNull(string $name): int
    {
        return (int)self::getInit($name);
    }
}
