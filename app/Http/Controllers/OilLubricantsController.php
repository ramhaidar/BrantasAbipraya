<?php
namespace App\Http\Controllers;
use App\Models\OilLubricants;
use Illuminate\Http\Request;
class OilLubricantsController extends Controller
{
    public function update ( Request $request, OilLubricants $oilLubricants )
    {
        $credentials = $request->validate ( [ 'engine_oil' => 'required', 'hydraulic_oil' => 'required', 'transmission_oil' => 'required', 'final_drive_oil' => 'required', 'swing_damper_oil' => 'required', 'differential_oil' => 'required', 'grease' => 'required', 'brake_power_steering_fluid' => 'required', 'coolant' => 'required',] );
        $oilLubricants->update ( $credentials );
        return back ()->with ( 'success', 'Mengubah Data Oil Lubricants ATB' );
    }
}