<?php

namespace App\Http\Controllers;

use App\Models\RKB;
use Illuminate\Http\Request;
use App\Models\LampiranRKBUrgent;
use App\Models\LinkAlatDetailRKB;
use App\Http\Controllers\Controller;

class LampiranEvaluasiUrgentController extends Controller
{
    public function store ( Request $request )
    {
        $validatedData = $request->validate ( [ 
            'id_link_alat_detail_rkb' => 'required|integer|exists:link_alat_detail_rkb,id',
            'lampiran'                => 'required|file|mimes:pdf|max:2048', // Maksimal 2MB
        ] );

        try
        {
            // Ambil data LinkAlatDetailRKB
            $linkAlatDetailRKB = LinkAlatDetailRKB::with ( 'masterDataAlat' )->findOrFail ( $validatedData[ 'id_link_alat_detail_rkb' ] );

            // Ambil data RKB terkait
            $rkb       = RKB::findOrFail ( $linkAlatDetailRKB->id_rkb );
            $rkbNumber = $rkb->nomor;

            // Dapatkan kode alat
            $kode_alat = $linkAlatDetailRKB->masterDataAlat->kode_alat;

            // Tangani unggahan file
            if ( $request->hasFile ( 'lampiran' ) )
            {
                $file = $request->file ( 'lampiran' );

                // Format nama file
                $originalName = pathinfo ( $file->getClientOriginalName (), PATHINFO_FILENAME );
                $extension    = $file->getClientOriginalExtension ();
                $timestamp    = now ()->format ( 'Y-m-d--H-i-s' );
                $fileName     = "{$originalName}___{$timestamp}.{$extension}";

                // Tentukan folder penyimpanan berdasarkan nomor RKB dan kode alat
                $folderPath = "uploads/rkb_urgent/{$rkbNumber}/{$kode_alat}";

                // Simpan file ke storage dengan struktur folder
                $filePath = $file->storeAs ( $folderPath, $fileName, 'public' );

                // Simpan data ke tabel LampiranRKBUrgent
                $lampiran = LampiranRKBUrgent::create ( [ 
                    'file_path'               => $filePath,
                    'id_link_alat_detail_rkb' => $validatedData[ 'id_link_alat_detail_rkb' ],
                ] );

                // Update id_lampiran_rkb_urgent pada LinkAlatDetailRKB
                $linkAlatDetailRKB->update ( [ 'id_lampiran_rkb_urgent' => $lampiran->id ] );

                return redirect ()->back ()->with ( 'success', 'Lampiran berhasil disimpan.' );
            }

            return redirect ()->back ()->with ( 'error', 'File lampiran tidak valid.' );
        }
        catch ( \Exception $e )
        {
            return redirect ()->back ()->withErrors ( [ 
                'error' => 'Terjadi kesalahan saat menyimpan lampiran: ' . $e->getMessage (),
            ] );
        }
    }

    public function show ( $id )
    {
        $lampiran = LampiranRKBUrgent::find ( $id );

        if ( ! $lampiran || ! $lampiran->file_path )
        {
            return response ()->json ( [ 'message' => 'Lampiran tidak ditemukan' ], 404 );
        }

        $pdfUrl = asset ( 'storage/' . $lampiran->file_path ); // Adjust based on storage structure
        return response ()->json ( [ 'pdf_url' => $pdfUrl ] );
    }

    public function destroy ( $id )
    {
        try
        {
            // Ambil lampiran dengan relasi
            $lampiran = LampiranRKBUrgent::with ( 'linkAlatDetailRKB.masterDataAlat' )->findOrFail ( $id );

            // Ambil informasi terkait
            $linkAlatDetailRKB = $lampiran->linkAlatDetailRKB;
            $kode_alat         = $linkAlatDetailRKB->masterDataAlat->kode_alat;
            $rkb               = RKB::findOrFail ( $linkAlatDetailRKB->id_rkb );
            $rkbNumber         = $rkb->nomor;

            // Path file dan folder
            $filePath   = storage_path ( 'app/public/' . $lampiran->file_path );
            $folderPath = storage_path ( 'app/public/lampiran_rkb_urgent/' . $kode_alat . '/' . $rkbNumber );

            // Hapus file
            if ( file_exists ( $filePath ) )
            {
                unlink ( $filePath );
            }

            // Hapus folder jika kosong
            if ( is_dir ( $folderPath ) && count ( scandir ( $folderPath ) ) == 2 )
            { // Folder kosong hanya memiliki '.' dan '..'
                rmdir ( $folderPath );
            }

            // Update relasi dan hapus lampiran
            $linkAlatDetailRKB->update ( [ 'id_lampiran_rkb_urgent' => null ] );
            $lampiran->delete ();

            return redirect ()->back ()->with ( 'success', 'Lampiran dan folder berhasil dihapus.' );
        }
        catch ( \Exception $e )
        {
            return redirect ()->back ()->withErrors ( [ 
                'error' => 'Terjadi kesalahan saat menghapus lampiran: ' . $e->getMessage (),
            ] );
        }
    }

    public function update ( Request $request, $id )
    {
        $validatedData = $request->validate ( [ 
            'lampiran' => 'required|file|mimes:pdf|max:2048', // Maksimal 2MB
        ] );

        try
        {
            // Find the existing lampiran
            $lampiran = LampiranRKBUrgent::findOrFail ( $id );

            // dd ( $lampiran );
// Find the associated LinkAlatDetailRKB
            $linkAlatDetailRKB = $lampiran->linkAlatDetailRkb;

            // Find the associated RKB to get RKB number for folder path
            $rkb       = RKB::findOrFail ( $linkAlatDetailRKB->id_rkb );
            $rkbNumber = $rkb->nomor;

            // Handle file upload
            if ( $request->hasFile ( 'lampiran' ) )
            {
                $file = $request->file ( 'lampiran' );

                // Delete the existing file
                $oldFilePath = storage_path ( 'app/public/' . $lampiran->file_path );
                if ( file_exists ( $oldFilePath ) )
                {
                    unlink ( $oldFilePath );
                }

                // Format new file name
                $originalName = pathinfo ( $file->getClientOriginalName (), PATHINFO_FILENAME );
                $extension    = $file->getClientOriginalExtension ();
                $timestamp    = now ()->format ( 'Y-m-d--H-i-s' );
                $fileName     = "{$originalName}___{$timestamp}.{$extension}";

                $kode_alat = $linkAlatDetailRKB->masterDataAlat->kode_alat;
                // Determine storage folder based on RKB number
                $folderPath = "uploads/rkb_urgent/{$rkbNumber}/{$kode_alat}";

                // Store the new file
                $newFilePath = $file->storeAs ( $folderPath, $fileName, 'public' );

                // Update the lampiran record with the new file path
                $lampiran->update ( [ 
                    'file_path' => $newFilePath
                ] );

                return redirect ()->back ()->with ( 'success', 'Lampiran berhasil diperbarui.' );
            }

            return redirect ()->back ()->with ( 'error', 'File lampiran tidak valid.' );
        }
        catch ( \Exception $e )
        {
            return redirect ()->back ()->withErrors ( [ 
                'error' => 'Terjadi kesalahan saat memperbarui lampiran: ' . $e->getMessage (),
            ] );
        }
    }
}
