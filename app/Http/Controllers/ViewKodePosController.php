<?php
// app/Http/Controllers/ViewKodePosController.php
namespace App\Http\Controllers;

use App\Models\ViewKodePos;
use Illuminate\Http\Response;

class ViewKodePosController extends Controller
{
    public function index()
    {
        $data = ViewKodePos::all();
        return response()->json($data);
    }
}
