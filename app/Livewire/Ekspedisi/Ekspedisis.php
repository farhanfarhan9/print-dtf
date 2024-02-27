<?php

namespace App\Livewire\Ekspedisi;

use Livewire\Component;
use WireUi\Traits\Actions;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use App\Models\Ekspedisi;

class Ekspedisis extends Component
{
    use Actions;
    public $ekspedisi;
    public $confirmingEkspedisiDeletion = null;

    public function render()
    {
        $this->ekspedisi = Ekspedisi::all();
        \Log::debug($this->ekspedisi);
        return view('livewire.ekspedisi.ekspedisis')->layout('layouts.app');
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
            'description' => 'Yaking Ingin Menghapus Data?',
            'acceptLabel' => 'Ya',
            'method'      => 'delete',
            'params'      => $ekspedisi,
            'timeout'     => 5000
        ]);
    }

    public function delete(Ekspedisi $ekspedisi)
    {
        $this->notification([
            'title'       => 'Ekspedisi Berhasil di Hapus!',
            'description' => 'Ekspedisi '. $ekspedisi->nama_ekspedisi .' di Hapus',
            'icon'        => 'success',
            'timeout'     => 3000
        ]);
        $ekspedisi->delete();
    }
}
