<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ekspedisi;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ExpeditionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Collection
    {
        return ekspedisi::query()
            ->select('id', 'nama_ekspedisi', 'ongkir')
            ->orderBy('nama_ekspedisi')
            ->when(
                $request->search,
                fn (Builder $query) => $query
                    ->where('nama_ekspedisi', 'like', "%{$request->search}%")
            )
            ->when(
                $request->exists('selected'),
                fn (Builder $query) => $query->whereIn('id', $request->input('selected', [])),
                fn (Builder $query) => $query->limit(10)
            )
            ->get()
            ->map(function (ekspedisi $ekspedisi) {
                return $ekspedisi;
            });
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ekspedisi $ekspedisi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ekspedisi $ekspedisi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ekspedisi $ekspedisi)
    {
        //
    }
}
