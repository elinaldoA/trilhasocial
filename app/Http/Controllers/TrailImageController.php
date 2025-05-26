<?php

namespace App\Http\Controllers;

use App\Models\TrailImage;
use Illuminate\Support\Facades\Storage;

class TrailImageController extends Controller
{
    public function destroy(TrailImage $image)
    {
        //$this->authorize('delete', $image->trail);

        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back()->with('success', 'Imagem removida com sucesso.');
    }
}
