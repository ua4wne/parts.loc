<?php

namespace Modules\Workflow\Entities;

use Illuminate\Database\Eloquent\Model;

class TblApplication extends Model
{
    //указываем имя таблицы
    protected $table = 'tbl_applications';

    protected $fillable = ['application_id','good_id','qty','car_id','unit_id','order_id'];

    public function application()
    {
        return $this->belongsTo('Modules\Workflow\Entities\Application','application_id','id');
    }

    public function good()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Good','good_id','id');
    }

    public function car()
    {
        return $this->belongsTo('App\Models\Car','car_id','id');
    }

    public function order()
    {
        return $this->Order::find($this->order_id);
    }

    public function getOffersAttribute()
    {
        $content = '<table><thead><tr><th>№ поставщика</th><th>Срок поставки</th><th>Цена</th><th>Комментарий</th></tr>
                    </thead><tbody>';
        $offers = Offer::where('good_id',$this->good_id)->get();
        if(!empty($offers)){
            foreach ($offers as $row){
                $amount = $row->price * $row->markup;
                $content .= '<tr><td>' . $row->firm->name . '</td><td>' . $row->delivery_time . '</td>
                                 <td>' . round($amount,2) . '</td><td>' . $row->comment . '</td></tr>';
            }
        }
        $content .= '</tbody></table>';
        return $content;
    }
}
