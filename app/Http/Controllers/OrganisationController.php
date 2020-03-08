<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use Illuminate\Http\Request;

class OrganisationController extends Controller
{
    public function index(){
        if (view()->exists('orgs')) {
            $rows = Organisation::paginate(env('PAGINATION_SIZE'));
            $title = 'Организации';
            $data = [
                'title' => $title,
                'head' => 'Наши организации',
                'rows' => $rows,
            ];
            return view('orgs', $data);
        }
        abort(404);
    }
}
