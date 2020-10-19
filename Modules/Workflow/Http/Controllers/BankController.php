<?php

namespace Modules\Workflow\Http\Controllers;

use App\Events\AddEventLogs;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Role;
use Modules\Workflow\Entities\Bank;
use Validator;
use Fomvasss\Dadata\Facades\DadataSuggest;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(){
        if(view()->exists('workflow::banks')){
            $rows = Bank::all();
            $data = [
                'title' => 'Банки',
                'head' => 'Справочник банков',
                'rows' => $rows,
            ];

            return view('workflow::banks',$data);
        }
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request){
        if(!Role::granted('fin_edit')){//вызываем event
            $msg = 'Попытка создания новой записи в справочнике банков!';
            event(new AddEventLogs('access',Auth::id(),$msg));
            abort(503,'У Вас нет прав на создание записи!');
        }
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'unique' => 'Значение поля должно быть уникальным!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строковым!',
            ];
            $validator = Validator::make($input,[
                'bik' => 'nullable|max:10',
                'swift' => 'required|unique:banks|max:15',
                'title' => 'required|unique:banks|max:70',
                'account' => 'required|unique:banks|max:30',
                'city' => 'nullable|max:50',
                'country' => 'nullable|max:50',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('bankAdd')->withErrors($validator)->withInput();
            }

            $bank = new Bank();
            $bank->fill($input);
            $bank->created_at = date('Y-m-d');
            $bank->user_id = Auth::id();
            if($bank->save()){
                $msg = 'Банк '. $input['title'] .' был успешно добавлен!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect('/banks')->with('status',$msg);
            }
        }
        if(view()->exists('workflow::bank_add')){
            $data = [
                'head' => 'Новая запись',
                'title' => 'Справочник банков'
            ];
            return view('workflow::bank_add', $data);
        }
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id,Request $request){
        $model = Bank::find($id);
        if($request->isMethod('delete')){
            if(!Role::granted('fin_edit')){
                $msg = 'Попытка удаления банка '.$model->title;
                event(new AddEventLogs('access',Auth::id(),$msg));
                abort(503,'У Вас нет прав на удаление записи!');
            }
            $model->delete();
            $msg = 'Банк '. $model->title .' был удален!';
            //вызываем event
            event(new AddEventLogs('info',Auth::id(),$msg));
            return redirect('/banks')->with('status',$msg);
        }
        if(!Role::granted('fin_edit')){
            $msg = 'Попытка редактирования банка '. $model->title;
            //вызываем event
            event(new AddEventLogs('access',Auth::id(),$msg));
            abort(503,'У Вас нет прав на редактирование записи!');
        }
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен
            $messages = [
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строковым!',
            ];
            $validator = Validator::make($input,[
                'bik' => 'nullable|string|max:10',
                'city' => 'nullable|string|max:50',
                'country' => 'nullable|string|max:50',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('bankEdit',['bank'=>$model->id])->withErrors($validator)->withInput();
            }
            $model->fill($input);
            $model->user_id = Auth::id();
            if($model->update()){
                $msg = 'Данные банка '. $model->title .' обновлены!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect('/banks')->with('status',$msg);
            }
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели Currency
        if(view()->exists('workflow::bank_edit')){
            $data = [
                'title' => 'Справочник банков',
                'head' => 'Редактирование записи '.$old['title'],
                'data' => $old
            ];
            return view('workflow::bank_edit',$data);
        }
        abort(404);
    }

    public function ajaxBik(Request $request)
    {
        $query = $request->get('query', '');
        //нужно чтобы возвращалось поле name иначе них.. не работает!!!
        //подите прочь, я возмущен и раздосадован...
        $codes = DB::select("select bik as name from banks where bik like '%$query%'");
        return response()->json($codes);
    }

    public function ajaxSwift(Request $request)
    {
        $query = $request->get('query', '');
        //нужно чтобы возвращалось поле name иначе них.. не работает!!!
        //подите прочь, я возмущен и раздосадован...
        $codes = DB::select("select swift as name from banks where swift like '%$query%'");
        return response()->json($codes);
    }

    public function fill(Request $request)
    {
        if (!Role::granted('fin_edit')) {//вызываем event
            $msg = 'Попытка создания нового банка!';
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на создание записи!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            if(!empty($input['bik']))
                $query = $input['bik'];
            $token = config('dadata.token');
            if(empty($token)) return 'NO_API_KEY';
            $res = DadataSuggest::suggest("bank", ["query"=>$query]); //"044525225"
            return $res;
        }
    }
}
