<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Products;

class Product extends Component
{
    public $products;
    public $confirmingProductDeletion = null;

    public function render()
    {
        $this->products = Products::all();
        \Log::debug($this->products); // Temporarily log the products to inspect the structure.
        return view('livewire.product')->layout('layouts.app');
    }

    public function addData(){
        return redirect()->to('/products/add/');
    }

    public function editProduct($productId)
    {
        return redirect()->to('/product/edit/' . $productId);
    }

    public function confirmDelete($productId)
    {
        $this->confirmingProductDeletion = $productId;
    }

    public function deleteProduct()
    {
        Products::destroy($this->confirmingProductDeletion);
        $this->confirmingProductDeletion = null;
        // Refresh the products list
        $this->products = Products::all();
    }
}
