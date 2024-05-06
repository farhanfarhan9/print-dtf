<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Customer;
use Illuminate\Support\Facades\Gate;

class EditCustomer extends Component
{
    public $name, $phone, $deposit, $address, $newDeposit, $isReseller;
    public $selectedDataProvinsi, $selectedDataKota, $selectedDataPostal, $selectedDataKecamatan;
    public $selectedDataNameProvinsi, $selectedDataNameKota, $selectedDataNameKecamatan;
    public $selectedProvinsi, $selectedKota, $selectedPostal, $selectedKecamatan;
    public $customer;
    public $change;

    public function mount(Customer $customer)
    {
        $this->customer = $customer;
        $this->name = $customer->name;
        $this->selectedDataProvinsi = $customer->provinsi;
        $this->selectedDataKota = $customer->city;
        $this->selectedDataKecamatan = $customer->district;
        $this->selectedDataPostal = $customer->postal;
        // Safely access properties of related models
        $this->selectedDataNameProvinsi = $customer->province ? $customer->province->prov_name : null;
        $this->selectedDataNameKota = $customer->kota ? $customer->kota->city_name : null;
        $this->selectedDataNameKecamatan = $customer->kecamatans ? $customer->kecamatans->dis_name : null;
        $this->phone = $customer->phone;
        $this->deposit = $customer->deposit;
        $this->address = $customer->address;
        $this->isReseller = (bool) $customer->is_reseller;
        $this->change = false;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'phone' => 'required',
            'newDeposit' => 'nullable|numeric',
            'address' => 'required',
        ];
    }

    public function save()
    {
        $this->validate();
        if($this->change == true){
            $this->customer->update([
                'name' => $this->name,
                'provinsi' => $this->selectedProvinsi,
                'city' => $this->selectedKota,
                'district' => $this->selectedKecamatan,
                'postal' => $this->selectedPostal,
                'phone' => $this->phone,
                'deposit' => $this->deposit + ($this->newDeposit ?? 0),
                'address' => $this->address,
                'is_reseller' => $this->isReseller ? true : false,
            ]);
        }else{
            $this->customer->update([
                'name' => $this->name,
                'provinsi' => $this->selectedDataProvinsi,
                'city' => $this->selectedDataKota,
                'district' => $this->selectedDataKecamatan,
                'postal' => $this->selectedDataPostal,
                'phone' => $this->phone,
                'deposit' => $this->deposit + ($this->newDeposit ?? 0),
                'address' => $this->address,
                'is_reseller' => $this->isReseller ? true : false,
            ]);
        }

        session()->flash('customerEdited',['Sukses', 'Berhasil mengedit data', 'success']);
        $this->redirect(route('customer.index'), navigate: true);
    }

    public function changeLocation()
    {
        $this->change = true;
    }

    public function cancelChangeLocation()
    {
        $this->change = false;
    }

    public function render()
    {
        return view('livewire.customer.edit-customer');
    }
}
