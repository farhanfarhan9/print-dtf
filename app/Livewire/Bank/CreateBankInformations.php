<?php

namespace App\Livewire\Bank;

use Livewire\Component;

class CreateBankInformations extends Component
{
    public $bank;
    
    public function render()
    {
        return view('livewire.bank.create-bank-informations');
    }
}
