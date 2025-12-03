<?php

namespace App\Http\Controllers;

use App\Models\TemporaryFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function store(Request $request)
    {
        // Determine file field and storage path
        $fileField = null;
        $storagePath = null;
        
        if ($request->hasFile('image')) {
            $fileField = 'image';
            $storagePath = 'posts/tmp/';
        } elseif ($request->hasFile('avatar')) {
            $fileField = 'avatar';
            $storagePath = 'avatars/tmp/';
        }

        if ($fileField && $storagePath) {
            $file = $request->file($fileField);
            $filename = $file->getClientOriginalName();
            $folder = uniqid().'-'.now()->timestamp;
            $file->storeAs($storagePath.$folder, $filename);

            TemporaryFile::create([
                'folder' => $folder,
                'filename' => $filename,
            ]);

            return $folder;
        }

        return '';
    }

    public function destroy()
    {
        $tmp_file = TemporaryFile::where('folder', request()->getContent())->first();
        if ($tmp_file) {
            // Try both possible storage paths
            $postsPath = 'posts/tmp/'.$tmp_file->folder;
            $avatarsPath = 'avatars/tmp/'.$tmp_file->folder;
            
            if (Storage::exists($postsPath)) {
                Storage::deleteDirectory($postsPath);
            } elseif (Storage::exists($avatarsPath)) {
                Storage::deleteDirectory($avatarsPath);
            }
            
            $tmp_file->delete();

            return response('');
        }
    }
}
