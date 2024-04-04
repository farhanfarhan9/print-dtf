<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;
use WireUi\Traits\Actions;
use Livewire\WithPagination;

class AllUser extends Component
{
    use Actions;
    use WithPagination;

    public $search;

    public function deleteDialog(User $user)
    {
        $this->dialog()->confirm([
            'title'       => 'Menghapus Data',
            'description' => 'Yakin Ingin Menghapus Data?',
            'acceptLabel' => 'Ya',
            'method'      => 'delete',
            'params'      => $user,
            'timeout'     => 3000
        ]);
    }

    public function delete(User $user)
    {
        $this->notification([
            'title'       => 'Sukses',
            'description' => 'Berhasil menghapus user',
            'icon'        => 'success',
            'timeout'     => 3000
        ]);
        $user->delete();
    }

    public function render()
    {
        return view('livewire.user.all-user',[
            'users' => User::where('name', 'like', "%{$this->search}%")->paginate(15)
        ]);
    }
}
