<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kategori = Kategori::latest()->get();
        return response()->json($kategori);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'      => 'required',
            'deskripsi' => 'nullable',
        ]);

        $kategori = Kategori::create($validated);

        return response()->json(['message' => 'Kategori berhasil dibuat.', 'data' => $kategori], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $kategori = Kategori::findOrFail($id);
        return response()->json($kategori);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama'      => 'required',
            'deskripsi' => 'nullable',
        ]);

        $kategori = Kategori::findOrFail($id);
        $kategori->update($validated);

        return response()->json(['message' => 'Kategori berhasil diperbarui.', 'data' => $kategori]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->delete();

        return response()->json(['message' => 'Kategori berhasil dihapus.'], 200);
    }
}
