<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\TemporaryFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $temporaryFile = TemporaryFile::where('folder', $request->avatar)->first();
        if ($temporaryFile) {
            // Delete old images if they exist
            if ($request->user()->getFirstMediaUrl('avatars')) {
                $request->user()->clearMediaCollection('avatars');
            }

            $request->user()
                ->addMedia(storage_path('app/public/avatars/tmp/'.$request->avatar.'/'.$temporaryFile->filename))
                ->toMediaCollection('avatars', 'avatars');

            // Delete the temporary file record
            Storage::deleteDirectory('avatars/tmp/'.$request->avatar);
            $temporaryFile->delete();
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
