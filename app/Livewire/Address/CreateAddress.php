<?php

namespace App\Livewire\Address;

use App\Models\Address;
use Livewire\Component;
use WireUi\Traits\Actions;

class CreateAddress extends Component
{
    use Actions;

    public $city;
    public $postal;
    public $phone;
    public $address;

    public function rules()
    {
        return [
            'city' => 'required',
            'postal' => 'required|min:3|numeric',
            'phone' => 'required',
            'address' => 'required',
        ];
    }
    public function store()
    {
        $this->validate();
        
        Address::create([
            'city' => $this->city,
            'postal' => $this->postal,
            'phone' => $this->phone,
            'address' => $this->address,
        ]);
        session()->flash('addressCreated',['Sukses', 'Berhasil menawdwdwdwmbahkan data', 'success']);
        return redirect(route('address.index'));
    }

    public function render()
    {
        return view('livewire.address.create-address');
    }
}
