<?php
// app/Http/Controllers/ViewKodePosController.php
namespace App\Http\Controllers;

use App\Models\EcProvinsi;
use App\Models\EcKota;
use App\Models\EcKecamatan;
use App\Models\EcPostal;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ViewKodePosController extends Controller
{
    public function index(Request $request): Collection
    {
        return EcProvinsi::query()
            ->select('prov_id', 'prov_name')
            ->orderBy('prov_id')
            ->when(
                $request->search,
                fn (Builder $query) => $query
                    ->where('prov_name', 'like', "%{$request->search}%")
            )
            ->when(
                $request->exists('selected'),
                fn (Builder $query) => $query->whereIn('prov_id', $request->input('selected', [])),
                fn (Builder $query) => $query->limit(34)
            )
            ->get()
            ->map(function (EcProvinsi $provinsi) {
                // Perform any transformation if needed
                return $provinsi;
            });
    }


    // Fetch provinces
    // public function getProvinces()
    // {
    //     return EcProvinsi::select('prov_id as id', 'prov_name as name')->orderBy('prov_name')->get();
    // }
    public function getProvinces(Request $request): Collection
    {
        return EcProvinsi::query()
            ->select('prov_id as id', 'prov_name as name')
            ->orderBy('prov_name')
            ->when(
                $request->search,
                fn (Builder $query) => $query
                    ->where('prov_name', 'like', "%{$request->search}%")
            )
            ->when(
                $request->exists('selected'),
                fn (Builder $query) => $query->whereIn('prov_id', $request->input('selected', [])),
                fn (Builder $query) => $query->limit(34)
            )
            ->get()
            ->map(function (EcProvinsi $ecProvinsi) {
                return $ecProvinsi;
            });
    }

    // Fetch Specific provinces
    public function getProvincesData($province = null)
    {
        $query = EcProvinsi::query();

        if (!is_null($province)) {
            $query->where('prov_id', $province);
        }

        return $query->select('prov_id as id', 'prov_name as name')
            ->orderBy('prov_name')
            ->get();
    }

    // Fetch cities based on province
    // public function getCities($province)
    // {
    //     return EcKota::where('prov_id', $province)
    //                  ->select('city_id as id', 'city_name as name')
    //                  ->orderBy('city_name')
    //                  ->get();
    // }

    public function getCities($province, Request $request): Collection
    {
        return EcKota::query()
            ->where('prov_id', $province)
            ->select('city_id as id', 'city_name as name')
            ->orderBy('city_name')
            ->when(
                $request->search,
                fn (Builder $query) => $query
                    ->where('city_name', 'like', "%{$request->search}%")
            )
            ->when(
                $request->exists('selected'),
                fn (Builder $query) => $query->whereIn('city_id', $request->input('selected', [])),
                fn (Builder $query) => $query->limit(34)
            )
            ->get()
            ->map(function (EcKota $ecKota) {
                return $ecKota;
            });
    }

    // Fetch districts based on city
    // public function getDistricts($city)
    // {
    //     return EcKecamatan::where('city_id', $city)
    //                      ->select('dis_id as id', 'dis_name as name')
    //                      ->orderBy('dis_name')
    //                      ->get();
    // }

    public function getDistricts($city, Request $request): Collection
    {
        return EcKecamatan::query()
            ->where('city_id', $city)
            ->select('dis_id as id', 'dis_name as name')
            ->orderBy('dis_name')
            ->when(
                $request->search,
                fn (Builder $query) => $query
                    ->where('dis_name', 'like', "%{$request->search}%")
            )
            ->when(
                $request->exists('selected'),
                fn (Builder $query) => $query->whereIn('dis_id', $request->input('selected', [])),
                fn (Builder $query) => $query->limit(34)
            )
            ->get()
            ->map(function (EcKecamatan $ecKecamatan) {
                return $ecKecamatan;
            });
    }

    // Fetch postal based on province,city and district
    // public function getPostal($province, $city)
    // {
    //     return EcPostal::where('prov_id', $province)
    //     ->where('city_id', $city)
    //     ->select('postal_code as name')
    //     ->distinct()
    //     ->groupBy('postal_code')
    //     ->orderBy('postal_code')
    //     ->get()
    //     ->map(function ($item) {
    //         return [
    //             'id' => $item->name, // or any logic to determine the ID
    //             'name' => $item->name
    //         ];
    //     });
    // }
    public function getPostal($province, $city, Request $request): Collection
    {
        return EcPostal::query()
            ->where('prov_id', $province)
            ->where('city_id', $city)
            ->select( 'postal_code as name')
            ->distinct()
            ->groupBy('postal_id', 'postal_code') // Modify GROUP BY clause
            ->orderBy('postal_code')
            ->when(
                $request->search,
                fn (Builder $query) => $query
                    ->where('postal_code', 'like', "%{$request->search}%")
            )
            ->when(
                $request->exists('selected'),
                fn (Builder $query) => $query->whereIn('postal_code', $request->input('selected', [])),
                fn (Builder $query) => $query->limit(34)
            )
            ->get()
            ->map(function (EcPostal $ecPostal) {
                return $ecPostal;
            });
    }

    // return EcKecamatan::query()
    //         ->where('city_id', $city)
    //         ->select('dis_id as id', 'dis_name as name')
    //         ->orderBy('dis_name')
    //         ->when(
    //             $request->search,
    //             fn (Builder $query) => $query
    //                 ->where('dis_name', 'like', "%{$request->search}%")
    //         )
    //         ->when(
    //             $request->exists('selected'),
    //             fn (Builder $query) => $query->whereIn('dis_id', $request->input('selected', [])),
    //             fn (Builder $query) => $query->limit(34)
    //         )
    //         ->get()
    //         ->map(function (EcKecamatan $ecKecamatan) {
    //             return $ecKecamatan;
    //         });

}
