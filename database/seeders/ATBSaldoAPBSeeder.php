<?php

namespace Database\Seeders;

use App\Models\ATB;
use App\Models\APB;
use App\Models\SPB;
use App\Models\Saldo;
use App\Models\Proyek;
use App\Models\AlatProyek;
use App\Models\MasterDataSparepart;
use App\Models\MasterDataSupplier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ATBSaldoAPBSeeder extends Seeder
{
    public function run () : void
    {
        // Get required models
        $proyek     = Proyek::first ();
        $spbs       = SPB::all ();
        $spareparts = MasterDataSparepart::all ();
        $suppliers  = MasterDataSupplier::all ();

        // Validations
        if ( ! $spbs->count () ) throw new \Exception( 'No SPB records found' );
        if ( $spareparts->isEmpty () ) throw new \Exception( 'No Sparepart records found' );
        if ( $suppliers->isEmpty () ) throw new \Exception( 'No Supplier records found' );

        $atbTypes    = [ 'hutang-unit-alat', 'panjar-unit-alat', 'mutasi-proyek', 'panjar-proyek' ];
        $satuanTypes = [ 'BTL', 'LTR', 'PCS', 'KG', 'PAIL', 'DRUM', 'SET', 'PACK', 'BOX' ];

        foreach ( $atbTypes as $type )
        {
            for ( $i = 0; $i < 50; $i++ )
            {
                $randomSparepart = $spareparts->random ();
                $randomSupplier  = $suppliers->random ();

                $atbData = [ 
                    'tipe'                     => $type,
                    'dokumentasi_foto'         => "uploads/atb/{$type}/foto-" . ( $i + 1 ) . '.jpg',
                    'surat_tanda_terima'       => "uploads/atb/{$type}/surat-" . ( $i + 1 ) . '.pdf',
                    'tanggal'                  => Carbon::now ()->subDays ( rand ( 1, 30 ) ),
                    'quantity'                 => rand ( 1, 100 ),
                    'harga'                    => rand ( 100000, 10000000 ),
                    'id_proyek'                => $proyek->id,
                    'id_master_data_sparepart' => $randomSparepart->id,
                    'id_master_data_supplier'  => $randomSupplier->id
                ];

                if ( $type === 'hutang-unit-alat' )
                {
                    $atbData[ 'id_spb' ] = $spbs->random ()->id;
                }

                $atb = ATB::create ( $atbData );

                $saldo = Saldo::create ( [ 
                    'tipe'                     => $type,
                    'satuan'                   => $satuanTypes[ array_rand ( $satuanTypes ) ],
                    'quantity'                 => $atb->quantity,
                    'harga'                    => $atb->harga,
                    'id_atb'                   => $atb->id,
                    'id_proyek'                => $atb->id_proyek,
                    'id_master_data_sparepart' => $atb->id_master_data_sparepart,
                    'id_master_data_supplier'  => $atb->id_master_data_supplier,
                    'id_spb'                   => $type === 'hutang-unit-alat' ? $atbData[ 'id_spb' ] : null
                ] );

                if ( rand ( 1, 100 ) <= 30 )
                {
                    // Get available alat proyek for this proyek
                    $availableAlatProyek = AlatProyek::where ( 'id_proyek', $proyek->id )
                        ->whereNull ( 'removed_at' )
                        ->get ();

                    $apbData = [ 
                        'tipe'                     => $type,
                        'tanggal'                  => Carbon::now (),
                        'mekanik'                  => 'Mekanik ' . rand ( 1, 5 ),
                        'quantity'                 => rand ( 1, $atb->quantity ),
                        'status'                   => 'accepted',
                        'id_saldo'                 => $saldo->id,
                        'id_proyek'                => $saldo->id_proyek,
                        'id_master_data_sparepart' => $saldo->id_master_data_sparepart,
                        'id_master_data_supplier'  => $saldo->id_master_data_supplier
                    ];

                    // Add alat_proyek if available
                    if ( $availableAlatProyek->isNotEmpty () )
                    {
                        $apbData[ 'id_alat_proyek' ] = $availableAlatProyek->random ()->id;
                    }

                    APB::create ( $apbData );
                }
            }
        }
    }
}