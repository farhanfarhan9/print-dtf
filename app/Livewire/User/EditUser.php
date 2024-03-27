<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class EditUser extends Component
{
    public $user,$name,$email,$password,$role;

    
    public function mount(User $user)
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->roles;
    }

    

    public function rules()
    {
        return [
            'name' => 'required',
            'email' => 'required',
            'role' => 'required',
        ];
    }

    public function save()
    {
        $this->validate();

        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'roles' => $this->role
        ]);

        session()->flash('userEdited',['Sukses', 'Berhasil mengedit data', 'success']);
        $this->redirect(route('user.index'), navigate: true);
    }
    
    public function render()
    {
        return view('livewire.user.edit-user');
    }
}
