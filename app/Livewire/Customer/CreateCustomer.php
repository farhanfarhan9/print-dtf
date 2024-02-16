<?php

namespace App\Livewire\Customer;

use App\Models\Customer;
use Livewire\Component;

class CreateCustomer extends Component
{
    public $name;
    public $city;
    public $postal;
    public $phone;
    public $deposit;
    public $address;

    public function rules()
    {
        return [
            'name' => 'required',
            'city' => 'required',
            'postal' => 'required|min:3|numeric',
            'phone' => 'required',
            'deposit' => 'nullable',
            'address' => 'required',
        ];
    }

    public function save()
    {
        $this->validate();

        Customer::create([
            'name' => $this->name,
            'city' => $this->city,
            'postal' => $this->postal,
            'phone' => $this->phone,
            'deposit' => $this->deposit ? $this->deposit : 0,
            'address' => $this->address,
        ]);

        $this->redirect(route('customer.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.customer.create-customer');
    }
}
