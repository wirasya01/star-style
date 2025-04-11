<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    public function index()
    {
        $produks = Produk::latest()->get()->map(function ($produk) {
            $produk->gambar = $this->getFullImageUrls($produk->gambar);
            return $produk;
        });
        return response()->json($produks);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'        => 'required|unique:produks',
            'deskripsi'   => 'nullable',
            'stok'        => 'required|integer',
            'harga'       => 'required|integer',
            'kategori_id' => 'required|exists:kategoris,id',
            'gambar.*'    => 'nullable|image|max:2048',
            'ukuran'      => 'nullable|string',
        ]);

        $produk              = new Produk();
        $produk->nama        = $request->nama;
        $produk->deskripsi   = $request->deskripsi;
        $produk->stok        = $request->stok;
        $produk->harga       = $request->harga;
        $produk->kategori_id = $request->kategori_id;
        $produk->ukuran      = json_encode(explode(',', $request->ukuran));

        if ($request->hasFile('gambar')) {
            $gambarPaths = [];
            foreach ($request->file('gambar') as $gambar) {
                $filePath      = $gambar->store('images/produk', 'public');
                $gambarPaths[] = $filePath;
            }
            $produk->gambar = json_encode($gambarPaths);
        }

        $produk->save();
        $produk->gambar = $this->getFullImageUrls($produk->gambar);

        return response()->json(['message' => 'Produk berhasil dibuat.', 'data' => $produk], 201);
    }

    public function show($id)
    {
        $produk         = Produk::findOrFail($id);
        $produk->gambar = $this->getFullImageUrls($produk->gambar);
        return response()->json($produk);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama'        => 'required|unique:produks,nama,' . $id,
            'deskripsi'   => 'nullable',
            'stok'        => 'required|integer',
            'harga'       => 'required|integer',
            'kategori_id' => 'required|exists:kategoris,id',
            'gambar.*'    => 'nullable|image|max:2048',
            'ukuran'      => 'nullable|string',
        ]);

        $produk              = Produk::findOrFail($id);
        $produk->nama        = $request->nama;
        $produk->deskripsi   = $request->deskripsi;
        $produk->stok        = $request->stok;
        $produk->harga       = $request->harga;
        $produk->kategori_id = $request->kategori_id;
        $produk->ukuran      = json_encode(explode(',', $request->ukuran));

        if ($request->hasFile('gambar')) {
            if ($produk->gambar) {
                $oldGambarPaths = json_decode($produk->gambar);
                foreach ($oldGambarPaths as $oldGambarPath) {
                    Storage::disk('public')->delete($oldGambarPath);
                }
            }

            $gambarPaths = [];
            foreach ($request->file('gambar') as $gambar) {
                $filePath      = $gambar->store('images/produk', 'public');
                $gambarPaths[] = $filePath;
            }
            $produk->gambar = json_encode($gambarPaths);
        }

        $produk->save();
        $produk->gambar = $this->getFullImageUrls($produk->gambar);

        return response()->json(['message' => 'Produk berhasil diperbarui.', 'data' => $produk]);
    }

    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);

        if ($produk->gambar) {
            $gambarPaths = json_decode($produk->gambar);
            foreach ($gambarPaths as $gambarPath) {
                Storage::disk('public')->delete($gambarPath);
            }
        }

        $produk->delete();

        return response()->json(['message' => 'Produk berhasil dihapus.']);
    }

    private function getFullImageUrls($gambar)
    {
        if (! $gambar) {
            return [];
        }

        $gambarPaths = json_decode($gambar);
        return array_map(function ($path) {
            return asset('storage/' . $path);
        }, $gambarPaths);
    }
}
