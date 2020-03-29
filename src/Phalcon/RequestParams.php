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
    static private $validation = null;

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
     * @return Validation
     */
    static public function getValidation()
    {
        if (self::$validation === null)
            self::$validation = new \Phalcon\Validation();
        return self::$validation;
    }


    /**
     * @param string $name
     * @return string
     */
    static public function getString(string $name): string
    {
        $val = self::get($name);
        return (string)(self::getFilter()->sanitize($val, 'string'));
    }

    /**
     * @param string $name |null
     * @return array
     */
    static public function getArray(string $name): ?array
    {
        $arr = self::get($name);
        if (gettype($arr) === 'array') {
            return $arr;
        }
        return null;
    }


    /**
     * @param string|null $name
     * @param string|null $value
     * @return string|null
     */
    static public function getPhone(string $name = null, string $value = null): ?string
    {

        $value = $value ?? self::getString($name);
        return (string)preg_replace('/[^0-9]/', '', $value);
    }

    static public function getStringValidateLength(string $name, int $max, int $min = 0, bool $required = false): string
    {
        $validation = self::getValidation();

        $validation->add($name, new Validation\Validator\StringLength([
            'max' => $max,
            'min' => $min,
            'messageMaximum' => $name . '_max_length_invalid',
            'messageMinimum' => $name . '_min_length_invalid'
        ]));

        if ($required)
            $validation->add($name, new Validation\Validator\PresenceOf([
                'message' => $name . '_is_required'
            ]));

        self::validate();

        return self::getString($name);
    }

    static public function validate(): void
    {
        $validation = self::getValidation();
        $messages = $validation->validate(self::getJson());
        foreach ($messages as $message) {
            throw new ApiException($message->getMessage());
        }
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
