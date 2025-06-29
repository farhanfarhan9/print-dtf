<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Products;
use Illuminate\Support\Facades\Gate;

class ProductAdd extends Component
{
    public $productName;
    public $stock;
    public $priceRanges = [];
    public $priceRetailRanges = [];

    public function mount()
    {
        // Initialize with one price range
        $this->priceRanges[] = ['start' => 0, 'end' => 0, 'price' => 0];
        $this->priceRetailRanges[] = ['start' => 0, 'end' => 0, 'price' => 0];
    }

    public function addPriceRange()
    {
        $this->priceRanges[] = ['start' => 0, 'end' => 0, 'price' => 0];
    }

    public function addPriceRangeRetail()
    {
        $this->priceRetailRanges[] = ['start' => 0, 'end' => 0, 'price' => 0];
    }

    public function removePriceRange($index)
    {
        unset($this->priceRanges[$index]);
        $this->priceRanges = array_values($this->priceRanges); // Re-index the array
    }

    public function removePriceRangeRetail($index)
    {
        unset($this->priceRetailRanges[$index]);
        $this->priceRetailRanges = array_values($this->priceRetailRanges); // Re-index the array
    }
    public function save()
    {
        // dd($this->priceRanges);
        // Validation and product creation logic
        $product = Products::create([
            'nama_produk' => $this->productName,
            'stok' => $this->stock,
            'detail_harga' => json_encode($this->priceRanges),
            'detail_harga_retail' => json_encode($this->priceRetailRanges),
        ]);

        session()->flash('productCreated', ['Sukses', 'Berhasil menambahkan data', 'success']);
        // Reset the form or give some response
        session()->flash('productCreated', ['Sukses', 'Berhasil menambahkan data', 'success']);
        $this->redirect('/products', navigate: true);
    }

    public function render()
    {
        return view('livewire.product-add');
    }
}
