<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    // Menampilkan daftar dokumen
    public function index()
    {
        return Inertia::render('Dashboard',[
            'documents' => \App\Models\Document::with('user')->latest()->get()
        ]);
    }

    // Proses membuat dokumen baru
    public function store(Request $request)
    {
        $request->validate(['title' => 'required|string|max:255']);

        $document = Auth::user()->documents()->create([
            'title' => $request->title,
            'slug'  => Str::slug($request->title) . '-' . Str::random(5),
            'content' => '', // awal nya kosong
        ]);

        return redirect()->route('documents.show', $document->slug);
    }

    // Halaman editor (Nanti akan dipasang Tiptap di sini)
    public function show(Document $document)
    {
        return Inertia::render('Editor', [
            'document' => $document
        ]);
    }

    public function update(Request $request, Document $document)
{
    // Validasi sederhana
    $document->update([
        'content' => $request->content
    ]);

    // OPSI: Simpan juga ke tabel revisions untuk fitur Version History
    $document->revisions()->create([
        'user_id' => Auth::id(),
        'content' => $request->content,
    ]);

    return response()->json(['message' => 'Tersimpan otomatis']);
}
}
