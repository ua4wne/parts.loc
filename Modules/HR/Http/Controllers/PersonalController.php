<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Entities\Role;
use Modules\HR\Entities\Position;
use Validate;

class PersonalController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if(!Role::granted('hr_work')){//вызываем event
            abort(503,'У Вас нет прав на просмотр справочников кадров!');
        }
        return 'INDEX';
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('hr::create');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id, Request $request)
    {
        return view('hr::edit');
    }

    public function destroy(Request $request){

    }
}
