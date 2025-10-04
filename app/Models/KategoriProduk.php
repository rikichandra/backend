<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriProduk extends Model
{
    protected $fillable = [
        'nama_kategori',
        'deskripsi_kategori',
    ];

    public function produks()
    {
        return $this->hasMany(Produk::class, 'kategori_produk_id');
    }
}
