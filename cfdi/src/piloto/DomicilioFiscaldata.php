<?php namespace Piloto;

class DomicilioFiscalData extends CfdiData
{
    protected $Calle;

    protected $NoExterior;

    protected $NoInterior;

    protected $Colonia;

    protected $Municipio;

    protected $Estado;

    protected $Pais;

    protected $CodigoPostal;

    public function __construct(array $data)
    {
        $this->parseData($data);
    }

    public function rules()
    {
        return [
            "Calle" => 'required',
            "NoExterior" => 'required',
            "Colonia" => 'required',
            "Municipio" => 'required',
            "Estado" => 'required',
            "Pais" => 'required',
            "CodigoPostal" => 'required',
        ];
    }
}
