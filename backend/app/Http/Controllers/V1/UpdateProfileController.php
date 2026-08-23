<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\UpdateProfileRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProfileController extends Controller
{
    public function __invoke(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        $validated = $request->when(
            $user->google_id,
            fn($r) => $r->only('name'),
            fn($r) => $r->validated()
        );

        $user->update($validated);

        if ($user->wasChanged(['email'])) {
            $user->email_verified_at = null;
            $user->save();
            $user->sendEmailVerificationNotification();
        }


        return response()->json($user);
    }
}
