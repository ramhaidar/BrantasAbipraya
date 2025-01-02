<?php
namespace App\Http\Controllers;

use App\Models\ATB;
use App\Models\RKB;
use App\Models\SPB;
use App\Models\Proyek;
use App\Models\DetailSPB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ATBController extends Controller
{
    public function hutang_unit_alat ( Request $request )
    {
        return $this->showAtbPage (
            "Hutang Unit Alat",
            "Data ATB Hutang Unit Alat",
            $request->id_proyek
        );
    }

    public function panjar_unit_alat ( Request $request )
    {
        return $this->showAtbPage (
            "Panjar Unit Alat",
            "Data ATB Panjar Unit Alat",
            $request->id_proyek
        );
    }

    public function mutasi_proyek ( Request $request )
    {
        return $this->showAtbPage (
            "Mutasi Proyek",
            "Data ATB Mutasi Proyek",
            $request->id_proyek
        );
    }

    public function panjar_proyek ( Request $request )
    {
        return $this->showAtbPage (
            "Panjar Proyek",
            "Data ATB Panjar Proyek",
            $request->id_proyek
        );
    }

    protected function console ( $message )
    {
        $output = new \Symfony\Component\Console\Output\ConsoleOutput();
        $output->writeln ( $message );
    }

    private function showAtbPage ( $tipe, $pageTitle, $id_proyek )
    {
        $proyek = Proyek::with ( "users" )->find ( $id_proyek );

        $proyeks = Proyek::with ( "users" )
            ->orderBy ( "updated_at", "asc" )
            ->orderBy ( "id", "asc" )
            ->get ();

        $rkbs = RKB::with ( "spbs.linkSpbDetailSpb.detailSpb" )->where ( 'id_proyek', $id_proyek )->get ();
        $spbs = collect ();

        foreach ( $rkbs as $rkb )
        {
            $spbs = $spbs->merge ( $rkb->spbs );
        }

        $filteredSpbs = collect ();

        foreach ( $spbs as $index => $spb )
        {
            $allZero = true;
            foreach ( $spb->linkSpbDetailSpb as $link )
            {
                if ( $link->detailSpb->quantity_belum_diterima > 0 )
                {
                    $allZero = false;
                    break;
                }
            }

            if ( ! $allZero )
            {
                if ( $spb->is_addendum == false && ! isset ( $spb->id_spb_original ) )
                {
                    $filteredSpbs->push ( $spb );
                }

                if ( $spb->is_addendum == true && isset ( $spb->id_spb_original ) )
                {
                    $filteredSpbs->push ( $spb );
                }
            }
        }

        // Ubah nilai $tipe menjadi huruf kecil dan ganti spasi dengan tanda hubung
        $tipe = strtolower ( str_replace ( ' ', '-', $tipe ) );

        // Ambil data ATB dari database dengan relasi
        $atbs = ATB::with ( [ 'spb', 'masterDataSparepart' ] )
            ->where ( 'id_proyek', $id_proyek )
            ->where ( 'tipe', $tipe )
            ->get ();

        return view ( "dashboard.atb.atb", [ 
            "proyek"     => $proyek,
            "proyeks"    => $proyeks,
            "spbs"       => $filteredSpbs,
            "headerPage" => $proyek->nama_proyek,
            "page"       => $pageTitle,
            "atbs"       => $atbs, // Kirim data ATB ke view
        ] );
    }

    public function getlinkSpbDetailSpbs ( $id )
    {
        $SPB = SPB::with ( [ 
            "linkSpbDetailSpb.detailSpb.MasterDataSparepart",
            "linkSpbDetailSpb.detailSpb.linkSpbDetailSpb.spb",
        ] )->find ( $id );

        $DetailSPB = [];

        foreach ( $SPB->linkSpbDetailSpb as $item )
        {
            $DetailSPB[] = $item->detailSpb;
        }

        $html = view ( 'dashboard.atb.partials.spb-details-table', [ 'spbDetails' => $DetailSPB ] )->render ();

        return response ()->json ( [ 'html' => $html ] );
    }

    public function store ( Request $request )
    {
        try
        {
            DB::beginTransaction ();

            // Validate the request first
            $validated = $request->validate ( [ 
                'tipe'                       => 'required|string',
                'tanggal'                    => 'required|date',
                'id_proyek'                  => 'required|exists:proyek,id',
                'id_spb'                     => 'required|exists:spb,id',
                'surat_tanda_terima'         => 'required|file|mimes:pdf|max:10240',
                'quantity'                   => 'required|array',
                'quantity.*'                 => 'required|integer|min:0', // Changed min from 1 to 0
                'id_detail_spb'              => 'required|array',
                'id_detail_spb.*'            => 'required|exists:detail_spb,id',
                'id_master_data_sparepart'   => 'required|array',
                'id_master_data_sparepart.*' => 'required|exists:master_data_sparepart,id',
                'harga'                      => 'required|array',
                'harga.*'                    => 'required|numeric|min:0',
                'documentation_photos'       => 'required|array',
                'documentation_photos.*'     => 'required|array',
                'documentation_photos.*.*'   => 'required|file|image|mimes:jpeg,png,jpg|max:2048',
                'id_master_data_supplier'    => 'required|array',
                'id_master_data_supplier.*'  => 'required|exists:master_data_supplier,id',
            ] );

            // Create base storage paths
            $folderName      = 'atb_' . date ( 'YmdHis' ) . '_' . uniqid ();
            $baseStoragePath = 'uploads/atb/' . $folderName;
            $suratPath       = $baseStoragePath . '/surat';
            Storage::disk ( 'public' )->makeDirectory ( $suratPath );

            // Handle surat_tanda_terima upload
            $suratFile     = $request->file ( 'surat_tanda_terima' );
            $originalName  = pathinfo ( $suratFile->getClientOriginalName (), PATHINFO_FILENAME );
            $extension     = $suratFile->getClientOriginalExtension ();
            $timestamp     = now ()->format ( 'Y-m-d--H-i-s' );
            $suratFileName = "{$originalName}___{$timestamp}.{$extension}";
            $suratFilePath = $suratFile->storeAs ( $suratPath, $suratFileName, 'public' );

            $processedItems = 0;
            $skippedReasons = [];

            // Process each detail SPB item
            foreach ( $request->id_detail_spb as $index => $id_detail_spb )
            {
                // Get the DetailSPB record
                $detailSpb         = DetailSPB::find ( $id_detail_spb );
                $requestedQuantity = intval ( $request->quantity[ $index ] );

                // Skip if quantity is 0 or item has no remaining quantity
                if ( $requestedQuantity <= 0 )
                {
                    $skippedReasons[] = "Index $index: Requested quantity is 0";
                    continue;
                }

                if ( $detailSpb->quantity_belum_diterima <= 0 )
                {
                    $skippedReasons[] = "Index $index: No remaining quantity";
                    continue;
                }

                // Validate that we have all required data for this index
                if (
                    ! isset ( $request->id_master_data_sparepart[ $index ] ) ||
                    ! isset ( $request->harga[ $index ] ) ||
                    ! isset ( $request->id_master_data_supplier[ $index ] ) ||
                    ! isset ( $request->file ( 'documentation_photos' )[ $index ] )
                )
                {
                    if ( ! isset ( $request->id_master_data_sparepart[ $index ] ) )
                    {
                        $skippedReasons[] = "Index $index: Missing sparepart data";
                    }
                    if ( ! isset ( $request->harga[ $index ] ) )
                    {
                        $skippedReasons[] = "Index $index: Missing harga";
                    }
                    if ( ! isset ( $request->id_master_data_supplier[ $index ] ) )
                    {
                        $skippedReasons[] = "Index $index: Missing supplier";
                    }
                    if ( ! isset ( $request->file ( 'documentation_photos' )[ $index ] ) )
                    {
                        $skippedReasons[] = "Index $index: Missing photos";
                    }
                    continue;
                }

                // Create documentation folder for this item
                $docPath = $baseStoragePath . '/dokumentasi_' . uniqid ();
                Storage::disk ( 'public' )->makeDirectory ( $docPath );

                // Store documentation photos
                foreach ( $request->file ( 'documentation_photos' )[ $index ] as $photo )
                {
                    $photoName      = pathinfo ( $photo->getClientOriginalName (), PATHINFO_FILENAME );
                    $photoExt       = $photo->getClientOriginalExtension ();
                    $photoTimestamp = now ()->format ( 'Y-m-d--H-i-s' );
                    $fileName       = "{$photoName}___{$photoTimestamp}.{$photoExt}";
                    $photo->storeAs ( $docPath, $fileName, 'public' );
                }

                // Update quantity_belum_diterima
                $detailSpb->reduceQuantityBelumDiterima ( $requestedQuantity );

                // Create ATB record
                ATB::create ( [ 
                    'tipe'                     => $request->tipe,
                    'dokumentasi_foto'         => $docPath,
                    'surat_tanda_terima'       => $suratFilePath,
                    'tanggal'                  => $request->tanggal,
                    'quantity'                 => $requestedQuantity,
                    'harga'                    => $request->harga[ $index ],
                    'id_proyek'                => $request->id_proyek,
                    'id_spb'                   => $request->id_spb,
                    'id_detail_spb'            => $id_detail_spb,
                    'id_master_data_sparepart' => $request->id_master_data_sparepart[ $index ],
                    'id_master_data_supplier'  => $request->id_master_data_supplier[ $index ],
                ] );

                $processedItems++;
            }

            if ( $processedItems === 0 )
            {
                throw new \Exception( 'Tidak ada item yang valid untuk diproses. Alasan: ' . implode ( ', ', $skippedReasons ) );
            }

            DB::commit ();
            return back ()->with ( 'success', 'Data ATB berhasil disimpan' );

        }
        catch ( \Exception $e )
        {
            DB::rollBack ();
            if ( isset ( $baseStoragePath ) )
            {
                Storage::disk ( 'public' )->deleteDirectory ( $baseStoragePath );
            }
            return back ()->withErrors ( [ 'error' => 'Gagal menyimpan data ATB: ' . $e->getMessage () ] )->withInput ();
        }
    }

    public function destroy ( $id )
    {
        try
        {
            DB::beginTransaction ();

            $atb = ATB::findOrFail ( $id );

            // Delete associated files
            if ( $atb->dokumentasi_foto )
            {
                Storage::disk ( 'public' )->deleteDirectory ( $atb->dokumentasi_foto );
            }
            if ( $atb->surat_tanda_terima )
            {
                Storage::disk ( 'public' )->delete ( $atb->surat_tanda_terima );
            }

            // Restore quantity_belum_diterima for the corresponding DetailSPB
            $detailSpb = DetailSPB::find ( $atb->id_detail_spb );
            $detailSpb->increaseQuantityBelumDiterima ( $atb->quantity );

            // Delete the ATB record
            $atb->delete ();

            DB::commit ();

            return back ()->with ( 'success', 'Data ATB berhasil dihapus' );
        }
        catch ( \Exception $e )
        {
            DB::rollBack ();
            return back ()->withErrors ( [ 'error' => 'Gagal menghapus data ATB: ' . $e->getMessage () ] );
        }
    }
}