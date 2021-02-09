<?php

namespace Modules\Workflow\Entities;

use Illuminate\Database\Eloquent\Model;

class TblApplication extends Model
{
    //указываем имя таблицы
    protected $table = 'tbl_applications';

    protected $fillable = ['application_id','good_id','tbl_sale_id','qty','car_id','unit_id','order_id','firm_id','price'];

    public function application()
    {
        return $this->belongsTo('Modules\Workflow\Entities\Application', 'application_id', 'id');
    }

    public function good()
    {
        return $this->belongsTo('Modules\Warehouse\Entities\Good', 'good_id', 'id');
    }

    public function tbl_sale()
    {
        return $this->belongsTo('Modules\Workflow\Entities\TblSale', 'tbl_sale_id', 'id');
    }

    public function car()
    {
        return $this->belongsTo('App\Models\Car', 'car_id', 'id');
    }

    public function getOrderAttribute()
    {
        return $this->Order::find($this->order_id);
    }

    public function getFirmAttribute()
    {
        return $this->Firm::find($this->firm_id);
    }

    public function getOffersAttribute()
    {
        $content = '<table class="table table-condensed"><thead><tr><th>Поставщик</th><th>Срок поставки</th><th>Цена</th><th>Комментарий</th><th></th></tr>
                    </thead><tbody>';
        $offers = SetOffer::where('tbl_application_id', $this->id)->get();
        if (!empty($offers)) {
            foreach ($offers as $row) {
                if ($this->application->state == 0) {

                    $content .= '<tr id="ofr' . $row->id . '"><td>' . $row->firm->name . '</td><td>' . $row->delivery_time . '</td>
                                 <td class="offer_pos">' . $row->amount . '</td><td>' . $row->comment . '</td><td><button class="btn btn-xs btn-primary btn-o">
																<i class="fa fa-edit"></i>
															</button></td></tr>';
                } else {
                    $content .= '<tr><td>' . $row->firm->name . '</td><td>' . $row->delivery_time . '</td>
                                 <td>' . $row->amount . '</td><td>' . $row->comment . '</td><td></td></tr>';
                }
            }
        }
        $content .= '</tbody></table>';
        return $content;
    }
}
