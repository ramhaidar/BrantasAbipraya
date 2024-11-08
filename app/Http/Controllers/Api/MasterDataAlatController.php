<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\MasterDataAlat;
use App\Http\Controllers\Controller;


class MasterDataAlatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index ()
    {
        return response ()->json ( MasterDataAlat::all (), Response::HTTP_OK );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store ( Request $request )
    {
        $validated = $request->validate ( [ 
            'jenis_alat'    => 'required|string',
            'kode_alat'     => 'required|string',
            'merek_alat'    => 'required|string',
            'tipe_alat'     => 'required|string',
            'serial_number' => 'required|string',
        ] );

        $alat = MasterDataAlat::create ( $validated );

        return response ()->json ( $alat, Response::HTTP_CREATED );
    }

    /**
     * Display the specified resource.
     */
    public function show ( $id )
    {
        $alat = MasterDataAlat::find ( $id );

        if ( ! $alat )
        {
            return response ()->json ( [ 'message' => 'Resource not found' ], Response::HTTP_NOT_FOUND );
        }

        return response ()->json ( $alat, Response::HTTP_OK );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update ( Request $request, $id )
    {
        $alat = MasterDataAlat::find ( $id );

        if ( ! $alat )
        {
            return response ()->json ( [ 'message' => 'Resource not found' ], Response::HTTP_NOT_FOUND );
        }

        $validated = $request->validate ( [ 
            'jenis_alat'    => 'sometimes|required|string',
            'kode_alat'     => 'sometimes|required|string',
            'merek_alat'    => 'sometimes|required|string',
            'tipe_alat'     => 'sometimes|required|string',
            'serial_number' => 'sometimes|required|string',
        ] );

        $alat->update ( $validated );

        return response ()->json ( $alat, Response::HTTP_OK );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy ( $id )
    {
        $alat = MasterDataAlat::find ( $id );

        if ( ! $alat )
        {
            return response ()->json ( [ 'message' => 'Resource not found' ], Response::HTTP_NOT_FOUND );
        }

        $alat->delete ();

        return response ()->json ( [ 'message' => 'Deleted successfully' ], Response::HTTP_NO_CONTENT );
    }
}
