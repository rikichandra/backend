<?php

namespace App\Http\Controllers;

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
            $query = Transaksi::with(['user', 'produk']);
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }
            $transaksis = $query->orderBy('created_at', 'desc')->get();
            return response()->json([
                'success' => true,
                'data' => $transaksis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
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
            $createdTransaksi = [];

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

                $transaksi = Transaksi::create([
                    'user_id' => $userId,
                    'produk_id' => $produk->id,
                    'jenis_transaksi' => $jenisTransaksi,
                    'jumlah_produk' => $jumlah,
                ]);
                $createdTransaksi[] = $transaksi;
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibuat',
                'data' => $createdTransaksi
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
