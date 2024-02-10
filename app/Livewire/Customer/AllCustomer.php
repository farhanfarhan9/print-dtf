<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Customer;
use Livewire\WithPagination;

class AllCustomer extends Component
{
    use WithPagination;
    public $search;

    public function mount()
    {
    }

    public function delete(Customer $customer)
    {
        $customer->delete();
    }
    public function render()
    {
        return view('livewire.customer.all-customer',[
            'customers' => Customer::where('name', 'like', "%{$this->search}%")->paginate(15)
        ]);
    }
}
