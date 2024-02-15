<?php

namespace App\Livewire\Address;

use App\Models\User;
use App\Models\Address;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AllAddress extends Component
{
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

        $this->dispatch('profile-updated', name: $this->user->name);
    }

    public function render()
    {
        return view('livewire.address.all-address', [
            'addresses' => Address::get()
        ]);
    }
}
