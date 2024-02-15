<?php

namespace App\Livewire\Address;

use App\Models\User;
use App\Models\Address;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use WireUi\Traits\Actions;

class AllAddress extends Component
{
    use Actions;

    public $user;

    public function update($id)
    {
        $this->user = Auth::user(); // Retrieve the authenticated user and assign it to $this->user

        // Check if the user exists
        if ($this->user) {
            // Update the user's address_id
            $this->user->address_id = $id;
            $this->user->save(); // Save the changes to the database
        }
    }

    public function deleteDialog(Address $address)
    {
        $this->dialog()->confirm([
            'title'       => 'Menghapus Data',
            'description' => 'Yaking Ingin Menghapus Data?',
            'acceptLabel' => 'Ya',
            'method'      => 'delete',
            'params'      => $address
        ]);
    }

    public function delete(Address $address)
    {
        // if (Auth::user()->address_id != $address->id) {
            $address->delete();
            // session()->flash('addressDeleted');
        // } else {
        //     session()->flash('addressDeletedFailed');
        // }
    }

    public function render()
    {
        return view('livewire.address.all-address', [
            'addresses' => Address::get()
        ]);
    }
}
