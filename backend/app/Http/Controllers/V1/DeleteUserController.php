<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\DeleteUserRequest;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class DeleteUserController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(DeleteUserRequest $request)
    {
        try {
            $user = Auth::user();
            abort_if(!$user, 401, 'Unauthenticated');

            DB::transaction(function () use ($user) {
                $user->notifications()->delete();
                Activity::causedBy($user)->delete();

                $user->delete();
            });

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->noContent();

        } catch (\Exception $e) {
            DB::rollBack();
            logger($e->getMessage());
        }
    }
}
