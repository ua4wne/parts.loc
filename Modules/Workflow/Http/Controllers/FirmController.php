<?php

namespace Modules\Workflow\Http\Controllers;

use App\Events\AddEventLogs;
use App\Models\Country;
use App\Models\FirmType;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Modules\Workflow\Entities\BankAccount;
use Modules\Workflow\Entities\Contact;
use Modules\Workflow\Entities\Firm;
use Validator;

class FirmController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if(view()->exists('workflow::firms')){
            $rows = Firm::all();
            $data = [
                'title' => 'Контрагенты',
                'head' => 'Справочник контрагентов',
                'rows' => $rows,
            ];

            return view('workflow::firms',$data);
        }
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request){
        if(!Role::granted('edit_refs')){//вызываем event
            $msg = 'Попытка создания нового контрагента!';
            event(new AddEventLogs('access',Auth::id(),$msg));
            abort(503,'У Вас нет прав на создание записи!');
        }
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'unique' => 'Значение поля должно быть уникальным!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'integer' => 'Значение поля должно быть числовым!',
                'string' => 'Значение поля должно быть строковым!',
            ];
            $validator = Validator::make($input,[
                'firm_type_id' => 'required|integer',
                'code' => 'required|string|max:12',
                'vcode' => 'nullable|string|max:12',
                'inn' => 'required|unique:firms|string|max:12',
                'kpp' => 'nullable|string|max:9',
                'okpo' => 'nullable|string|max:10',
                'title' => 'required|string|max:255',
                'name' => 'nullable|string|max:150',
                'country_id' => 'nullable|integer',
                'tax_number' => 'nullable|string|max:30',
                'client' => 'nullable|integer',
                'provider' => 'nullable|integer',
                'other' => 'nullable|integer',
                'foreigner' => 'nullable|integer',
                'user_id' => 'required|integer',
                'lname' => 'required|string|max:70',
                'mname' => 'nullable|string|max:70',
                'fname' => 'required|string|max:70',
                'position' => 'nullable|string|max:70',
                'phone' => 'nullable|string|max:20',
                'phones' => 'nullable|string|max:30',
                'email' => 'nullable|email|max:50',
                'site' => 'nullable|string|max:70',
                'legal_address' => 'nullable|string|max:254',
                'fact_address' => 'nullable|string|max:254',
                'post_address' => 'nullable|string|max:254',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('firmAdd')->withErrors($validator)->withInput();
            }

            $firm = new Firm();
            $firm->firm_type_id = $input['firm_type_id'];
            $firm->code = $input['code'];
            if(!empty($input['vcode']))
                $firm->vcode = $input['vcode'];
            $firm->inn = $input['inn'];
            if(!empty($input['kpp']))
                $firm->kpp = $input['kpp'];
            if(!empty($input['okpo']))
                $firm->okpo = $input['okpo'];
            $firm->title = $input['title'];
            $firm->name = $input['name'];
            if(!empty($input['country_id']))
                $firm->country_id = $input['country_id'];
            if(!empty($input['tax_number']))
                $firm->tax_number = $input['tax_number'];
            if(!empty($input['client']))
                $firm->client = $input['client'];
            else
                $firm->client = 0;
            if(!empty($input['provider']))
                $firm->provider = $input['provider'];
            else
                $firm->provider = 0;
            if(!empty($input['other']))
                $firm->other = $input['other'];
            else
                $firm->other = 0;
            if(!empty($input['foreigner']))
                $firm->foreigner = $input['foreigner'];
            else
                $firm->foreigner = 0;
            $firm->user_id = $input['user_id'];
            $firm->created_at = date('Y-m-d H:i:s');
            if($firm->save()){
                //добавляем контакты
                $contact = new Contact();
                $contact->firm_id = $firm->id;
                $contact->lname = $input['lname'];
                $contact->mname = $input['mname'];
                $contact->fname = $input['fname'];
                $contact->position = $input['position'];
                $contact->phone = $input['phone'];
                $contact->phones = $input['phone'];
                $contact->email = $input['email'];
                $contact->site = $input['site'];
                $contact->legal_address = $input['legal_address'];
                $contact->fact_address = $input['fact_address'];
                $contact->post_address = $input['post_address'];
                $contact->created_at = date('Y-m-d H:i:s');
                $contact->save();
                $msg = 'Контрагент '. $input['title'] .' был успешно добавлен!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect('/firms')->with('status',$msg);
            }
        }
        if(view()->exists('workflow::firm_add')){
            $ftypes = FirmType::all();
            $ftsel = array();
            foreach ($ftypes as $val) {
                $ftsel[$val->id] = $val->title;
            }
            $users = User::where(['active' => 1])->get();
            $usel = array();
            foreach ($users as $val) {
                $usel[$val->id] = $val->name;
            }
            $countries = Country::all();
            $consel = array();
            foreach ($countries as $val) {
                $consel[$val->id] = $val->title;
            }
            $data = [
                'title' => 'Справочник контрагентов',
                'head' => 'Новая запись',
                'ftsel' => $ftsel,
                'usel' => $usel,
                'consel' => $consel,
            ];
            return view('workflow::firm_add', $data);
        }
        abort(404);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        if(view()->exists('workflow::firm_view')){
            $ftypes = FirmType::all();
            $ftsel = array();
            foreach ($ftypes as $val) {
                $ftsel[$val->id] = $val->title;
            }
            $users = User::where(['active' => 1])->get();
            $usel = array();
            foreach ($users as $val) {
                $usel[$val->id] = $val->name;
            }
            $countries = Country::all();
            $consel = array();
            foreach ($countries as $val) {
                $consel[$val->id] = $val->title;
            }
            $firm = Firm::find($id);
            $contact = Contact::where(['firm_id'=>$id])->get();
            //dd($contact);
            if(!empty($firm->country_id))
                $country = Country::find($firm->country_id)->id;
            else
                $country=null;
            $data = [
                'title' => 'Справочник контрагентов',
                'head' => $firm->title,
                'ftsel' => $ftsel,
                'usel' => $usel,
                'consel' => $consel,
                'firm' => $firm,
                'contact' => $contact,
                'country' => $country,
            ];

            return view('workflow::firm_view',$data);
        }
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id,Request $request)
    {
        $model = Firm::find($id);
        if(!Role::granted('edit_refs')){
            $msg = 'Попытка редактирования контрагента '. $model->title;
            //вызываем event
            event(new AddEventLogs('access',Auth::id(),$msg));
            //abort(503,'У Вас нет прав на редактирование записи!');
            return 'NOT';
        }
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'unique' => 'Значение поля должно быть уникальным!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'integer' => 'Значение поля должно быть числовым!',
                'string' => 'Значение поля должно быть строковым!',
            ];
            $validator = Validator::make($input,[
                'firm_type_id' => 'required|integer',
                'code' => 'required|string|max:12',
                'vcode' => 'nullable|string|max:12',
                'inn' => 'required|string|max:12',
                'kpp' => 'nullable|string|max:9',
                'okpo' => 'nullable|string|max:10',
                'title' => 'required|string|max:255',
                'name' => 'nullable|string|max:150',
                'country_id' => 'nullable|integer',
                'tax_number' => 'nullable|string|max:30',
                'client' => 'nullable|integer',
                'provider' => 'nullable|integer',
                'other' => 'nullable|integer',
                'foreigner' => 'nullable|integer',
                'user_id' => 'required|integer',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('firmView',['id'=>$id])->withErrors($validator)->withInput();
            }
            if(empty($input['client']))
                $input['client'] = 0;
            if(empty($input['provider']))
                $input['provider'] = 0;
            if(empty($input['other']))
                $input['other'] = 0;
            if(empty($input['foreigner']))
                $input['foreigner'] = 0;
            $model->fill($input);
            if($model->update()){
                $msg = 'Данные контрагента '. $model->title .' обновлены!';
                //вызываем event
                event(new AddEventLogs('info',Auth::id(),$msg));
                return redirect()->route('firmView',['id'=>$id])->with('status',$msg);
            }
        }
    }

    public function contact_edit(Request $request){
        if(!Role::granted('edit_refs')){
            return 'NOT';
        }
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'integer' => 'Значение поля должно быть числовым!',
                'string' => 'Значение поля должно быть строковым!',
            ];
            $validator = Validator::make($input,[
                'firm_id' => 'required|integer',
                'lname' => 'required|string|max:70',
                'mname' => 'nullable|string|max:70',
                'fname' => 'required|string|max:70',
                'position' => 'nullable|string|max:70',
                'phone' => 'nullable|string|max:20',
                'phones' => 'nullable|string|max:30',
                'email' => 'nullable|email|max:50',
                'site' => 'nullable|string|max:70',
                'legal_address' => 'nullable|string|max:254',
                'fact_address' => 'nullable|string|max:254',
                'post_address' => 'nullable|string|max:254',
            ],$messages);
            if($validator->fails()){
                return 'NO VALIDATE';
            }
            // есть такая запись или нет
            Contact::updateOrCreate(['firm_id' => $input['firm_id']], ['lname' => $input['lname'],'mname' => $input['mname'],'fname' => $input['fname'],
                'position' => $input['position'],'phone' => $input['phone'],'phones' => $input['phones'],'email' => $input['email'],'site' => $input['site'],
                'legal_address' => $input['legal_address'],'fact_address' => $input['fact_address'],'post_address' => $input['post_address']]);

            $msg = 'Контакты контрагента ' . Firm::find($input['firm_id'])->title . ' успешно добавлены\обновлены!';
            //вызываем event
            event(new AddEventLogs('info', Auth::id(), $msg));
            return 'OK';
        }
        return 'ERR';
    }

    public function delete(Request $request){
        if($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $firm = Firm::find($input['firm_id'])->title;
            $msg='';
            if (!Role::granted('delete_refs')) {
                $msg = 'Попытка удаления контрагента ' . $firm;
                //вызываем event
                event(new AddEventLogs('access', Auth::id(), $msg));
                abort(503, 'У Вас нет прав на удаление записи!');
            }
            //попытка удаления
            Firm::find($input['firm_id'])->delete();
            $msg = "Контрагент $firm был удален!";
            return redirect()->route('firms')->with('status',$msg);
        }
    }

    public function fill(Request $request)
    {
        if (!Role::granted('edit_refs')) {//вызываем event
            $msg = 'Попытка создания нового контрагента!';
            event(new AddEventLogs('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на создание записи!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $key_api = env('API_FNS');
            if(empty($key_api)) return 'NO_API_KEY';
            if($input['fns']!='find') return 'NOT_METHOD';
            // create curl resource
            $ch = curl_init();
            $url = 'https://api-fns.ru/api/egr?req='.$input['inn'].'&key='.$key_api;
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // Set the url
            curl_setopt($ch, CURLOPT_URL,$url);
            // Execute
            $res=curl_exec($ch);
            // Closing
            curl_close($ch);
            return $res;
        }
    }
}
