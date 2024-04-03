<?php

namespace App\Livewire;

use Livewire\Component;
use App\Imports\CustomerImport;
use App\Models\Customer;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class CustomerImportData extends Component
{
    use WithFileUploads;
    public $file;
    public $data;
    public $typeerror;

    public function mount()
    {

    }

    protected $rules = [
        "file" => "mimes:xlsx"
    ];

    public function render()
    {
        if ($this->file) {
            // Generate a temporary file path with the correct extension
            $tempPath = $this->file->getRealPath();
            $newPath = $tempPath . '.xlsx'; // Append .xlsx to the temp file path
    
            // Copy the file to the new path with the correct extension
            copy($tempPath, $newPath);
    
            try {
                $this->data = Excel::toArray(new CustomerImport, $newPath)[0];
                $this->typeerror = false;
            } catch (\Throwable $th) {
                if ($th instanceof NoTypeDetectedException) {
                    $this->typeerror = "File type not supported";
                } else {
                    // Handle other exceptions
                    $this->typeerror = "An error occurred: " . $th->getMessage();
                }
            }
    
            // Optionally, delete the copied file after processing
            @unlink($newPath);
        }
        return view('livewire.customer-import-data');
    }

    function save()
    {
        foreach ($this->data as $key => $value) {
            if ($key == 0) {
                continue;
            }
            Customer::create([
                "name" => $value[0],
                "provinsi" => 2,
                "city" => 55,
                "district" => 629,
                "postal" => 20771,
                "phone" => $value[1],
                "deposit" => 0,
                "address" => $value[2],
            ]);
        }
        session()->flash('customerCreated', ['Sukses', 'Berhasil menambahkan data', 'success']);
        return redirect()->route('customer.index');
    }
}
