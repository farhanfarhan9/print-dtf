<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Customer;
use WireUi\Traits\Actions;
use Livewire\WithPagination;

class AllCustomer extends Component
{
    use Actions;
    use WithPagination;
    public $search;

    public function mount()
    {
    }

    public function deleteDialog(Customer $customer)
    {
        $this->dialog()->confirm([
            'title'       => 'Menghapus Data',
            'description' => 'Yakin Ingin Menghapus Data?',
            'acceptLabel' => 'Ya',
            'method'      => 'delete',
            'params'      => $customer,
            'timeout'     => 3000
        ]);
    }

    public function delete(Customer $customer)
    {
        $this->notification([
            'title'       => 'Sukses',
            'description' => 'Berhasil menghapus Customer',
            'icon'        => 'success',
            'timeout'     => 3000
        ]);
        $customer->delete();
    }
    public function render()
    {
        return view('livewire.customer.all-customer',[
            'customers' => Customer::where('name', 'like', "%{$this->search}%")->paginate(15)
        ]);
    }
}
