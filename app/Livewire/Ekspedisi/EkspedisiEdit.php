<?php

namespace App\Livewire\Ekspedisi;

use Livewire\Component;
use App\Models\Ekspedisi;
use WireUi\Traits\Actions;

class EkspedisiEdit extends Component
{
    use Actions;
    public Ekspedisi $ekspedisi;
    public $namaEkspedisi, $ongkir;

    public function mount(Ekspedisi $ekspedisi)
    {
        $this->ekspedisi = $ekspedisi;
        $this->namaEkspedisi = $ekspedisi->nama_ekspedisi;
        $this->ongkir = $ekspedisi->ongkir;
    }

    public function save()
    {
        // Validate input
        $this->validate([
            'namaEkspedisi' => 'required|string|max:255',
            'ongkir' => 'required|numeric',
        ]);

        // Update ekspedisi properties
        $this->ekspedisi->nama_ekspedisi = $this->namaEkspedisi;
        $this->ekspedisi->ongkir = $this->ongkir;

        // Save the ekspedisi
        $this->ekspedisi->save();

        $this->notification()->success(
            $title = 'Ekspedisi saved',
            $description = 'Your Ekspedisi. was successfully updated'
        );

        return redirect()->route('ekspedisi-view');
    }

    public function render()
    {
        return view('livewire.ekspedisi.ekspedisi-edit');
    }
}
