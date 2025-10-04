<?php

namespace App\Http\Controllers;

use App\Models\DetailTransaksi;
use Illuminate\Http\Request;

use App\Models\Transaksi;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    // List riwayat transaksi
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $s = $request->get('s', '');
            $query = Transaksi::with(['user', 'detailTransaksis.produk']);
            if($s) {
                $query->whereHas('detailTransaksis.produk', function($q) use ($s) {
                    $q->where('nama_produk', 'like', '%' . $s . '%')
                      ->orWhere('deskripsi_produk', 'like', '%' . $s . '%');
                });
            }
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }
            $transaksis = $query->orderBy('created_at', 'desc')->paginate($perPage);
            return response()->json([
                'status' => true,
                'data' => $transaksis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Buat transaksi multi-produk
    public function store(Request $request)
    {
        $request->validate([
            'jenis_transaksi' => 'required|in:in,out',
            'produk' => 'required|array|min:1',
            'produk.*.produk_id' => 'required|exists:produks,id',
            'produk.*.jumlah_produk' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $userId = $request->user()->id;
            $jenisTransaksi = $request->jenis_transaksi;
            $produkList = $request->produk;

            // Buat satu transaksi utama
            $transaksi = Transaksi::create([
                'user_id' => $userId,
                'jenis_transaksi' => $jenisTransaksi,
            ]);

            $detailTransaksis = [];
            foreach ($produkList as $item) {
                $produk = Produk::findOrFail($item['produk_id']);
                $jumlah = $item['jumlah_produk'];

                if ($jenisTransaksi === 'out') {
                    if ($produk->stok_produk < $jumlah) {
                        throw new \Exception("Stok produk '{$produk->nama_produk}' tidak mencukupi.");
                    }
                    $produk->stok_produk -= $jumlah;
                } else {
                    $produk->stok_produk += $jumlah;
                }
                $produk->save();

                $detailTransaksi = DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $produk->id,
                    'jumlah_produk' => $jumlah,
                ]);
                $detailTransaksis[] = $detailTransaksi;
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Transaksi berhasil dibuat',
                'data' => [
                    'transaksi' => $transaksi,
                    'detail_transaksis' => $detailTransaksis
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $transaksi = Transaksi::findOrFail($id);
            DB::beginTransaction();
            
            if ($transaksi->jenis_transaksi === 'out') {
                foreach ($transaksi->detailTransaksis as $detail) {
                    $produk = Produk::findOrFail($detail->produk_id);
                    $produk->stok_produk += $detail->jumlah_produk;
                    $produk->save();
                }
            } else if ($transaksi->jenis_transaksi === 'in') {                
                foreach ($transaksi->detailTransaksis as $detail) {
                    $produk = Produk::findOrFail($detail->produk_id);
                    if ($produk->stok_produk < $detail->jumlah_produk) {
                        throw new \Exception("Stok produk '{$produk->nama_produk}' tidak mencukupi untuk menghapus transaksi.");
                    }
                    $produk->stok_produk -= $detail->jumlah_produk;
                    $produk->save();
                }
            }

            $transaksi->delete();
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Transaksi berhasil dihapus',
                'data' => null,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

}
