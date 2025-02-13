<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use App\Models\UserProyek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProyekController extends Controller
{
    private function getSelectedValues ( $paramValue )
    {
        if ( ! $paramValue ) return [];

        try
        {
            return explode ( '||', base64_decode ( $paramValue ) );
        }
        catch ( \Exception $e )
        {
            \Log::error ( 'Error decoding parameter value: ' . $e->getMessage () );
            return [];
        }
    }

    public function index ( Request $request )
    {
        // Validate and set perPage to allowed values only
        $allowedPerPage = [ 10, 25, 50, 100 ];
        $perPage        = in_array ( (int) $request->get ( 'per_page' ), $allowedPerPage ) ? (int) $request->get ( 'per_page' ) : 10;

        $query = Proyek::query ()
            ->with ( 'users' );

        if ( $request->has ( 'search' ) )
        {
            $search = $request->get ( 'search' );
            $query->where ( function ($q) use ($search)
            {
                $q->where ( 'nama', 'ilike', "%{$search}%" );
            } );
        }

        if ( $request->filled ( 'selected_nama' ) )
        {
            try
            {
                $nama = $this->getSelectedValues ( $request->selected_nama );
                if ( in_array ( 'null', $nama ) )
                {
                    $nonNullValues = array_filter ( $nama, fn ( $value ) => $value !== 'null' );
                    $query->where ( function ($q) use ($nonNullValues)
                    {
                        $q->whereNull ( 'nama' )
                            ->orWhere ( 'nama', '' )
                            ->orWhere ( 'nama', '-' );

                        if ( ! empty ( $nonNullValues ) )
                        {
                            $q->orWhereIn ( 'nama', $nonNullValues );
                        }
                    } );
                }
                else
                {
                    $query->whereIn ( 'nama', $nama );
                }
            }
            catch ( \Exception $e )
            {
                \Log::error ( 'Error in nama filter: ' . $e->getMessage () );
            }
        }

        $uniqueValues = [ 
            'nama' => Proyek::whereNotNull ( 'nama' )
                ->where ( 'nama', '<>', '' )
                ->where ( 'nama', '<>', '-' )
                ->distinct ()
                ->pluck ( 'nama' )
                ->sort ()
                ->values (),
        ];

        // Filter projects based on user role
        $user         = Auth::user ();
        $proyeksQuery = Proyek::with ( "users" );
        if ( $user->role === 'koordinator_proyek' )
        {
            $proyeksQuery->whereHas ( 'users', function ($query) use ($user)
            {
                $query->where ( 'users.id', $user->id );
            } );
        }

        $proyeks = $proyeksQuery
            ->orderBy ( "updated_at", "desc" )
            ->orderBy ( "id", "desc" )
            ->get ();

        $TableData = $perPage === -1
            ? $query->orderBy ( 'updated_at', 'desc' )
                ->orderBy ( 'id', 'desc' )
                ->paginate ( $query->count () )
            : $query->orderBy ( 'updated_at', 'desc' )
                ->orderBy ( 'id', 'desc' )
                ->paginate ( $perPage );

        $TableData = $TableData->withQueryString ();

        return view ( "dashboard.proyek.proyek", [ 
            "headerPage"   => "Proyek",
            "page"         => "Data Proyek",

            "proyeks"      => $proyeks,
            "TableData"    => $TableData,
            'uniqueValues' => $uniqueValues,
        ] );
    }

    public function show ( $id )
    {
        $proyek = Proyek::with ( 'users' )
            ->findOrFail ( $id );

        return response ()->json ( [ 
            'data' => $proyek,
        ] );
    }

    public function store ( Request $request )
    {
        $credentials = $request->validate ( [ 
            "nama" => "required",
        ] );
        Proyek::create ( $credentials );
        return back ()->with ( "success", "Berhasil menambahkan data proyek." );
    }

    public function update ( Request $request, Proyek $id )
    {
        $credentials = $request->validate ( [ 
            "nama" => "required",
        ] );
        $id->update ( $credentials );
        $id->save ();

        return back ()->with ( "success", "Berhasil mengubah data proyek" );
    }
    public function destroy ( Proyek $id )
    {
        $id->delete ();
        $msg = "Berhasil menghapus data proyek";

        return back ()->with ( "success", $msg );
    }
}
