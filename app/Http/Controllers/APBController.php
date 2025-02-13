<?php
namespace App\Http\Controllers;
use App\Models\APB;
use App\Models\ATB;
use App\Models\RKB;
use App\Models\Alat;
use App\Models\Saldo;
use App\Models\Proyek;
use App\Models\AlatProyek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MasterDataSparepart;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
class APBController extends Controller
{
    public function hutang_unit_alat ( Request $request )
    {
        return $this->showApbPage ( "Hutang Unit Alat", "Data APB EX Unit Alat", $request->id_proyek );
    }
    public function panjar_unit_alat ( Request $request )
    {
        return $this->showApbPage ( "Panjar Unit Alat", "Data APB EX Panjar Unit Alat", $request->id_proyek );
    }
    public function mutasi_proyek ( Request $request )
    {
        return $this->showApbPage ( "Mutasi Proyek", "Data APB EX Mutasi Proyek", $request->id_proyek );
    }
    public function panjar_proyek ( Request $request )
    {
        return $this->showApbPage ( "Panjar Proyek", "Data APB EX Panjar Proyek", $request->id_proyek );
    }
    private function getUniqueValues ( $query )
    {
        $id_proyek                       = request ( 'id_proyek' );
        $tipe                            = strtolower ( str_replace ( ' ', '-', request ( 'tipe', '' ) ) );
        $baseQuery                       = clone $query;
        $baseQuery->getQuery ()->selects = null;
        $results                         = $baseQuery->with ( [ 'alatProyek.masterDataAlat', 'masterDataSparepart.kategoriSparepart', 'masterDataSupplier', 'saldo', 'tujuanProyek' ] )->get ()
            ->map ( function ($item)
            {
                // Calculate jumlah_harga for each item
                $item->jumlah_harga = ( $item->saldo->harga ?? 0 ) * $item->quantity;
                return $item;
            } );
        return [ 'tanggal' => $results->pluck ( 'tanggal' )->filter ()->unique ()->values (), 'tujuan_proyek' => $results->pluck ( 'tujuanProyek.nama' )->filter ()->unique ()->values (), 'jenis_alat' => $results->pluck ( 'alatProyek.masterDataAlat.jenis_alat' )->filter ()->unique ()->values (), 'kode_alat' => $results->pluck ( 'alatProyek.masterDataAlat.kode_alat' )->filter ()->unique ()->values (), 'merek_alat' => $results->pluck ( 'alatProyek.masterDataAlat.merek_alat' )->filter ()->unique ()->values (), 'tipe_alat' => $results->pluck ( 'alatProyek.masterDataAlat.tipe_alat' )->filter ()->unique ()->values (), 'serial_number' => $results->pluck ( 'alatProyek.masterDataAlat.serial_number' )->filter ()->unique ()->values (), 'kode' => $results->pluck ( 'masterDataSparepart.kategoriSparepart.kode' )->filter ()->unique ()->values (), 'supplier' => $results->pluck ( 'masterDataSupplier.nama' )->filter ()->unique ()->values (), 'sparepart' => $results->pluck ( 'masterDataSparepart.nama' )->filter ()->unique ()->values (), 'merk' => $results->pluck ( 'masterDataSparepart.merk' )->filter ()->unique ()->values (), 'part_number' => $results->pluck ( 'masterDataSparepart.part_number' )->filter ()->unique ()->values (), 'satuan' => $results->pluck ( 'saldo.satuan' )->filter ()->unique ()->values (), 'quantity' => $results->pluck ( 'quantity' )->filter ()->unique ()->values (), 'harga' => $results->pluck ( 'saldo.harga' )->filter ()->unique ()->sort ()->values (), 'jumlah_harga' => $results->pluck ( 'jumlah_harga' )->filter ()->unique ()->sort ()->values (), 'mekanik' => $results->pluck ( 'mekanik' )->filter ()->unique ()->values (), 'status' => collect ( [ 'pending', 'accepted', 'rejected' ] )->values (),];
    }
    private function applyFilters ( $query, $request )
    {
        if ( $request->filled ( 'selected_tanggal' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_tanggal' ) );
            $query->whereIn ( 'tanggal', $selectedValues );
        }
        if ( $request->filled ( 'selected_jenis_alat' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_jenis_alat' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->whereDoesntHave ( 'alatProyek.masterDataAlat' )
                        ->orWhereHas ( 'alatProyek.masterDataAlat', function ($sq)
                        {
                            $sq->whereNull ( 'jenis_alat' )
                                ->orWhere ( 'jenis_alat', '' )
                                ->orWhere ( 'jenis_alat', '-' );
                        } );

                    $otherValues = array_diff ( $selectedValues, [ 'Empty/Null' ] );
                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhereHas ( 'alatProyek.masterDataAlat', function ($sq) use ($otherValues)
                        {
                            $sq->whereIn ( 'jenis_alat', $otherValues );
                        } );
                    }
                }
                else
                {
                    $q->whereHas ( 'alatProyek.masterDataAlat', function ($sq) use ($selectedValues)
                    {
                        $sq->whereIn ( 'jenis_alat', $selectedValues );
                    } );
                }
            } );
        }
        if ( $request->filled ( 'selected_kode_alat' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_kode_alat' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->whereDoesntHave ( 'alatProyek.masterDataAlat' )
                        ->orWhereHas ( 'alatProyek.masterDataAlat', function ($sq)
                        {
                            $sq->whereNull ( 'kode_alat' )
                                ->orWhere ( 'kode_alat', '' )
                                ->orWhere ( 'kode_alat', '-' );
                        } );

                    $otherValues = array_diff ( $selectedValues, [ 'Empty/Null' ] );
                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhereHas ( 'alatProyek.masterDataAlat', function ($sq) use ($otherValues)
                        {
                            $sq->whereIn ( 'kode_alat', $otherValues );
                        } );
                    }
                }
                else
                {
                    $q->whereHas ( 'alatProyek.masterDataAlat', function ($sq) use ($selectedValues)
                    {
                        $sq->whereIn ( 'kode_alat', $selectedValues );
                    } );
                }
            } );
        }
        if ( $request->filled ( 'selected_merek_alat' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_merek_alat' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->whereDoesntHave ( 'alatProyek.masterDataAlat' )
                        ->orWhereHas ( 'alatProyek.masterDataAlat', function ($sq)
                        {
                            $sq->whereNull ( 'merek_alat' )
                                ->orWhere ( 'merek_alat', '' )
                                ->orWhere ( 'merek_alat', '-' );
                        } );

                    $otherValues = array_diff ( $selectedValues, [ 'Empty/Null' ] );
                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhereHas ( 'alatProyek.masterDataAlat', function ($sq) use ($otherValues)
                        {
                            $sq->whereIn ( 'merek_alat', $otherValues );
                        } );
                    }
                }
                else
                {
                    $q->whereHas ( 'alatProyek.masterDataAlat', function ($sq) use ($selectedValues)
                    {
                        $sq->whereIn ( 'merek_alat', $selectedValues );
                    } );
                }
            } );
        }

        if ( $request->filled ( 'selected_tipe_alat' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_tipe_alat' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->whereDoesntHave ( 'alatProyek.masterDataAlat' )
                        ->orWhereHas ( 'alatProyek.masterDataAlat', function ($sq)
                        {
                            $sq->whereNull ( 'tipe_alat' )
                                ->orWhere ( 'tipe_alat', '' )
                                ->orWhere ( 'tipe_alat', '-' );
                        } );

                    $otherValues = array_diff ( $selectedValues, [ 'Empty/Null' ] );
                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhereHas ( 'alatProyek.masterDataAlat', function ($sq) use ($otherValues)
                        {
                            $sq->whereIn ( 'tipe_alat', $otherValues );
                        } );
                    }
                }
                else
                {
                    $q->whereHas ( 'alatProyek.masterDataAlat', function ($sq) use ($selectedValues)
                    {
                        $sq->whereIn ( 'tipe_alat', $selectedValues );
                    } );
                }
            } );
        }

        if ( $request->filled ( 'selected_serial_number' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_serial_number' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->whereDoesntHave ( 'alatProyek.masterDataAlat' )
                        ->orWhereHas ( 'alatProyek.masterDataAlat', function ($sq)
                        {
                            $sq->whereNull ( 'serial_number' )
                                ->orWhere ( 'serial_number', '' )
                                ->orWhere ( 'serial_number', '-' );
                        } );

                    $otherValues = array_diff ( $selectedValues, [ 'Empty/Null' ] );
                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhereHas ( 'alatProyek.masterDataAlat', function ($sq) use ($otherValues)
                        {
                            $sq->whereIn ( 'serial_number', $otherValues );
                        } );
                    }
                }
                else
                {
                    $q->whereHas ( 'alatProyek.masterDataAlat', function ($sq) use ($selectedValues)
                    {
                        $sq->whereIn ( 'serial_number', $selectedValues );
                    } );
                }
            } );
        }

        if ( $request->filled ( 'selected_kode' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_kode' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->whereDoesntHave ( 'masterDataSparepart.kategoriSparepart' )
                        ->orWhereHas ( 'masterDataSparepart.kategoriSparepart', function ($sq)
                        {
                            $sq->whereNull ( 'kode' )
                                ->orWhere ( 'kode', '' )
                                ->orWhere ( 'kode', '-' );
                        } );

                    $otherValues = array_diff ( $selectedValues, [ 'Empty/Null' ] );
                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhereHas ( 'masterDataSparepart.kategoriSparepart', function ($sq) use ($otherValues)
                        {
                            $sq->whereIn ( 'kode', $otherValues );
                        } );
                    }
                }
                else
                {
                    $q->whereHas ( 'masterDataSparepart.kategoriSparepart', function ($sq) use ($selectedValues)
                    {
                        $sq->whereIn ( 'kode', $selectedValues );
                    } );
                }
            } );
        }

        if ( $request->filled ( 'selected_supplier' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_supplier' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->whereDoesntHave ( 'masterDataSupplier' )
                        ->orWhereHas ( 'masterDataSupplier', function ($sq)
                        {
                            $sq->whereNull ( 'nama' )
                                ->orWhere ( 'nama', '' )
                                ->orWhere ( 'nama', '-' );
                        } );

                    $otherValues = array_diff ( $selectedValues, [ 'Empty/Null' ] );
                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhereHas ( 'masterDataSupplier', function ($sq) use ($otherValues)
                        {
                            $sq->whereIn ( 'nama', $otherValues );
                        } );
                    }
                }
                else
                {
                    $q->whereHas ( 'masterDataSupplier', function ($sq) use ($selectedValues)
                    {
                        $sq->whereIn ( 'nama', $selectedValues );
                    } );
                }
            } );
        }

        if ( $request->filled ( 'selected_sparepart' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_sparepart' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->whereDoesntHave ( 'masterDataSparepart' )
                        ->orWhereHas ( 'masterDataSparepart', function ($sq)
                        {
                            $sq->whereNull ( 'nama' )
                                ->orWhere ( 'nama', '' )
                                ->orWhere ( 'nama', '-' );
                        } );

                    $otherValues = array_diff ( $selectedValues, [ 'Empty/Null' ] );
                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhereHas ( 'masterDataSparepart', function ($sq) use ($otherValues)
                        {
                            $sq->whereIn ( 'nama', $otherValues );
                        } );
                    }
                }
                else
                {
                    $q->whereHas ( 'masterDataSparepart', function ($sq) use ($selectedValues)
                    {
                        $sq->whereIn ( 'nama', $selectedValues );
                    } );
                }
            } );
        }

        if ( $request->filled ( 'selected_merk' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_merk' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->whereDoesntHave ( 'masterDataSparepart' )
                        ->orWhereHas ( 'masterDataSparepart', function ($sq)
                        {
                            $sq->whereNull ( 'merk' )
                                ->orWhere ( 'merk', '' )
                                ->orWhere ( 'merk', '-' );
                        } );

                    $otherValues = array_diff ( $selectedValues, [ 'Empty/Null' ] );
                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhereHas ( 'masterDataSparepart', function ($sq) use ($otherValues)
                        {
                            $sq->whereIn ( 'merk', $otherValues );
                        } );
                    }
                }
                else
                {
                    $q->whereHas ( 'masterDataSparepart', function ($sq) use ($selectedValues)
                    {
                        $sq->whereIn ( 'merk', $selectedValues );
                    } );
                }
            } );
        }

        if ( $request->filled ( 'selected_merk' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_merk' ) );
            $query->whereHas ( 'masterDataSparepart', function ($q) use ($selectedValues)
            {
                $q->whereIn ( 'merk', $selectedValues );
            } );
        }

        if ( $request->filled ( 'selected_part_number' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_part_number' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->whereDoesntHave ( 'masterDataSparepart' )
                        ->orWhereHas ( 'masterDataSparepart', function ($sq)
                        {
                            $sq->whereNull ( 'part_number' )
                                ->orWhere ( 'part_number', '' )
                                ->orWhere ( 'part_number', '-' );
                        } );

                    $otherValues = array_diff ( $selectedValues, [ 'Empty/Null' ] );
                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhereHas ( 'masterDataSparepart', function ($sq) use ($otherValues)
                        {
                            $sq->whereIn ( 'part_number', $otherValues );
                        } );
                    }
                }
                else
                {
                    $q->whereHas ( 'masterDataSparepart', function ($sq) use ($selectedValues)
                    {
                        $sq->whereIn ( 'part_number', $selectedValues );
                    } );
                }
            } );
        }

        if ( $request->filled ( 'selected_quantity' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_quantity' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                // Handle Empty/Null case
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->whereNull ( 'quantity' )
                        ->orWhere ( 'quantity', 0 );

                    // Remove 'Empty/Null' from array and filter out non-numeric values
                    $numericValues = array_filter (
                        array_diff ( $selectedValues, [ 'Empty/Null' ] ),
                        'is_numeric'
                    );

                    if ( ! empty ( $numericValues ) )
                    {
                        $q->orWhereIn ( 'quantity', $numericValues );
                    }
                }
                else
                {
                    // If no Empty/Null selected, just filter by numeric values
                    $numericValues = array_filter ( $selectedValues, 'is_numeric' );
                    if ( ! empty ( $numericValues ) )
                    {
                        $q->whereIn ( 'quantity', $numericValues );
                    }
                }
            } );
        }

        if ( $request->filled ( 'selected_quantity_dikirim' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_quantity_dikirim' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->where ( function ($sq)
                    {
                        $sq->whereNull ( 'status' )  // Records that are "penggunaan"
                            ->orWhere ( 'quantity', '0' )
                            ->orWhere ( 'quantity', null );
                    } );

                    $otherValues = array_diff ( $selectedValues, [ 'Empty/Null' ] );
                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhere ( function ($sq) use ($otherValues)
                        {
                            $sq->whereNotNull ( 'status' )  // Only for records with status (mutasi)
                                ->whereIn ( 'quantity', $otherValues );
                        } );
                    }
                }
                else
                {
                    $q->whereNotNull ( 'status' )  // Only for records with status (mutasi)
                        ->whereIn ( 'quantity', $selectedValues );
                }
            } );
        }

        if ( $request->filled ( 'selected_satuan' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_satuan' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->whereDoesntHave ( 'saldo' )
                        ->orWhereHas ( 'saldo', function ($sq)
                        {
                            $sq->whereNull ( 'satuan' )
                                ->orWhere ( 'satuan', '' )
                                ->orWhere ( 'satuan', '-' );
                        } );

                    $otherValues = array_diff ( $selectedValues, [ 'Empty/Null' ] );
                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhereHas ( 'saldo', function ($sq) use ($otherValues)
                        {
                            $sq->whereIn ( 'satuan', $otherValues );
                        } );
                    }
                }
                else
                {
                    $q->whereHas ( 'saldo', function ($sq) use ($selectedValues)
                    {
                        $sq->whereIn ( 'satuan', $selectedValues );
                    } );
                }
            } );
        }

        if ( $request->filled ( 'selected_jumlah_harga' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_jumlah_harga' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->where ( function ($sq)
                    {
                        $sq->whereNull ( 'quantity' )
                            ->orWhere ( 'quantity', 0 )
                            ->orWhereDoesntHave ( 'saldo' )
                            ->orWhereHas ( 'saldo', function ($ssq)
                            {
                                $ssq->whereNull ( 'harga' )
                                    ->orWhere ( 'harga', 0 );
                            } );
                    } );

                    $otherValues = array_filter (
                        array_diff ( $selectedValues, [ 'Empty/Null' ] ),
                        function ($value)
                        {
                            return is_numeric ( $value ) && $value !== '';
                        }
                    );

                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhereHas ( 'saldo', function ($sq) use ($otherValues)
                        {
                            foreach ( $otherValues as $value )
                            {
                                $sq->orWhereRaw ( '(saldo.harga * apb.quantity) = ?', [ $value ] );
                            }
                        } );
                    }
                }
                else
                {
                    $numericValues = array_filter ( $selectedValues, function ($value)
                    {
                        return is_numeric ( $value ) && $value !== '';
                    } );

                    if ( ! empty ( $numericValues ) )
                    {
                        $q->whereHas ( 'saldo', function ($sq) use ($numericValues)
                        {
                            foreach ( $numericValues as $value )
                            {
                                $sq->orWhereRaw ( '(saldo.harga * apb.quantity) = ?', [ $value ] );
                            }
                        } );
                    }
                }
            } );
        }

        if ( $request->filled ( 'selected_harga' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_harga' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->whereDoesntHave ( 'saldo' )
                        ->orWhereHas ( 'saldo', function ($sq)
                        {
                            $sq->whereNull ( 'harga' )
                                ->orWhere ( 'harga', '0' );
                        } );

                    $otherValues = array_filter (
                        array_diff ( $selectedValues, [ 'Empty/Null' ] ),
                        function ($value)
                        {
                            return is_numeric ( $value ) && $value !== '';
                        }
                    );

                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhereHas ( 'saldo', function ($sq) use ($otherValues)
                        {
                            $sq->whereIn ( 'harga', $otherValues );
                        } );
                    }
                }
                else
                {
                    $numericValues = array_filter ( $selectedValues, function ($value)
                    {
                        return is_numeric ( $value ) && $value !== '';
                    } );

                    if ( ! empty ( $numericValues ) )
                    {
                        $q->whereHas ( 'saldo', function ($sq) use ($numericValues)
                        {
                            $sq->whereIn ( 'harga', $numericValues );
                        } );
                    }
                }
            } );
        }

        if ( $request->filled ( 'selected_mekanik' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_mekanik' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->whereNull ( 'mekanik' )
                        ->orWhere ( 'mekanik', '' )
                        ->orWhere ( 'mekanik', '-' );

                    $otherValues = array_diff ( $selectedValues, [ 'Empty/Null' ] );
                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhereIn ( 'mekanik', $otherValues );
                    }
                }
                else
                {
                    $q->whereIn ( 'mekanik', $selectedValues );
                }
            } );
        }

        if ( $request->filled ( 'selected_tujuan_proyek' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_tujuan_proyek' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->whereNull ( 'id_tujuan_proyek' )
                        ->orWhereHas ( 'tujuanProyek', function ($sq)
                        {
                            $sq->whereNull ( 'nama' )->orWhere ( 'nama', '' );
                        } );

                    $otherValues = array_diff ( $selectedValues, [ 'Empty/Null' ] );
                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhereHas ( 'tujuanProyek', function ($sq) use ($otherValues)
                        {
                            $sq->whereIn ( 'nama', $otherValues );
                        } );
                    }
                }
                else
                {
                    $q->whereHas ( 'tujuanProyek', function ($sq) use ($selectedValues)
                    {
                        $sq->whereIn ( 'nama', $selectedValues );
                    } );
                }
            } );
        }

        if ( $request->filled ( 'selected_quantity_diterima' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_quantity_diterima' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->where ( function ($sq)
                    {
                        $sq->whereNull ( 'status' ) // Not a mutasi record
                            ->orWhereDoesntHave ( 'atbMutasi' ) // No ATB record
                            ->orWhereHas ( 'atbMutasi', function ($ssq)
                            {
                                $ssq->whereNull ( 'quantity' )
                                    ->orWhere ( 'quantity', '0' );
                            } );
                    } );

                    $otherValues = array_diff ( $selectedValues, [ 'Empty/Null' ] );
                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhereHas ( 'atbMutasi', function ($sq) use ($otherValues)
                        {
                            $sq->whereIn ( 'quantity', $otherValues );
                        } );
                    }
                }
                else
                {
                    $q->whereHas ( 'atbMutasi', function ($sq) use ($selectedValues)
                    {
                        $sq->whereIn ( 'quantity', $selectedValues );
                    } );
                }
            } );
        }

        if ( $request->filled ( 'selected_quantity_digunakan' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_quantity_digunakan' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->where ( function ($sq)
                    {
                        $sq->whereNotNull ( 'status' ) // Is a mutasi record
                            ->orWhere ( 'quantity', '0' )
                            ->orWhere ( 'quantity', null );
                    } );

                    $otherValues = array_diff ( $selectedValues, [ 'Empty/Null' ] );
                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhere ( function ($sq) use ($otherValues)
                        {
                            $sq->whereNull ( 'status' ) // Only penggunaan records
                                ->whereIn ( 'quantity', $otherValues );
                        } );
                    }
                }
                else
                {
                    $q->whereNull ( 'status' ) // Only penggunaan records
                        ->whereIn ( 'quantity', $selectedValues );
                }
            } );
        }

        if ( $request->filled ( 'selected_status' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_status' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->whereNull ( 'status' ); // For "Penggunaan" records

                    $otherValues = array_diff ( $selectedValues, [ 'Empty/Null' ] );
                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhereIn ( 'status', $otherValues );
                    }
                }
                else
                {
                    $q->whereIn ( 'status', $selectedValues );
                }
            } );
        }

        return $query;
    }
    private function getSelectedValues ( $paramValue )
    {
        if ( ! $paramValue ) return [];
        try
        {
            return explode ( '||', base64_decode ( $paramValue ) );
        }
        catch ( \Exception $e )
        {
            return [];
        }
    }
    private function showApbPage ( $tipe, $pageTitle, $id_proyek )
    {
        $allowedPerPage = [ 10, 25, 50, 100 ];
        $perPage        = in_array ( (int) request ()->get ( 'per_page' ), $allowedPerPage ) ? (int) request ()->get ( 'per_page' ) : 10;
        $tipe           = strtolower ( str_replace ( ' ', '-', $tipe ) );
        $search         = request ()->get ( 'search', '' );
        $query          = APB::with ( [ 'masterDataSparepart.kategoriSparepart', 'masterDataSupplier', 'proyek', 'alatProyek.masterDataAlat', 'saldo' ] )->where ( 'id_proyek', $id_proyek )->where ( 'tipe', $tipe );
        if ( $search )
        {
            $query->where ( function ($q) use ($search)
            {
                $searchLower    = strtolower ( trim ( $search ) );
                $searchParts    = explode ( ' ', $searchLower );
                $hariIndonesia  = [ 'senin' => 'Monday', 'selasa' => 'Tuesday', 'rabu' => 'Wednesday', 'kamis' => 'Thursday', 'jumat' => 'Friday', "jum'at" => 'Friday', 'sabtu' => 'Saturday', 'minggu' => 'Sunday',];
                $bulanIndonesia = [ 'januari' => '01', 'februari' => '02', 'maret' => '03', 'april' => '04', 'mei' => '05', 'juni' => '06', 'juli' => '07', 'agustus' => '08', 'september' => '09', 'oktober' => '10', 'november' => '11', 'desember' => '12',];
                $isDateSearch   = false;
                $year           = null;
                $month          = null;
                $day            = null;
                foreach ( $searchParts as $part )
                {
                    if ( is_numeric ( $part ) && strlen ( $part ) === 4 )
                    {
                        $year         = $part;
                        $isDateSearch = true;
                        continue;
                    }foreach ( $hariIndonesia as $indo => $eng )
                    {
                        if ( str_starts_with ( $indo, $part ) )
                        {
                            $isDateSearch = true;
                            $q->orWhereRaw ( "DAYNAME(tanggal) = ?", [ $eng ] );
                            break 2;
                        }
                    }foreach ( $bulanIndonesia as $indo => $num )
                    {
                        if ( str_starts_with ( $indo, $part ) )
                        {
                            $month        = $num;
                            $isDateSearch = true;
                            break;
                        }
                    }if ( is_numeric ( $part ) && strlen ( $part ) <= 2 )
                    {
                        $day          = sprintf ( "%02d", $part );
                        $isDateSearch = true;
                    }
                }if ( $isDateSearch )
                {
                    if ( $year )
                    {
                        $q->whereYear ( 'tanggal', $year );
                    }if ( $month )
                    {
                        $q->whereMonth ( 'tanggal', $month );
                    }if ( $day )
                    {
                        $q->whereDay ( 'tanggal', $day );
                    }
                }
                else
                {
                    $q->where ( function ($q) use ($search)
                    {
                        $q->whereHas ( 'masterDataSparepart', function ($q) use ($search)
                        {
                            $q->where ( 'nama', 'ilike', "%{$search}%" )->orWhere ( 'part_number', 'ilike', "%{$search}%" )->orWhere ( 'merk', 'ilike', "%{$search}%" )->orWhereHas ( 'kategoriSparepart', function ($q) use ($search)
                            {
                                $q->where ( 'kode', 'ilike', "%{$search}%" )->orWhere ( 'nama', 'ilike', "%{$search}%" );
                            } );
                        } )->orWhereHas ( 'masterDataSupplier', function ($q) use ($search)
                        {
                            $q->where ( 'nama', 'ilike', "%{$search}%" );
                        } )->orWhereHas ( 'alatProyek.masterDataAlat', function ($q) use ($search)
                        {
                            $q->where ( 'jenis_alat', 'ilike', "%{$search}%" )->orWhere ( 'kode_alat', 'ilike', "%{$search}%" )->orWhere ( 'merek_alat', 'ilike', "%{$search}%" )->orWhere ( 'tipe_alat', 'ilike', "%{$search}%" )->orWhere ( 'serial_number', 'ilike', "%{$search}%" );
                        } )->orWhereHas ( 'tujuanProyek', function ($q) use ($search)
                        {
                            $q->where ( 'nama', 'ilike', "%{$search}%" );
                        } )->orWhereHas ( 'saldo', function ($q) use ($search)
                        {
                            $q->where ( 'satuan', 'ilike', "%{$search}%" );
                        } )->orWhere ( 'mekanik', 'ilike', "%{$search}%" );
                        if ( is_numeric ( str_replace ( [ ',', '.' ], '', $search ) ) )
                        {
                            $numericSearch = str_replace ( [ ',', '.' ], '', $search );
                            $q->orWhere ( 'quantity', 'ilike', "%{$numericSearch}%" )->orWhereHas ( 'saldo', function ($q) use ($numericSearch)
                            {
                                $q->where ( 'harga', 'ilike', "%{$numericSearch}%" );
                            } )->orWhereRaw ( '(
                                  SELECT s.harga * apb.quantity 
                                  FROM saldo s 
                                  WHERE s.id = apb.id_saldo
                              ) LIKE ?', [ "%{$numericSearch}%" ] );
                        }
                    } );
                }
            } );
        }
        $uniqueValues            = $this->getUniqueValues ( $query );
        $query                   = $this->applyFilters ( $query, request () );
        $totalQuery              = clone $query;
        $totalAmount             = DB::table ( 'apb' )->join ( 'saldo', 'apb.id_saldo', '=', 'saldo.id' )->where ( 'apb.id_proyek', $id_proyek )->where ( 'apb.tipe', $tipe )->when ( $tipe === 'mutasi-proyek', function ($query)
        {
            $query->where ( function ($q)
            {
                $q->where ( 'apb.status', 'accepted' )->orWhereNull ( 'apb.status' );
            } );
        } )->sum ( DB::raw ( 'apb.quantity * saldo.harga' ) );
        $proyek                  = Proyek::with ( "users" )->findOrFail ( $id_proyek );
        $alats                   = AlatProyek::where ( 'id_proyek', $id_proyek )->get ();
        $spbs                    = $this->getFilteredSpbs ( $id_proyek );
        $spareparts              = Saldo::where ( 'quantity', '>', 0 )->with ( [ 'masterDataSparepart', 'atb' ] )->whereHas ( 'atb', function ($query) use ($tipe)
        {
            $query->where ( 'tipe', $tipe );
        } )->whereHas ( 'atb', function ($query) use ($id_proyek)
        {
            $query->where ( 'id_proyek', $id_proyek );
        } )->get ()->sortBy ( 'atb.tanggal' );
        $sparepartsForMutasi     = Saldo::where ( 'quantity', '>', 0 )->with ( [ 'masterDataSparepart', 'atb' ] )->whereHas ( 'atb', function ($query) use ($tipe, $id_proyek)
        {
            $query->where ( 'id_proyek', $id_proyek );
            if ( $tipe !== 'mutasi-proyek' )
            {
                $query->where ( 'tipe', $tipe );
            }
        } )->get ()->sortBy ( 'atb.tanggal' );
        $TableData               = $query->orderBy ( 'tanggal', 'desc' )->orderBy ( 'updated_at', 'desc' )->orderBy ( 'id', 'desc' )->paginate ( $perPage )->withQueryString ();
        $TableData->total_amount = $totalAmount;
        $user                    = Auth::user ();
        $proyeksQuery            = Proyek::with ( "users" );
        if ( $user->role === 'koordinator_proyek' )
        {
            $proyeksQuery->whereHas ( 'users', function ($query) use ($user)
            {
                $query->where ( 'users.id', $user->id );
            } );
        }
        $proyeks = $proyeksQuery->orderBy ( "updated_at", "desc" )->orderBy ( "id", "desc" )->get ();
        return view ( "dashboard.apb.apb", [ "proyek" => $proyek, "alats" => $alats, "proyeks" => $proyeks, "spbs" => $spbs, "spareparts" => $spareparts, "sparepartsForMutasi" => $sparepartsForMutasi, "headerPage" => $proyek->nama, "page" => $pageTitle, "tipe" => $tipe, "TableData" => $TableData, "search" => $search, "uniqueValues" => $uniqueValues,] );
    }
    private function getFilteredSpbs ( $id_proyek )
    {
        $rkbs = RKB::with ( "spbs.linkSpbDetailSpb.detailSpb" )->where ( 'id_proyek', $id_proyek )->get ();
        $spbs = collect ();
        foreach ( $rkbs as $rkb )
        {
            $filteredSpbs = $rkb->spbs->filter ( function ($spb)
            {
                $hasRemainingQuantity = $spb->linkSpbDetailSpb->some ( function ($link)
                {
                    return $link->detailSpb->quantity_belum_diterima > 0;
                } );
                return $hasRemainingQuantity && ( ( ! $spb->is_addendum && ! isset ( $spb->id_spb_original ) ) || ( $spb->is_addendum && isset ( $spb->id_spb_original ) ) );
            } );
            $spbs         = $spbs->merge ( $filteredSpbs );
        }
        return $spbs;
    }
    public function store ( Request $request )
    {
        $validated = $request->validate ( [ 'tanggal' => 'required|date', 'id_proyek' => 'required|exists:proyek,id', 'id_alat' => 'required|exists:alat_proyek,id', 'id_saldo' => 'required|exists:saldo,id', 'quantity' => 'required|integer|min:1', 'tipe' => 'required|string', 'mekanik' => 'required|string|max:255' ] );
        try
        {
            DB::beginTransaction ();
            $saldo               = Saldo::find ( $request->id_saldo );
            $masterDataSparepart = $saldo->masterDataSparepart;
            if ( $saldo->quantity < $request->quantity )
            {
                throw new \Exception( 'Stok sparepart tidak mencukupi.' );
            }
            $apb = APB::create ( [ 'tanggal' => $request->tanggal, 'tipe' => $request->tipe, 'mekanik' => $request->mekanik, 'quantity' => $request->quantity, 'id_saldo' => $saldo->id, 'id_proyek' => $request->id_proyek, 'id_master_data_sparepart' => $masterDataSparepart->id, 'id_master_data_supplier' => $saldo->id_master_data_supplier, 'id_alat_proyek' => $request->id_alat ] );
            $saldo->decrementQuantity ( $request->quantity );
            DB::commit ();
            return redirect ()->back ()->with ( 'success', 'Data APB berhasil ditambahkan.' );
        }
        catch ( \Exception $e )
        {
            DB::rollBack ();
            return redirect ()->back ()->with ( 'error', 'Gagal menambahkan data APB: ' . $e->getMessage () );
        }
    }
    public function mutasi_store ( Request $request )
    {
        $validated = $request->validate ( [ 'tanggal' => 'required|date', 'id_proyek' => 'required|exists:proyek,id', 'id_proyek_tujuan' => 'required|exists:proyek,id|different:id_proyek', 'id_saldo' => 'required|exists:saldo,id', 'quantity' => 'required|integer|min:1', 'tipe' => 'required|string', 'keterangan' => 'nullable|string' ] );
        try
        {
            DB::beginTransaction ();
            $saldo             = Saldo::findOrFail ( $request->id_saldo );
            $pendingQuantity   = APB::where ( 'id_saldo', $saldo->id )->where ( 'tipe', 'mutasi-proyek' )->where ( 'status', 'pending' )->sum ( 'quantity' );
            $availableQuantity = $saldo->quantity - $pendingQuantity;
            if ( $availableQuantity < $request->quantity )
            {
                throw new \Exception( 'Stok sparepart tidak mencukupi. Sisa stok yang tersedia: ' . $availableQuantity . ' ' . $saldo->masterDataSparepart->satuan );
            }
            $newAPB = APB::create ( [ 'tanggal' => $request->tanggal, 'tipe' => $request->tipe, 'quantity' => $request->quantity, 'status' => 'pending', 'id_saldo' => $saldo->id, 'id_proyek' => $request->id_proyek, 'id_tujuan_proyek' => $request->id_proyek_tujuan, 'id_master_data_sparepart' => $saldo->id_master_data_sparepart, 'id_master_data_supplier' => $saldo->id_master_data_supplier, 'keterangan' => $request->keterangan ] );
            ATB::create ( [ 'tanggal' => $request->tanggal, 'tipe' => 'mutasi-proyek', 'quantity' => null, 'harga' => $saldo->harga, 'id_proyek' => $request->id_proyek_tujuan, 'id_asal_proyek' => $request->id_proyek, 'id_apb_mutasi' => $newAPB->id, 'id_spb' => null, 'id_detail_spb' => null, 'id_master_data_sparepart' => $saldo->id_master_data_sparepart, 'id_master_data_supplier' => $saldo->id_master_data_supplier ] );
            DB::commit ();
            return redirect ()->back ()->with ( 'success', 'Mutasi sparepart berhasil dibuat dan menunggu persetujuan.' );
        }
        catch ( \Exception $e )
        {
            DB::rollBack ();
            return redirect ()->back ()->with ( 'error', 'Gagal melakukan mutasi: ' . $e->getMessage () );
        }
    }
    public function mutasi_destroy ( $id )
    {
        try
        {
            DB::beginTransaction ();
            $apb = APB::findOrFail ( $id );
            if ( $apb->tipe !== 'mutasi-proyek' )
            {
                $apb->saldo->incrementQuantity ( $apb->quantity );
            }
            $apb->atbMutasi->delete ();
            $apb->delete ();
            DB::commit ();
            return redirect ()->back ()->with ( 'success', 'Data APB berhasil dihapus.' );
        }
        catch ( \Exception $e )
        {
            DB::rollBack ();
            return redirect ()->back ()->with ( 'error', 'Gagal menghapus data APB: ' . $e->getMessage () );
        }
    }
    public function destroy ( $id )
    {
        try
        {
            DB::beginTransaction ();
            $apb = APB::findOrFail ( $id );
            if ( $apb->tipe !== 'mutasi-proyek' )
            {
                $apb->saldo->incrementQuantity ( $apb->quantity );
            }
            $apb->delete ();
            DB::commit ();
            return redirect ()->back ()->with ( 'success', 'Data APB berhasil dihapus.' );
        }
        catch ( \Exception $e )
        {
            DB::rollBack ();
            return redirect ()->back ()->with ( 'error', 'Gagal menghapus data APB: ' . $e->getMessage () );
        }
    }
}