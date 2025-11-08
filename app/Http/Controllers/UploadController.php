<?php

namespace App\Http\Controllers;

use App\Models\TemporaryFile;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function store(Request $request)
    {

        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $filename = $file->getClientOriginalName();
            $folder = uniqid().'-'.now()->timestamp;
            $file->storeAs('posts/tmp/'.$folder, $filename);

            TemporaryFile::create([
                'folder' => $folder,
                'filename' => $filename,
            ]);

            return $folder;
        }

        return '';
    }

    public function destroy(TemporaryFile $temporaryFile)
    {
        dd($temporaryFile);
        $temporaryFile->delete();

        return response()->json(['message' => 'Temporary file deleted successfully']);
    }
}
