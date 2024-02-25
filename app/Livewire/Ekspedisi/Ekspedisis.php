<?php

namespace App\Livewire\Ekspedisi;

use Livewire\Component;
use App\Models\Ekspedisi;

class Ekspedisis extends Component
{
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

    public function confirmDelete($ekspedisiId)
    {
        $this->confirmingEkspedisiDeletion = $ekspedisiId;
    }

    public function deleteEkspedisi()
    {
        Ekspedisi::destroy($this->confirmingEkspedisiDeletion);
        $this->confirmingEkspedisiDeletion = null;
        // Refresh the products list
        $this->ekspedisi = Ekspedisi::all();
    }
}
