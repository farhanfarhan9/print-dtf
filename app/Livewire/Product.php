<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Products;
use WireUi\Traits\Actions;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class Product extends Component
{
    use Actions;
    public $products;
    public $search;
    public $confirmingProductDeletion = null;
    
    
    public function render()
    {
        // $this->products = Products::all();
        $this->products = Products::where('nama_produk', 'like', "%{$this->search}%")->get();
        \Log::debug($this->products); // Temporarily log the products to inspect the structure.
        return view('livewire.product');
    }

    public function addData(){
        return redirect()->to('/products/add/');
    }

    public function deleteDialog(Products $product)
    {
        $this->dialog()->confirm([
            'title'       => 'Menghapus Data',
            'description' => 'Yakin Ingin Menghapus Data?',
            'acceptLabel' => 'Ya',
            'method'      => 'delete',
            'params'      => $product,
            'timeout'     => 3000
        ]);
    }

    public function editProduct($productId)
    {
        return redirect()->to('/product/edit/' . $productId);
    }

    public function delete(Products $products)
    {
        $this->notification([
            'title'       => 'Sukses',
            'description' => 'Berhasil menghapus data produk',
            'icon'        => 'success',
            'timeout'     => 3000
        ]);
        $products->delete();
    }
}
