<?php

namespace App\Livewire\Ekspedisi;

use Livewire\Component;
use App\Models\Ekspedisi;
use WireUi\Traits\Actions;

class EkspedisiAdd extends Component
{
    use Actions;

    public $namaEkspedisi;
    public $ongkir;

    public function save()
    {
        // Validation and product creation logic
        $Ekspedisi = Ekspedisi::create([
            'nama_ekspedisi' => $this->namaEkspedisi,
            'ongkir' => $this->ongkir,
        ]);

        session()->flash('expeditionCreated', ['Sukses', 'Berhasil menambahkan data', 'success']);
        // Reset the form or give some response
        return redirect()->route('ekspedisi-view');
    }

    public function render()
    {
        return view('livewire.ekspedisi.ekspedisi-add');
    }
}
