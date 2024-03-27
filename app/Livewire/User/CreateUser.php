<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class CreateUser extends Component
{
    public $name,$email,$password,$role;

    public function rules()
    {
        return [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'role' => 'required',
        ];
    }

    public function save()
    {
        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'roles' => $this->role
        ]);

        session()->flash('userCreated', ['Sukses', 'Berhasil menambahkan data', 'success']);
        return redirect()->route('user.index');
    }

    public function render()
    {
        return view('livewire.user.create-user');
    }
}
