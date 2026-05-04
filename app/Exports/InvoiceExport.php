<?php

namespace App\Exports;

use App\Http\Resources\InvoiceParcelResource;
use App\Models\Backend\Merchant;
use App\Models\Backend\Merchantpanel\Invoice;
use App\Models\MerchantShops;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Style\Color;

class InvoiceExport implements FromCollection, WithHeadings, WithEvents, WithStyles, WithDrawings, WithColumnWidths
{

    /**
     * @return \Illuminate\Support\Collection
     */
    protected $invoice;
    public function __construct($invoice)
    { 
        $this->invoice = $invoice; 
    }
    public function headings(): array
    { 

       return [
            'Delivery Date',
            'Customer Info',  
            'Tracking ID', 
            'Status', 
            'Total delivery charge',
            'Paid out'
       ];
  
    }
    public function collection()
    {
        return InvoiceParcelResource::collection($this->invoice->invoiceParcels);
    }

    public function registerEvents(): array
    { 
       
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                    
                $merchant = Merchant::find($this->invoice->merchant_id);  
                $event->sheet->appendRows(array(
                    [' ', ' ',' ']
                ), $event);
                 
                //header
                $event->sheet->setCellValue('D1', 'Merchant Name:'); 
                $event->sheet->setCellValue('D2', 'Date:'); //invoice date

                $event->sheet->setCellValue('E1', @$merchant->business_name); //merchant name 
                $event->sheet->setCellValue('E2', $this->invoice->invoice_date); //date 

                //merge  
                $event->sheet->mergeCells('E1:F1'); 
                $event->sheet->mergeCells('E2:F2');
                //header
                $event->sheet->setCellValue('A5', ' '); //merchant name
                $event->sheet->setAutoFilter('A6:F10');

                $event->sheet->mergeCells('A1:C4');

            },
            // Array callable, refering to a static method.
            AfterSheet::class => function (AfterSheet $event) {

                // total calculation
                $total_rows = $event->sheet->getHighestRow();
                $last_row = $total_rows + 1;
                $event->sheet->setCellValue('A' . $last_row, 'Total='); 
                $event->sheet->setCellValue('E' . $last_row, '=SUM(E2:E' . $total_rows . ')'); //total piad out //amount
                $event->sheet->setCellValue('F' . $last_row, '=SUM(F2:F' . $total_rows . ')'); //total delivery charges / rtd fees
                $event->sheet->getStyle($last_row)->applyFromArray([
                    'font' => ['bold' => true]
                ]);

                $event->sheet->mergeCells('A'.$last_row.':D'.$last_row);
           
                //end total calculation 

                // net amount and rtd fees 
                $f_h_row = $last_row + 1; 
                // end net amount and rtd fees

              
                // signature rows
                    $a_sig_row = $f_h_row + 3; 
                    $event->sheet->setCellValue('A' . $a_sig_row, 'Accounting Signature:'); 
                    $event->sheet->mergeCells('A'.$a_sig_row.':B'.$a_sig_row);  
                    $event->sheet->getStyle('A'.$a_sig_row.':B'.$a_sig_row)->applyFromArray([ 
                        'font' => ['bold' => true]
                    ]); 
 
                    $m_sig_row = $f_h_row + 3;
                    $event->sheet->setCellValue('E' . $m_sig_row, 'Merchant Signature:'); 
                    $event->sheet->mergeCells('E'.$m_sig_row.':F'.$m_sig_row);  
                    $event->sheet->getStyle('E'.$m_sig_row.':F'.$m_sig_row)->applyFromArray([ 
                        'font' => ['bold' => true]
                    ]); 
 
 
                //end signature rows
                    
                //signature box 
                    $sb_sig_row = $m_sig_row + 4;   
                    $event->sheet->mergeCells('A'.($m_sig_row+1).':B'.$sb_sig_row);  
                    $event->sheet->mergeCells('E'.($m_sig_row+1).':F'.$sb_sig_row);   
                //end signature box
 
                $event->sheet->getStyle('A5:F'.($m_sig_row+4))->applyFromArray([ 
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        // 'wrapText' => true,
                    ]                    
                ]); 

                $event->sheet->getStyle('A'.$last_row)->applyFromArray([ 
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT, 
                    ]                    
                ]);

            },

        ];
    }

    public function styles(Worksheet $sheet)
    {

        $styles = [
            // Style the first row as bold text.
            6        => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => Color::COLOR_WHITE]
                ],
                'fill' => ['fillType'   => Fill::FILL_SOLID, 'startColor' => ['argb' => '4472c4']],
            ],

        ];
        $total_rows = $sheet->getHighestRow() + 1;
        for ($i = 7; $i < $total_rows; $i++) {
            $modulas = $i % 2;
            if ($modulas == 1) :
                $styles[$i] =   [
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'dae3f3'],
                    ],
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '4472c4']
                        ],

                    ],
                ];
            else :
                $styles[$i] =   [
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '4472c4']
                        ],

                    ],
                ];
            endif;
        }
  
        return $styles;
    }

    public function drawings()
    { 
        
        if(file_exists(public_path(settings()->rxlogo->original))):
            $logo = public_path(settings()->rxlogo->original);
        else:
            $logo =  public_path('images/default/logo.png');
        endif;
     
        // if (!$imageResource = @imagecreatefromstring(file_get_contents('https://i.ibb.co/PZXLYWy/202404221128001502-copy.jpg'))) {
        if (!$imageResource = @imagecreatefromstring(file_get_contents($logo))) {
            throw new \Exception('The image URL cannot be converted into an image resource.');
        }
        $drawing = new MemoryDrawing();
        $drawing->setImageResource($imageResource);
        $drawing->setHeight(80);
        $drawing->setCoordinates('A1');
        return $drawing;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 20,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 20,
            'H' => 20,
            'I' => 20,
            'J' => 20,
            'K' => 20,
        ];
    }
}
