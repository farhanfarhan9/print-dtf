<?php

namespace App\Livewire\Ekspedisi;

use App\Models\Ekspedisi;
use Livewire\Component;
use WireUi\Traits\Actions;
use Livewire\WithPagination;

class EkspedisiArchieve extends Component
{
    use Actions;
    use WithPagination;

    public $search;


    public function restore($id)
    {
        $ekspedisi = Ekspedisi::onlyTrashed()->findOrFail($id);
        $ekspedisi->restore();
        $this->notification([
            'title'       => 'Sukses',
            'description' => 'Berhasil restore ekspedisi',
            'icon'        => 'success',
            'timeout'     => 3000
        ]);
    }

    public function render()
    {
        return view('livewire.ekspedisi.ekspedisi-archieve',[
            'ekspedisi' => Ekspedisi::where('nama_ekspedisi', 'like', "%{$this->search}%")->onlyTrashed()->get()
        ]);
    }
}
