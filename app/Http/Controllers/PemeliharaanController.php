<?php
namespace App\Http\Controllers;
use App\Models\Pemeliharaan;
use App\Models\OilLubricants;
use App\Models\MaintenanceKit;
use App\Models\ATB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
class PemeliharaanController extends Controller
{
    public function showById ( Pemeliharaan $pemeliharaan )
    {
        $resp = [ 'id' => $pemeliharaan->id, 'tyre' => $pemeliharaan->tyre, 'oilLubricants' => $pemeliharaan->oilLubricants, 'maintenanceKit' => $pemeliharaan->maintenanceKit ];
        return response ()->json ( $resp );
    }
    public function updateMaintenanceKit ( Request $request, Pemeliharaan $pemeliharaan )
    {
        $update         = $request->validate ( [ 'tyre' => 'required' ] );
        $maintenanceKit = $request->validate ( [ 'oil_filter' => 'required', 'fuel_fitler' => 'required', 'air_filter' => 'required', 'hydraulic_filter' => 'required', 'transmission_filter' => 'required', 'differential_filter' => 'required',] );
        dd ( ATB::where ( 'id_pemeliharaan', $pemeliharaan->id_pemeliharaan )->first () );
        MaintenanceKit::find ( $pemeliharaan->id_maintanance_kit )->update ( $maintenanceKit );
        $pemeliharaan->update ( $update );
        return redirect ( '/atb' )->with ( 'success', 'Mengubah Data Pemeliharan ATB' );
    }
    public function updateOilLubricants ( Request $request, Pemeliharaan $pemeliharaan )
    {
        $update        = $request->validate ( [ 'tyre' => 'required' ] );
        $oilLubricants = $request->validate ( [ 'engine_oil' => 'required', 'hydraulic_oil' => 'required', 'transmission_oil' => 'required', 'final_drive_oil' => 'required', 'swing_damper_oil' => 'required', 'differential_oil' => 'required', 'grease' => 'required', 'brake_power_steering_fluid' => 'required', 'coolant' => 'required',] );
        dd ( ATB::where ( 'id_pemeliharaan', $pemeliharaan->id_pemeliharaan )->first () );
        OilLubricants::find ( $pemeliharaan->id_oil_lubricants )->update ( $oilLubricants );
        $pemeliharaan->update ( $update );
        return back ()->with ( 'success', 'Mengubah Data Pemeliharan ATB' );
    }
}