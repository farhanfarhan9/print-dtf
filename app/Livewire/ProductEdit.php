<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Products;
use Illuminate\Support\Facades\Gate;

class ProductEdit extends Component
{
    public Products $product;
    public array $priceRanges = [];
    public array $priceRetailRanges = [];
    public $nama_produk, $stok;
    public $isEceran = false;

    public function mount(Products $product) // Correct type hinting
    {
        $this->product = $product;
        $this->nama_produk = $product->nama_produk;
        $this->stok = $product->stok;
        $this->priceRanges = json_decode($product->detail_harga, true) ?? [];
        $this->priceRetailRanges = json_decode($product->detail_harga_retail, true) ?? [];
        $this->isEceran = $product->type === 'eceran';
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

    public function addPriceRetailRange()
    {
        $this->priceRetailRanges[] = ['start' => 0, 'end' => 0, 'price' => 0];
    }

    public function removePriceRetailRange($index)
    {
        unset($this->priceRetailRanges[$index]);
        $this->priceRetailRanges = array_values($this->priceRetailRanges);
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
            'priceRetailRanges.*.start' => 'required|numeric|min:0',
            'priceRetailRanges.*.end' => 'required|numeric|min:0',
            'priceRetailRanges.*.price' => 'required|numeric|min:0',
        ]);

        // Update product properties
        $this->product->nama_produk = $this->nama_produk;
        $this->product->stok = $this->stok;
        $this->product->detail_harga = json_encode($this->priceRanges);
        $this->product->detail_harga_retail = json_encode($this->priceRetailRanges);
        $this->product->type = $this->isEceran ? 'eceran' : 'meteran';

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
