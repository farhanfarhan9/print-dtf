<?php

namespace App\Livewire;

use Livewire\Component;
use WireUi\Traits\Actions;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use App\Models\Products;

class Product extends Component
{
    use Actions;
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

    public function deleteDialog(Products $products)
    {
        $this->dialog()->confirm([
            'title'       => 'Menghapus Data',
            'description' => 'Yaking Ingin Menghapus Data?',
            'acceptLabel' => 'Ya',
            'method'      => 'delete',
            'params'      => $products,
            'timeout'     => 5000
        ]);
    }

    public function delete(Products $products)
    {
        $products->delete();;
        $this->notification([
            'title'       => 'Product Berhasil di Hapus!',
            'description' => 'Product di Hapus',
            'icon'        => 'success',
            'timeout'     => 3000
        ]);
    }
}
