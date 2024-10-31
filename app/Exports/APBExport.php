<?php

namespace App\Exports;

use App\Models\APB;
use Maatwebsite\Excel\Concerns\FromCollection;

class APBExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return APB::all();
    }
}
