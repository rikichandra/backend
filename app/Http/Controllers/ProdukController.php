<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        try {

            $perPage = $request->query('per_page', 10);
            $s = $request->query('s', '');
            $query = Produk::with('kategoriProduk');
            if ($s) {
                $query->where('nama_produk', 'like', '%' . $s . '%')
                    ->orWhere('deskripsi_produk', 'like', '%' . $s . '%');
            }
            $produks = $query->paginate($perPage);
            return response()->json([
                'status'  => true,
                'message' => 'Products retrieved successfully',
                'data'    => $produks,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to retrieve products',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'kategori_produk_id' => 'required|exists:kategori_produks,id',
                'nama_produk'        => 'required|string|max:255',
                'deskripsi_produk'   => 'nullable|string',
                'gambar_produk'      => 'nullable|image|max:2048',
                'stok_produk'        => 'required|integer|min:0',
            ]);

            if ($request->hasFile('gambar_produk')) {
                $path = $request->file('gambar_produk')->store('produk_images', 'public');
                $validatedData['gambar_produk'] = $path;
            }

            $produk = Produk::create($validatedData);

            return response()->json([
                'status'  => true,
                'message' => 'Product created successfully',
                'data'    => $produk,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to create product',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $produk = Produk::findOrFail($id);

            $validatedData = $request->validate([
                'kategori_produk_id' => 'sometimes|required|exists:kategori_produks,id',
                'nama_produk'        => 'sometimes|required|string|max:255',
                'deskripsi_produk'   => 'nullable|string',
                'gambar_produk'      => 'nullable|image|max:2048',
                'stok_produk'        => 'sometimes|required|integer|min:0',
            ]);

            if ($request->hasFile('gambar_produk')) {
                $path = $request->file('gambar_produk')->store('produk_images', 'public');
                $validatedData['gambar_produk'] = $path;
            }

            $produk->update($validatedData);

            return response()->json([
                'status'  => true,
                'message' => 'Product updated successfully',
                'data'    => $produk,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to update product',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $produk = Produk::findOrFail($id);

            if($produk->gambar_produk){
                Storage::disk('public')->delete($produk->gambar_produk);
            }

            $produk->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Product deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to delete product',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
