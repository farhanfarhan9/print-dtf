<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Customer;
use Illuminate\Support\Facades\Gate;

class CreateCustomer extends Component
{
    public $name, $city, $postal, $phone, $deposit, $address, $isReseller;
    public $selectedProvinsi = null, $selectedKota = null, $selectedKecamatan = null, $selectedPostal = null;

    public function rules()
    {
        return [
            'name' => 'required',
            'selectedProvinsi' => 'required',
            'selectedKota' => 'required',
            'selectedKecamatan' => 'nullable',
            'selectedPostal' => 'nullable|min:3|numeric',
            'phone' => 'required',
            'deposit' => 'nullable|numeric',
            'address' => 'required',
        ];
    }

    public function save()
    {
        $this->validate();

        try {
            logger('Save method triggered');
            logger('Validation passed');

            $customer = Customer::create([
                'name' => $this->name,
                'provinsi' => $this->selectedProvinsi,
                'city' => $this->selectedKota,
                'district' => $this->selectedKecamatan,
                'postal' => $this->selectedPostal,
                'phone' => $this->phone,
                'deposit' => $this->deposit ?: 0,
                'address' => $this->address,
                'is_reseller' => $this->isReseller ? true : false,
            ]);

            logger('Customer created: ' . $customer->id);

            session()->flash('customerCreated', ['Sukses', 'Berhasil menambahkan data', 'success']);
            return redirect()->route('customer.index');
        } catch (\Exception $e) {
            logger('Error: ' . $e->getMessage());
        }
    }

    public function updateCities($value)
    {
        $this->selectedProvinsi = $value;
        // Assuming you might want to reset the city and district when the province changes
        $this->selectedKota = null;
        $this->selectedKecamatan = null;
        // Add logic here if you need to fetch cities based on the selected province
    }

    public function updateDistricts($value)
    {
        $this->selectedKota = $value;
        // Reset districts when the city changes or add logic to fetch new districts
        $this->selectedKecamatan = null;
    }

    public function updatePostal($value)
    {
        $this->selectedKecamatan = $value;
    }

    public function render()
    {
        return view('livewire.customer.create-customer');
    }
}
