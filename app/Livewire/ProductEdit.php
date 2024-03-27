<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Products;
use Illuminate\Support\Facades\Gate;

class ProductEdit extends Component
{
    public Products $product;
    public array $priceRanges = [];
    public $nama_produk, $stok;

    public function mount(Products $product) // Correct type hinting
    {
        $this->product = $product;
        $this->nama_produk = $product->nama_produk;
        $this->stok = $product->stok;
        $this->priceRanges = json_decode($product->detail_harga, true) ?? [];
    }

    public function addPriceRange()
    {
        $this->priceRanges[] = ['start' => 0, 'end' => 0, 'price' => 0];
    }

    public function removePriceRange($index)
    {
        unset($this->priceRanges[$index]);
        $this->priceRanges = array_values($this->priceRanges);
    }

    public function save()
    {
        // Validate input
        $this->validate([
            'nama_produk' => 'required|string|max:255',
            'stok' => 'required|numeric',
            'priceRanges.*.start' => 'required|numeric|min:0',
            'priceRanges.*.end' => 'required|numeric|min:0',
            'priceRanges.*.price' => 'required|numeric|min:0',
        ]);

        // Update product properties
        $this->product->nama_produk = $this->nama_produk;
        $this->product->stok = $this->stok;
        $this->product->detail_harga = json_encode($this->priceRanges);

        // Save the product
        $this->product->save();

        session()->flash('productEdited', ['Sukses', 'Berhasil mengedit data', 'success']);
        return redirect()->route('products-view');
    }

    public function render()
    {
        return view('livewire.product-edit');
    }
}
