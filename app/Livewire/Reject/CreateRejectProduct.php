<?php

namespace App\Livewire\Reject;

use Livewire\Component;
use App\Models\Products;
use App\Models\RejectProduct;

class CreateRejectProduct extends Component
{
    public $products;
    public $product_id;
    public $qty;

    public function save()
    {
        RejectProduct::create([
            'product_id' => $this->product_id,
            'stok' => $this->qty,
        ]);

        session()->flash('dataCreated', ['Sukses', 'Berhasil menambahkan data', 'success']);
        return redirect()->route('rejected-products.index');
    }

    public function render()
    {
        $this->products = Products::get();

        return view('livewire.reject.create-reject-product', [
            'product' => $this->products,
        ]);
    }
}
