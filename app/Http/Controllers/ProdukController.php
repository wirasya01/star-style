<?php
namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    public $fillable = [
        'nama', 'deskripsi', 'stok', 'harga', 'kategori_id', 'gambar', 'ukuran',
    ];
    public $visible = [
        'nama', 'deskripsi', 'stok', 'harga', 'kategori_id', 'gambar', 'ukuran',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produks = Produk::latest()->get();
        return view('admin.produk.index', compact('produks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kategoris = Kategori::all();
        return view('admin.produk.create', compact('kategoris'));
    }

    /**
     * Store a newly created resource in storage.
     */
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
        $produk->harga       = $request->harga; // Multiply by 1000
        $produk->kategori_id = $request->kategori_id;
        $produk->ukuran      = json_encode(explode(',', $request->ukuran)); // Convert ukuran to JSON

        // upload gambar
        if ($request->hasFile('gambar')) {
            $gambarPaths = [];
            foreach ($request->file('gambar') as $gambar) {
                $filePath      = $gambar->store('images/produk', 'public');
                $gambarPaths[] = $filePath;
            }
            $produk->gambar = json_encode($gambarPaths);
        }

        $produk->save();

        return redirect()->route('produk.index')
            ->with('success', 'Produk berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $produk = Produk::findOrFail($id);
        return view('admin.produk.show', compact('produk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $produk    = Produk::findOrFail($id);
        $kategoris = Kategori::all();
        return view('admin.produk.edit', compact('produk', 'kategoris'));
    }

    /**
     * Update the specified resource in storage.
     */
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
        $produk->harga       = $request->harga; // Multiply by 1000
        $produk->kategori_id = $request->kategori_id;
        $produk->ukuran      = json_encode(explode(',', $request->ukuran)); // Convert ukuran to JSON

        // upload gambar
        if ($request->hasFile('gambar')) {
            // Delete the old images
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

        return redirect()->route('produk.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);

        // Delete the images
        if ($produk->gambar) {
            $gambarPaths = json_decode($produk->gambar);
            foreach ($gambarPaths as $gambarPath) {
                Storage::disk('public')->delete($gambarPath);
            }
        }

        $produk->delete();

        return redirect()->route('produk.index')->with('success', 'Product deleted successfully!');
    }

    public function getProducts()
    {
        $products = Product::all()->map(function ($product) {
            $product->image_url = asset('storage/' . $product->image);
            return $product;
        });
        return response()->json($products);
    }

}
