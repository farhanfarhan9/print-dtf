<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Products;

class ProductAdd extends Component
{
    public $productName;
    public $stock;
    public $priceRanges = [];

    public function mount()
    {
        // Initialize with one price range
        $this->priceRanges[] = ['start' => 0, 'end' => 0, 'price' => 0];
    }

    public function addPriceRange()
    {
        $this->priceRanges[] = ['start' => 0, 'end' => 0, 'price' => 0];
    }

    public function removePriceRange($index)
    {
        unset($this->priceRanges[$index]);
        $this->priceRanges = array_values($this->priceRanges); // Re-index the array
    }

    public function save()
    {
        // Validation and product creation logic
        $product = Products::create([
            'nama_produk' => $this->productName,
            'stok' => $this->stock,
            'detail_harga' => json_encode($this->priceRanges),
        ]);

        session()->flash('productCreated',['Sukses', 'Berhasil menambahkan data', 'success']);
        // Reset the form or give some response
        return redirect('/products');
    }

    public function render()
    {
        return view('livewire.product-add')->layout('layouts.app');
    }
}
