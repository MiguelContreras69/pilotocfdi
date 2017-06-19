<?php

namespace Piloto;

class TrasladoData extends CfdiData {

    protected $Base;
    protected $Impuesto;
    protected $TipoFactor;
    protected $TasaOCuota;
    protected $Importe;

    public function __construct(array $data) {
        $this->parseData($data);
    }

    public function rules() {
        return [
            "Base" => 'required', // numero entero
            "Impuesto" => 'required', // numero de impuesto son 3 pero deben de llevar 4 ceros antes del impuesto ,
            "TipoFactor" => 'required', // ver si este valor es requerido
            "TasaOCuota" => 'required',
            "Importe" => 'required',
        ];
    }

}
