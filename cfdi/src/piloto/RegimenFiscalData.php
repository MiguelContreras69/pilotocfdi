<?php
namespace Piloto;

class RegimenFiscalData extends CfdiData
{
    protected $Regimen;

    public function __construct(array $data)
    {
        $this->parseData($data);
    }

    public function rules()
    {
        return [
            "Regimen" => '',
        ];
    }
}
