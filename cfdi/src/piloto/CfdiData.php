<?php

namespace Piloto;

use Piloto\Exceptions\CfdiException;
use Piloto\Validator;

abstract class CfdiData {
// agrega los valores al cfdi data
    protected function add($property, $value) {
        $this->{$property} = $value;
       
    }
// valida y verifica las propiedades del objeto validas estan incluidas
    protected function parseData(array $data) {
        //print_r($data);
        //print_r($this->rules());
        $validator = new Validator($data, $this->rules());
        $validator->make();

        foreach ($data as $property => $value) {
            $this->verifyValidProperty($property);
            $this->add($property, $value);
        }
        
        
        return true;
    }

    protected function verifyValidProperty($property) {
        if (!array_key_exists($property, $this->rules())) {
            throw new CfdiException(sprintf("The '%s' isnt a valid property from SAT Schema", $property));
        }
       
    }

    public function __get($property) {
        return $this->{$property};
    }

    public function getData() {
        return get_object_vars($this);
    }

    abstract public function rules();
}
