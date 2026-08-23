<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DicebearSeedController extends Controller
{
    public function update()
    {
        $user = Auth::user();

        $user->seedDiceBear();

        return response()->json($user);
    }

    public function destroy()
    {
        $user = Auth::user();
        $user->dicebear_seed = null;
        $user->save();

        return response()->json($user);
    }
}
