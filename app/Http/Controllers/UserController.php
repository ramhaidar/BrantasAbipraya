<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Proyek;
use App\Models\UserProyek;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index ()
    {
        return view (
            'users.user',
            [ 
                'headerPage' => 'User',
                'page'       => 'Data User',
                "proyeks"    => Proyek::with ( "users" )->orderBy ( "updated_at" )->get (),
                'users'      => User::orderByDesc ( 'updated_at' )->get (),
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
        // Validate incoming request
        $credentials = $request->validate ( [ 
            'name'     => 'required',
            'username' => 'required',
            'sex'      => 'required|in:Laki-laki,Perempuan',
            'role'     => 'required|in:Admin,Pegawai,Boss',
            'proyek'   => 'array|nullable',
            'phone'    => 'required|min:8|unique:users,phone,' . $user->id,
            'email'    => 'required|email|unique:users,email,' . $user->id,
        ] );

        // Validate and hash password if provided
        if ( $request->password )
        {
            $credentials += $request->validate ( [ 'password' => 'required|min:8' ] );
            $credentials[ 'password' ] = bcrypt ( $request->password );
        }

        // Track current projects for the user
        $currentProyekIds = $user->proyek ()->pluck ( 'id_proyek' )->toArray ();

        // Get the new projects from the request
        $newProyekIds = $request->proyek ?? [];

        // Find projects to remove
        $proyeksToRemove = array_diff ( $currentProyekIds, $newProyekIds );
        if ( ! empty ( $proyeksToRemove ) )
        {
            UserProyek::where ( 'id_user', $user->id )
                ->whereIn ( 'id_proyek', $proyeksToRemove )
                ->delete ();
        }

        // Update or create new projects
        foreach ( $newProyekIds as $proyek )
        {
            UserProyek::updateOrCreate (
                [ 'id_user' => $user->id, 'id_proyek' => $proyek ],
                [ 'id_user' => $user->id, 'id_proyek' => $proyek ]
            );
        }

        // Update user record
        $user->update ( $credentials );

        // Return response
        return back ()->with ( 'success', 'Berhasil mengubah data user' );
    }

    public function destroy ( User $user )
    {
        $msg = 'Akun ' . $user->role . ' berhasil dihapus';
        $user->delete ();
        return back ()->with ( 'success', $msg );
    }
}