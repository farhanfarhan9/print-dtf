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
    public $depositModal;
    public $editedUser;
    public $newDeposit;

    public function mount()
    {
    }

    public function depositDialog(Customer $customer)
    {
        $this->editedUser = $customer;
        $this->depositModal = 1;
    }

    public function addDeposit()
    {
        $existingDeposit = $this->editedUser->deposit;
        $this->editedUser->update([
            'deposit' => $existingDeposit + $this->newDeposit,
        ]);
        
        session()->flash('customerCreated',['Sukses', 'Berhasil menambahkan deposit', 'success']);
        $this->reset('editedUser', 'depositModal', 'newDeposit');
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
        return view('livewire.customer.all-customer', [
            'customers' => Customer::where('name', 'like', "%{$this->search}%")->paginate(15)
        ]);
    }
}
