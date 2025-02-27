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
        $allowedPerPage = [ 10, 25, 50, 100 ];
        $perPage        = in_array ( (int) $request->get ( 'per_page' ), $allowedPerPage ) ? (int) $request->get ( 'per_page' ) : 10;

        $query = User::query ()
            ->with ( 'proyek' )
            ->orderBy ( $request->get ( 'sort', 'updated_at' ), $request->get ( 'direction', 'desc' ) );

        // Apply search filter
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

        // Apply all filters
        $this->applyAllFilters ( $request, $query );

        // Get data
        $TableData = $query->orderBy ( 'updated_at', 'desc' )
            ->orderBy ( 'id', 'desc' )
            ->paginate ( $perPage )
            ->withQueryString ();

        // Get unique values from database, independent of current filtering
        $uniqueValues = $this->getUniqueValues ();

        // Get projects based on user role
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

        return view ( 'dashboard.users.user', [ 
            'headerPage'   => 'User',
            'page'         => 'Data User',
            'proyeks'      => $proyeks,
            'TableData'    => $TableData,
            'uniqueValues' => $uniqueValues,
        ] );
    }

    /**
     * Get unique values for each filterable field directly from the database
     * 
     * @return array
     */
    private function getUniqueValues ()
    {
        $filterFields = [ 'name', 'username', 'sex', 'role', 'phone', 'email' ];
        $uniqueValues = [];

        foreach ( $filterFields as $field )
        {
            // Get all unique values for this field from the database
            $uniqueValues[ $field ] = User::whereNotNull ( $field )
                ->where ( $field, '!=', '' )
                ->distinct ()
                ->pluck ( $field )
                ->filter ()
                ->unique ()
                ->values ();
        }

        return $uniqueValues;
    }

    private function applyAllFilters ( Request $request, $query )
    {
        $filterFields = [ 'name', 'username', 'sex', 'role', 'phone', 'email' ];

        foreach ( $filterFields as $field )
        {
            $this->applyFilter ( $request, $query, $field );
        }
    }

    private function applyFilter ( Request $request, $query, $field )
    {
        $selectedValues = $request->get ( "selected_{$field}" );
        if ( ! empty ( $selectedValues ) )
        {
            try
            {
                // Mengubah pemisah array dari koma menjadi ||
                $values = explode ( '||', base64_decode ( $selectedValues ) );

                // Filter out empty values
                $values = array_filter ( $values, fn ( $value ) => $value !== '' );

                if ( in_array ( 'null', $values, true ) )
                {
                    $nonNullValues = array_filter ( $values, fn ( $value ) => $value !== 'null' );
                    $query->where ( function ($q) use ($field, $nonNullValues)
                    {
                        $q->whereNull ( $field )
                            ->orWhere ( $field, '-' )
                            ->orWhereIn ( $field, $nonNullValues );
                    } );
                }
                else
                {
                    $query->whereIn ( $field, $values );
                }
            }
            catch ( \Exception $e )
            {
                \Log::error ( "Error in {$field} filter: " . $e->getMessage () );
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