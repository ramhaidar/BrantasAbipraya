<?php
namespace App\Http\Controllers;
use App\Models\Nilai;
use Illuminate\Http\Request;
class NilaiController extends Controller
{
    public function showById ( Nilai $nilai )
    {
        return response ()->json ( $nilai );
    }
    public function update ( Request $request, Nilai $nilai )
    {
        $credentials = $request->validate ( [ 'harga' => 'required|numeric|min:0', 'net' => 'required|numeric|min:0', 'ppn' => 'required|numeric|min:0', 'bruto' => 'required|numeric|min:0',] );
        $nilai->update ( $credentials );
        return back ()->with ( 'success', 'Mengubah Data Nilai ATB' );
    }
}