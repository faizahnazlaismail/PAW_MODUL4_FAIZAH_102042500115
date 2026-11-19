<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman katalog buku dengan fitur pencarian, filter, sorting, dan paginasi.
     */

    //TODO 3
   
public function index(Request $request)
{
    // Query pencarian buku
    $query = Buku::query();

    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('judul', 'like', "%{$search}%")
              ->orWhere('penulis', 'like', "%{$search}%")
              ->orWhere('penerbit', 'like', "%{$search}%");
        });
    }

    // Filter penerbit
    if ($request->filled('penerbit')) {
        $query->where('penerbit', $request->penerbit);
    }

    // Filter ketersediaan stok
    if ($request->status === 'tersedia') {
        $query->where('stok', '>', 0);
    } elseif ($request->status === 'habis') {
        $query->where('stok', '<=', 0);
    }

    // Pengurutan data
    $sort = $request->input('sort', 'latest');

    switch ($sort) {
        case 'judul':
            $query->orderBy('judul', 'asc');
            break;
        case 'tahun':
            $query->orderBy('tahun_terbit', 'desc');
            break;
        case 'stok':
            $query->orderBy('stok', 'desc');
            break;
        default:
            $query->latest();
            break;
    }

    // Pagination 8 data
    $buku = $query->paginate(8)->withQueryString();

    // Daftar penerbit untuk dropdown
    $listPenerbit = Buku::select('penerbit')
        ->distinct()
        ->orderBy('penerbit')
        ->pluck('penerbit');

    return view('home', compact('buku', 'listPenerbit'));
}

    /**
     * Menampilkan halaman detail informasi sebuah buku untuk publik/mahasiswa.
     */
    public function show(Buku $buku)
    {
        // TODO 3: Kembalikan view 'home_show' membawa data $buku
        return view('home_show', compact('buku'));
    }
}
