<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $fillable = [
        'kategori_produk_id',
        'nama_produk',
        'deskripsi_produk',
        'gambar_produk',
        'stok_produk',
    ];
    
    protected $appends = ['gambar_produk_url'];

    public function kategoriProduk()
    {
        return $this->belongsTo(KategoriProduk::class, 'kategori_produk_id');
    }

    public function getGambarProdukUrlAttribute()
    {
        return $this->gambar_produk ? asset('storage/' . $this->gambar_produk) : null;
    }
}
