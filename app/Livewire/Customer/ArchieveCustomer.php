<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Customer;
use WireUi\Traits\Actions;
use Livewire\WithPagination;

class ArchieveCustomer extends Component
{
    use Actions;
    use WithPagination;
    public $search;

    public function restore($id)
    {
        $customer = Customer::onlyTrashed()->findOrFail($id);
        $customer->restore();
        $this->notification([
            'title'       => 'Sukses',
            'description' => 'Berhasil restore customer',
            'icon'        => 'success',
            'timeout'     => 3000
        ]);
    }
    
    public function render()
    {
        return view('livewire.customer.archieve-customer',[
            'customers' => Customer::where('name', 'like', "%{$this->search}%")->onlyTrashed()->paginate(15)
        ]);
    }
}
