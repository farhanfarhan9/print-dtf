<?php

namespace App\Livewire\Reject;

use App\Models\RejectProduct;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RejectProductsExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AllRejectProduct extends Component
{
    use WithPagination;

    public $search = '';
    public $startDate;
    public $endDate;
    public $perPage = 15;
    public $loadingTime = 0;
    public $ramUsage = 0;
    public $dataSize = 0;
    public $isLoading = false;
    protected $paginationTheme = 'tailwind';

    protected $queryString = [
        'search' => ['except' => ''],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'perPage' => ['except' => 15],
    ];

    // Disable automatic Livewire rendering on property updates
    protected $disableRenderOnPropertyUpdate = true;

    public function updatedSearch()
    {
        $this->resetPage();
        Cache::forget($this->getCacheKey());
    }

    public function updatedStartDate()
    {
        $this->resetPage();
        Cache::forget($this->getCacheKey());
    }

    public function updatedEndDate()
    {
        $this->resetPage();
        Cache::forget($this->getCacheKey());
    }

    public function updatedPerPage()
    {
        $this->resetPage();
        Cache::forget($this->getCacheKey());
    }

    private function getCacheKey()
    {
        $page = request()->query('page', 1);
        return 'reject_products_' . $this->search . '_' . $this->startDate . '_' . $this->endDate . '_' . $this->perPage . '_' . $page;
    }

    public function exportExcel()
    {
        $this->isLoading = true;

        // Get all reject product data without pagination
        $rejectProducts = $this->getRejectProductsData(false);

        // Generate filename with date
        $filename = 'reject_products_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';

        $this->isLoading = false;

        // Return the download response
        return Excel::download(new RejectProductsExport($rejectProducts), $filename);
    }

    private function getRejectProductsData($paginate = true)
    {
        // Start measuring time and memory usage
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        // If paginating, try to get from cache first
        if ($paginate) {
            $cacheKey = $this->getCacheKey();
            if (Cache::has($cacheKey)) {
                $result = Cache::get($cacheKey);

                // Calculate metrics for cached result
                $endTime = microtime(true);
                $endMemory = memory_get_usage();
                $this->loadingTime = round($endTime - $startTime, 2);
                $this->ramUsage = round(($endMemory - $startMemory) / 1024 / 1024, 2);
                $this->dataSize = round(strlen(serialize($result->items())) / 1024, 2);

                return $result;
            }
        }

        // Create a base query with eager loading to avoid N+1 problems
        $query = RejectProduct::with('product');

        // Apply date filters if provided
        if ($this->startDate && $this->endDate) {
            $start = Carbon::createFromFormat('Y-m-d', $this->startDate)->startOfDay();
            $end = Carbon::createFromFormat('Y-m-d', $this->endDate)->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        }

        // Apply search filter if provided
        if ($this->search) {
            $query->whereHas('product', function ($q) {
                $q->where('nama_produk', 'like', '%' . $this->search . '%');
            });
        }

        // Order by created_at
        $query->orderBy('created_at', 'desc');

        // Execute the query with or without pagination
        $result = $paginate ? $query->paginate($this->perPage) : $query->get();

        // Cache the paginated result for 5 minutes
        if ($paginate) {
            Cache::put($this->getCacheKey(), $result, now()->addMinutes(5));
        }

        // End measuring time and memory usage
        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        // Calculate performance metrics
        $this->loadingTime = round($endTime - $startTime, 2);
        $this->ramUsage = round(($endMemory - $startMemory) / 1024 / 1024, 2);
        $this->dataSize = $paginate ?
            round(strlen(serialize($result->items())) / 1024, 2) :
            round(strlen(serialize($result)) / 1024, 2);

        return $result;
    }

    public function render()
    {
        $this->isLoading = true;
        $rejecteds = $this->getRejectProductsData();
        $this->isLoading = false;

        return view('livewire.reject.all-reject-product', [
            'rejecteds' => $rejecteds
        ]);
    }
}
