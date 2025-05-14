<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LampiranController extends Controller
{
    function showDokumen($kategori, $filename)
    {
        $path = 'storage/' . $kategori . '/' . $filename;

        $ekstensi = pathinfo($filename, PATHINFO_EXTENSION);
        $type = '';

        if (in_array($ekstensi, ['jpg', 'jpeg', 'png', 'gif', 'bmp'])) {
            $type = 'image';
        } elseif (in_array($ekstensi, ['pdf'])) {
            $type = 'pdf';
        }

        return view('lampiran', compact('path', 'filename', 'type'));
    }
}
