<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Proyek;
use App\Models\UserProyek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index ()
    {
        $proyeks = Proyek::with ( "users" )
            ->orderBy ( "updated_at", "desc" )
            ->orderBy ( "id", "asc" )
            ->get ();
        $users   = User::orderBy ( 'updated_at', 'desc' )
            ->orderBy ( 'id', 'asc' )
            ->get ();

        return view (
            'dashboard.users.user',
            [ 
                'headerPage' => 'User',
                'page'       => 'Data User',

                'proyeks'    => $proyeks,
                'users'      => $users,
            ]
        );
    }

    public function store ( Request $request )
    {
        // dd ( $request->all () );
        // Validate incoming request
        $credentials = $request->validate ( [ 
            'name'         => 'required',
            'username'     => 'required',
            'sex'          => 'required|in:Laki-laki,Perempuan',
            'role'         => 'required|in:Admin,Pegawai,Boss',
            'proyek'       => 'array',
            'phone'        => 'required|min:8|unique:users',
            'email'        => 'required|email|unique:users',
            'password'     => 'required|min:8',
            'path_profile' => 'nullable|string', // Validate the path_profile if present
        ] );

        // Set default path_profile if not provided
        if ( empty ( $credentials[ 'path_profile' ] ) )
        {
            $credentials[ 'path_profile' ] = 'https://cdn-icons-png.flaticon.com/512/4140/4140037.png';
        }

        // Create the user with the validated data
        $user = User::create ( $credentials );

        // Attach projects to the user
        foreach ( $request->proyek as $i )
        {
            UserProyek::create ( [ 
                'id_proyek' => $i,
                'id_user'   => $user->id,
            ] );
        }

        return back ()->with ( 'success', 'Berhasil menambahkan data user' );
    }

    public function showByID ( User $user )
    {
        $user->load ( 'proyek' );

        return response ()->json ( $user );
    }

    public function update ( Request $request, User $user )
    {
        $validatedData = $request->validate ( [ 
            'name'     => 'required|max:255',
            'username' => 'required|max:255|unique:users,username,' . $user->id,
            'sex'      => 'required',
            'role'     => 'required',
            'phone'    => 'required',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8',
            'proyek'   => 'nullable|array'
        ] );


        try
        {
            DB::beginTransaction ();

            // Remove proyek from data to be updated on users table
            $userData = array_filter (
                array_diff_key ( $validatedData, [ 'proyek' => '' ] ),
                function ($value)
                {
                    return $value !== null;
                }
            );

            $user->update ( $userData );

            // Handle proyek relationship separately
            if ( isset ( $validatedData[ 'proyek' ] ) )
            {
                $user->proyek ()->sync ( $validatedData[ 'proyek' ] );
            }

            DB::commit ();
            return redirect ()->back ()->with ( 'success', 'User berhasil diperbarui' );
        }
        catch ( \Exception $e )
        {
            DB::rollback ();
            \Log::error ( 'User update error: ' . $e->getMessage () );
            return redirect ()->back ()->with ( 'error', 'Terjadi kesalahan saat memperbarui user' );
        }
    }

    public function destroy ( User $user )
    {
        $msg = 'User ' . $user->name . ' (' . $user->username . ') berhasil dihapus';
        $user->delete ();
        return back ()->with ( 'success', $msg );
    }
}