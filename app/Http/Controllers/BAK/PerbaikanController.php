<?php
namespace App\Http\Controllers;
use App\Models\Perbaikan;
use Illuminate\Http\Request;
class PerbaikanController extends Controller
{
    public function showById ( Perbaikan $perbaikan )
    {
        return response ()->json ( $perbaikan );
    }
    public function update ( Request $request, Perbaikan $perbaikan )
    {
        $credentials = $request->validate ( [ 'cabin' => 'required', 'engine_system' => 'required', 'transmission_system' => 'required', 'chassis_swing_machinery' => 'required', 'differential_system' => 'required', 'electrical_system' => 'required', 'pneumatic_system' => 'required', 'streering_system' => 'required', 'brake_system' => 'required', 'suspension' => 'required', 'attachment' => 'required', 'undercarriage' => 'required', 'final_drive' => 'required', 'freight_cost' => 'required|numeric',] );
        $perbaikan->update ( $credentials );
        return back ()->with ( 'success', 'Mengubah Data Perbaikan ATB' );
    }
}