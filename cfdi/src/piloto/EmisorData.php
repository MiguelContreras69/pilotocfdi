<?php namespace Piloto;

class EmisorData extends CfdiData
{
    protected $Rfc;

    protected $Nombre;

    protected $RegimenFiscal;

    public function __construct(array $data)
    {
        $this->parseData($data);
    }

    public function rules()
    {
        return [
            "Rfc" => '',
            "Nombre" => '',
            "RegimenFiscal" => '',
        ];
    }
}
