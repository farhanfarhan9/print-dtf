<?php

namespace App\Livewire\Ekspedisi;

use Livewire\Component;
use App\Models\Ekspedisi;
use WireUi\Traits\Actions;

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
            'title'       => 'Menghapus Data',
            'description' => 'Yakin Ingin Menghapus Data?',
            'acceptLabel' => 'Ya',
            'method'      => 'delete',
            'params'      => $ekspedisi,
            'timeout'     => 3000
        ]);
    }

    public function delete(Ekspedisi $ekspedisi)
    {
        $ekspedisi->delete();
    }
}
