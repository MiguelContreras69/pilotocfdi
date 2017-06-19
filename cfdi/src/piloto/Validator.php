<?php

namespace Piloto;

use BadMethodCallException;
use Piloto\Exceptions\CfdiException;

/* Esta clase se encarga de validar los campos que se encuentran en las clases (objetos)
 * valida si son null, y el tipo mas que nada.
 */

class Validator {

    protected $data = [];
    protected $rules = [];

    public function __construct(array $data, array $rules) {
        $this->data = $data;
        $this->rules = $rules;
    }

    public function make() {
        foreach ($this->rules as $attribute => $rules) {
            $this->parseMethodWithRules($attribute, $rules);
        }
        return true;
    }

    private function parseMethodWithRules($attribute, $rules) {
        foreach (preg_split("/\|/", $rules) as $rule) {
            if (empty($rule)) {
                continue;
            }

            list($rule, $params) = $this->parseStringRule($rule);
            $value = $this->getValue($attribute);

            $method = "validate{$rule}";
            $this->{$method}($params, $attribute, $value);
        }
    }

    private function getValue($attribute) {
        if (isset($this->data[$attribute])) {
            return $this->data[$attribute];
        }

        return null;
    }

    private function parseStringRule($rules) {
        $parameters = [];

        if (strpos($rules, ':') !== false) {
            list($rules, $parameter) = explode(':', $rules, 2);
            $parameters = $this->parseParameters($parameter);
        }

        return [ucfirst($rules), $parameters];
    }

    private function validateKeys() {
        if (file_exists('xml/catCFDI.xsd')) {
            $xml = simplexml_load_file('xml/catCFDI.xsd');
            print_r($xml);
        } else {
            exit('Failed to open the XML archive.');
        }
    }

    private function parseParameters($parameter) {
        return str_getcsv($parameter);
    }

    private function validateRequired($params, $attribute, $value) {
        if (is_null($value)) {
            throw new CfdiException(sprintf("The '%s' is a required key from SAT Schema", $attribute));
        } elseif (is_string($value) && trim($value) === '') {
            throw new CfdiException(sprintf("The '%s' is a required key from SAT Schema", $attribute));
        } elseif ((is_array($value) || $value instanceof \Countable) && count($value) < 1) {
            throw new CfdiException(sprintf("The '%s' is a required key from SAT Schema", $attribute));
        } elseif (!array_key_exists($attribute, $this->data)) {
            throw new CfdiException(sprintf("The '%s' is a required key from SAT Schema", $attribute));
        }

        return true;
    }

    private function validateEnum($params, $attribute, $value) {
        if (!in_array($value, $params)) {
            throw new CfdiException(sprintf("The '%s' i´snt a valid value for '%s' field SAT Schema", $value, $attribute));
        }

        return true;
    }

    public function __call($method, $parameters) {
        if (is_callable($method)) {
            return true;
        }
        throw new BadMethodCallException("Method [$method] does not exist.");
    }

}
