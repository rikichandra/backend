<?php

namespace App\Http\Controllers;

use App\Models\KategoriProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KategoriProdukController extends Controller
{



    public function index(Request $request)
    {

        try {

            $perPage = $request->query('per_page', 10);
            $s  = $request->query('s', '');
            $query = KategoriProduk::query();

            if ($s) {
                $query->where('nama_kategori', 'like', '%' . $s . '%')
                    ->orWhere('deskripsi_kategori', 'like', '%' . $s . '%');
            }

            $kategoriProduks = $query->paginate($perPage);


            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengambil data kategori produk',
                'data' => $kategoriProduks
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching kategori produk: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data kategori produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nama_kategori' => 'required|string|max:255',
                'deskripsi_kategori' => 'nullable|string',
            ]);

            $kategoriProduk = KategoriProduk::create($validatedData);

            return response()->json([
                'status' => true,
                'message' => 'Kategori produk berhasil dibuat',
                'data' => $kategoriProduk
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating kategori produk: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal membuat kategori produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'nama_kategori' => 'sometimes|required|string|max:255',
                'deskripsi_kategori' => 'nullable|string',
            ]);

            $kategoriProduk = KategoriProduk::findOrFail($id);
            $kategoriProduk->update($validatedData);

            return response()->json([
                'status' => true,
                'message' => 'Kategori produk berhasil diperbarui',
                'data' => $kategoriProduk
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating kategori produk: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui kategori produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $kategoriProduk = KategoriProduk::findOrFail($id);
            $kategoriProduk->delete();

            return response()->json([
                'status' => true,
                'message' => 'Kategori produk berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting kategori produk: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus kategori produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
