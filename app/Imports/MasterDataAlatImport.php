<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToCollection;

class MasterDataAlatImport implements ToArray
{
    public function array( array $array )
    {
        // Return the array as is; you can modify this method to handle the data more specifically
        return $array;
    }
}
