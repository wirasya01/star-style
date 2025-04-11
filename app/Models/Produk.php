<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produks'; // Ensure the table name is correct

    protected $fillable = [
        'nama', 'deskripsi', 'stok', 'harga', 'kategori_id', 'gambar', 'ukuran',
    ];

    protected $casts = [
        'gambar' => 'array',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function keranjangs()
    {
        return $this->hasMany(Keranjang::class, 'produk_id');
    }

    public function detailPesanans()
    {
        return $this->hasMany(DetailPesanan::class, 'produk_id');
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($produk) {
            $produk->deleteImage();
        });
    }

    public function deleteImage()
    {
        if (is_array($this->gambar)) {
            foreach ($this->gambar as $image) {
                Storage::disk('public')->delete('images/produk/' . $image);
            }
        } else {
            Storage::disk('public')->delete('storage/images/produk/' . $this->gambar);
        }
    }
}
