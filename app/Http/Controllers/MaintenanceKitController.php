<?php
namespace App\Http\Controllers;

use App\Models\MaintenanceKit;
use Illuminate\Http\Request;

class MaintenanceKitController extends Controller
{
    public function update ( Request $request, MaintenanceKit $maintenanceKit )
    {
        $credentials = $request->validate ( [ 'oil_filter' => 'required', 'fuel_fitler' => 'required', 'air_filter' => 'required', 'hydraulic_filter' => 'required', 'transmission_filter' => 'required', 'differential_filter' => 'required',] );
        $maintenanceKit->update ( $credentials );
        return back ()->with ( 'success', 'Mengubah Data Maintenance Kit ATB' );
    }
}