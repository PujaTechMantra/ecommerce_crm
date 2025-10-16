<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\OrderItemReturn;
use App\Models\DamagedPartLog;
use Illuminate\Support\Str;

class UserRefundSummaryExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
    public function collection()
    {
        $data = $this->data;
        $return_data = [];
        foreach($data as $key=>$item){
            $existing_damages = DamagedPartLog::with('bom_part')->where('order_item_id', $item->order_item_id)->get()
            ->map(function ($damage){
                return [
                    'part_name'=>optional($damage->bom_part)->part_name,
                    'price'=>$damage->price,
                ];
            })->toArray();
            $total_part_amount = 0;
            $part_details = [];
            foreach($existing_damages as $part_index=>$part_item){
                $total_part_amount+=$part_item['price'];
               $part_details[] = $part_item['part_name'] . '(₹' . $part_item['price'] . ')';
            }
            $part_details_str = implode(', ', $part_details);
            $return_data[]= [
                'name'=>optional($item->user)->name,
                'mobile'=>optional($item->user)->mobile,
                'actual_amount'=>(int)$item->actual_amount,
                'port_charges'=>$item->port_charges,
                'over_due_amnt'=>$item->over_due_amnt,
                'part_amount'=>$total_part_amount,
                'refund_amount'=>(int)$item->refund_amount,
                'initiated_at'=>$item->refund_initiated_at,
                'parts'=>$part_details_str,
                'status'=>Str::upper($item->status),
                'reason'=>$item->reason,
                'vehicle_condition'=>$item->return_condition,
            ];
        }
        return collect($return_data);
      
    }

    public function headings(): array
    {
        return [
            'Name',
            'Mobile',
            'Actual Amount',
            'Port Charges',
            'Over Due Amount',
            'Part Amount',
            'Refund Amount',
            'Initiated At',
            'Parts',
            'Status',
            'Reason',
            'Vehicle Condition',
        ];
        
    }
}
