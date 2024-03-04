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
    public $selectedLocation;

    public function mount()
    {
        $this->selectedLocation = null;
    }

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

        session()->flash('customerCreated',['Sukses', 'Berhasil menambahkan data', 'success']);
        $this->redirect(route('customer.index'), navigate: true);
    }

    public function formatLabel($item)
    {
        return "{$item['KOTA']}, {$item['KECAMATAN']}, {$item['PROVINSI']}";
    }

    public function render()
    {
        return view('livewire.customer.create-customer');
    }
}
