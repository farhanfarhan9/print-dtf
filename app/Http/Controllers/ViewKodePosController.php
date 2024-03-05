<?php
// app/Http/Controllers/ViewKodePosController.php
namespace App\Http\Controllers;

use App\Models\ViewKodePos;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ViewKodePosController extends Controller
{
    public function index(Request $request): Collection
    {
        return ViewKodePos::query()
        ->select('id', 'kota')
        ->orderBy('kota')
        ->when(
            $request->search,
            fn (Builder $query) => $query
                ->where('kota', 'like', "%{$request->search}%")
        )
        ->when(
            $request->exists('selected'),
            fn (Builder $query) => $query->whereIn('id', $request->input('selected', [])),
            fn (Builder $query) => $query->limit(20)
        )
        ->get()
        ->map(function (ViewKodePos $user) {
            return $user;
        });
    }
}
