<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Receiver implements FromCollection,WithHeadings,WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $receiver;
    public function __construct($receiver)
    {
        $this->receiver = $receiver;
    }

    public function headings(): array
    {
        return [
            'Name',
            'Phone',
            'Address', 
        ];
    }
    
    public function collection()
    {
        return $this->receiver;
    }

    public function styles(Worksheet $sheet)
    {
        return [ 
           1    => ['font' => ['bold' => true]],
        ];
    }

}
