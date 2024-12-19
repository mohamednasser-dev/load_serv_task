<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */


    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'customer_id' => $this->customer_id,
            'customer_name' => $this->customer->name,
            'amount' => $this->amount,
            'payment_status' => $this->payment_status,
            'status' => $this->status,
            'invoice_date' => $this->invoice_date,
            'products' => InvoiceProductResource::collection($this->products),
        ];
    }


}
