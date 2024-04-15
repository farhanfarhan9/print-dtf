<?php

namespace App\Livewire\Ekspedisi;

use Livewire\Component;
use App\Models\Ekspedisi;
use WireUi\Traits\Actions;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class Ekspedisis extends Component
{
    use Actions;
    public $ekspedisi;
    public $search;
    public $confirmingEkspedisiDeletion = null;

    public function render()
    {
        $this->ekspedisi = Ekspedisi::where('nama_ekspedisi', 'like', "%{$this->search}%")->get();
        \Log::debug($this->ekspedisi);
        return view('livewire.ekspedisi.ekspedisis');
    }

    public function addData(){
        return redirect()->to('/ekspedisi/add/');
    }

    public function editEkspedisi($ekspedisiId)
    {
        return redirect()->to('/ekspedisi/edit/' . $ekspedisiId);
    }

    public function deleteDialog(Ekspedisi $ekspedisi)
    {
        $this->dialog()->confirm([
            'title'       => 'Arsipkan Data',
            'description' => 'Yakin Ingin Mengarsipkan Data?',
            'acceptLabel' => 'Ya',
            'method'      => 'delete',
            'params'      => $ekspedisi,
            'timeout'     => 3000
        ]);
    }

    public function delete(Ekspedisi $ekspedisi)
    {
        $this->notification([
            'title'       => 'Ekspedisi Berhasil di arsipkan!',
            'description' => 'Ekspedisi '. $ekspedisi->nama_ekspedisi .' di arsipkan',
            'icon'        => 'success',
            'timeout'     => 3000
        ]);
        $ekspedisi->delete();
    }
}
