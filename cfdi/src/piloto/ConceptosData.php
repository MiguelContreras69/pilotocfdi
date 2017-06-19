<?php

namespace Piloto;

class ConceptosData extends CfdiData {

    protected $ClaveProdServ; // nuevo
    protected $ClaveUnidad; // nuevo
    protected $NoIdentificacion; //  nuevo un autoincrementable de 5 cifras que
    //  al parecer actua como id del conepto anidado investigar si el campo es obligatorio
    protected $Cantidad;
    protected $Unidad;
    protected $Descripcion;
    protected $ValorUnitario;
    protected $Importe;

    public function __construct(array $data) {
        $this->parseData($data);
    }

    public function rules() {
        return [
            "ClaveProdServ" => 'required',
            "ClaveUnidad" => 'required',
            "NoIdentificacion" => '', // max 100
            "Cantidad" => 'required',
            "Unidad" => '',
            "Descripcion" => 'required', // max 1000
            "ValorUnitario" => 'required',
            "Importe" => 'required',
            "Descuento" => '',// en caso dado que existan descuentos en el concepto
        ];
    }

}
