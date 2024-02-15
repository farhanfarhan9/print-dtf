<?php

namespace App\Livewire\Address;

use App\Models\Address;
use Livewire\Component;

class EditAddress extends Component
{
    public $city;
    public $postal;
    public $phone;
    public $address;

    public $address_data;

    public function mount(Address $address_data)
    {
        $this->city = $address_data->city;
        $this->postal = $address_data->postal;
        $this->phone = $address_data->phone;
        $this->address = $address_data->address;
    }
    public function rules()
    {
        return [
            'city' => 'required',
            'postal' => 'required|min:3|numeric',
            'phone' => 'required',
            'address' => 'required',
        ];
    }

    public function update()
    {
        $this->validate();

        $this->address_data->update([
            'city' => $this->city,
            'postal' => $this->postal,
            'phone' => $this->phone,
            'address' => $this->address,
        ]);

        // session()->flash('addressEdited');
        $this->redirect(route('address.index'));
    }

    public function render()
    {
        return view('livewire.address.edit-address');
    }
}
