<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;
use WireUi\Traits\Actions;
use Livewire\WithPagination;

class ArchieveUser extends Component
{
    use Actions;
    use WithPagination;
    public $search;
    
    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        $this->notification([
            'title'       => 'Sukses',
            'description' => 'Berhasil restore user',
            'icon'        => 'success',
            'timeout'     => 3000
        ]);
    }

    public function render()
    {
        return view('livewire.user.archieve-user',[
            'users' => User::where('name', 'like', "%{$this->search}%")->onlyTrashed()->paginate(15)
        ]);
    }
}
