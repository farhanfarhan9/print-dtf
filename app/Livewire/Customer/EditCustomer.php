<?php

namespace App\Livewire\Customer;

use App\Models\Customer;
use Livewire\Component;

class EditCustomer extends Component
{
    public $name;
    public $city;
    public $postal;
    public $phone;
    public $deposit;
    public $address;
    public $newDeposit;

    public $customer;

    public function mount(Customer $customer)
    {
        $this->name = $customer->name;
        $this->city = $customer->city;
        $this->postal = $customer->postal;
        $this->phone = $customer->phone;
        $this->deposit = $customer->deposit;
        $this->address = $customer->address;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'city' => 'required',
            'postal' => 'required|min:3|numeric',
            'phone' => 'required',
            'newDeposit' => 'nullable',
            'address' => 'required',
        ];
    }

    public function save()
    {
        $this->validate();

        $this->customer->update([
            'name' => $this->name,
            'city' => $this->city,
            'postal' => $this->postal,
            'phone' => $this->phone,
            'deposit' => $this->deposit + $this->newDeposit,
            'address' => $this->address,
        ]);

        $this->redirect(route('customer.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.customer.edit-customer');
    }
}
