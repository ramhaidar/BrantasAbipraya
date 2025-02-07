<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Proyek;
use App\Models\UserProyek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index ( Request $request )
    {
        // Validate and set perPage to allowed values only
        $allowedPerPage = [ 10, 25, 50, 100 ];
        $perPage        = in_array ( (int) $request->get ( 'per_page' ), $allowedPerPage ) ? (int) $request->get ( 'per_page' ) : 10;

        $query = User::query ()
            ->with ( 'proyek' )
            ->orderBy ( $request->get ( 'sort', 'updated_at' ), $request->get ( 'direction', 'desc' ) );

        if ( $request->has ( 'search' ) )
        {
            $search = $request->get ( 'search' );
            $query->where ( function ($q) use ($search)
            {
                $q->where ( 'name', 'ilike', "%{$search}%" )
                    ->orWhere ( 'username', 'ilike', "%{$search}%" )
                    ->orWhere ( 'sex', 'ilike', "%{$search}%" )
                    ->orWhere ( 'path_profile', 'ilike', "%{$search}%" )
                    ->orWhere ( 'role', 'ilike', "%{$search}%" )
                    ->orWhere ( 'phone', 'ilike', "%{$search}%" )
                    ->orWhere ( 'email', 'ilike', "%{$search}%" );
            } );
        }

        $this->handleNameFilter ( $request, $query );
        $this->handleUsernameFilter ( $request, $query );
        $this->handleSexFilter ( $request, $query );
        $this->handleRoleFilter ( $request, $query );
        $this->handlePhoneFilter ( $request, $query );
        $this->handleEmailFilter ( $request, $query );

        $TableData = $query->orderBy ( 'updated_at', 'desc' )
            ->orderBy ( 'id', 'desc' )
            ->paginate ( $perPage )
            ->withQueryString ();

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

        $uniqueValues = [ 
            'name'     => User::whereNotNull ( 'name' )->distinct ()->pluck ( 'name' ),
            'username' => User::whereNotNull ( 'username' )->distinct ()->pluck ( 'username' ),
            'sex'      => User::whereNotNull ( 'sex' )->distinct ()->pluck ( 'sex' ),
            'role'     => User::whereNotNull ( 'role' )->distinct ()->pluck ( 'role' ),
            'phone'    => User::whereNotNull ( 'phone' )->distinct ()->pluck ( 'phone' ),
            'email'    => User::whereNotNull ( 'email' )->distinct ()->pluck ( 'email' ),
        ];

        return view ( 'dashboard.users.user', [ 
            'headerPage'   => 'User',
            'page'         => 'Data User',

            'proyeks'      => $proyeks,
            'TableData'    => $TableData,
            'uniqueValues' => $uniqueValues,
        ] );
    }

    private function handleNameFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_name' ) )
        {
            $name = explode ( ',', $request->selected_name );
            if ( in_array ( 'null', $name ) )
            {
                $nonNullValues = array_filter ( $name, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereNull ( 'name' )
                        ->orWhere ( 'name', '-' )
                        ->orWhereIn ( 'name', $nonNullValues );
                } );
            }
            else
            {
                $query->whereIn ( 'name', $name );
            }
        }
    }

    private function handleUsernameFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_username' ) )
        {
            $username = explode ( ',', $request->selected_username );
            if ( in_array ( 'null', $username ) )
            {
                $nonNullValues = array_filter ( $username, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereNull ( 'username' )
                        ->orWhere ( 'username', '-' )
                        ->orWhereIn ( 'username', $nonNullValues );
                } );
            }
            else
            {
                $query->whereIn ( 'username', $username );
            }
        }
    }

    private function handleSexFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_sex' ) )
        {
            $sex = explode ( ',', $request->selected_sex );
            if ( in_array ( 'null', $sex ) )
            {
                $nonNullValues = array_filter ( $sex, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereNull ( 'sex' )
                        ->orWhere ( 'sex', '-' )
                        ->orWhereIn ( 'sex', $nonNullValues );
                } );
            }
            else
            {
                $query->whereIn ( 'sex', $sex );
            }
        }
    }

    private function handleRoleFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_role' ) )
        {
            $role = explode ( ',', $request->selected_role );
            if ( in_array ( 'null', $role ) )
            {
                $nonNullValues = array_filter ( $role, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereNull ( 'role' )
                        ->orWhere ( 'role', '-' )
                        ->orWhereIn ( 'role', $nonNullValues );
                } );
            }
            else
            {
                $query->whereIn ( 'role', $role );
            }
        }
    }

    private function handlePhoneFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_phone' ) )
        {
            $phone = explode ( ',', $request->selected_phone );
            if ( in_array ( 'null', $phone ) )
            {
                $nonNullValues = array_filter ( $phone, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereNull ( 'phone' )
                        ->orWhere ( 'phone', '-' )
                        ->orWhereIn ( 'phone', $nonNullValues );
                } );
            }
            else
            {
                $query->whereIn ( 'phone', $phone );
            }
        }
    }

    private function handleEmailFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_email' ) )
        {
            $email = explode ( ',', $request->selected_email );
            if ( in_array ( 'null', $email ) )
            {
                $nonNullValues = array_filter ( $email, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereNull ( 'email' )
                        ->orWhere ( 'email', '-' )
                        ->orWhereIn ( 'email', $nonNullValues );
                } );
            }
            else
            {
                $query->whereIn ( 'email', $email );
            }
        }
    }

    public function store ( Request $request )
    {
        // dd ( $request->all () );
        // Validate incoming request
        $credentials = $request->validate ( [ 
            'name'         => 'required',
            'username'     => 'required',
            'sex'          => 'required|in:Laki-laki,Perempuan',
            'role'         => 'required|in:superadmin,svp,vp,admin_divisi,koordinator_proyek',
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
            'sex'      => 'required|in:Laki-laki,Perempuan',
            'role'     => 'required|in:superadmin,svp,vp,admin_divisi,koordinator_proyek',
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