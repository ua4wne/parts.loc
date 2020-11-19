<?php

namespace Modules\Workflow\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Warehouse\Entities\Category;
use Modules\Warehouse\Entities\Good;

class PricingRule extends Model
{
    //указываем имя таблицы
    protected $table = 'pricing_rules';

    protected $fillable = ['title','ratio','currency_id','category_id','good_id','user_id','price_type'];

    public function currency()
    {
        return $this->belongsTo('App\Models\Currency','currency_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }

    public function getPriceNameAttribute()
    {
        $name = '';
        switch ($this->price_type) {
            case 'retail':
                $name = 'Розница';
                break;
            case 'wholesale':
                $name = 'Оптовая';
                break;
            case 'small':
                $name = 'Мелкооптовая';;
                break;
        }
        return $name;
    }

    public function getCategoryAttribute()
    {
        $category = '';
        if(!empty($this->category_id))
            $category = Category::find($this->category_id)->category;
        return $category;
    }

    public function getGoodAttribute()
    {
        $good = '';
        if(!empty($this->good_id)){
            $row = Good::find($this->good_id);
            $good = $row->title . ' ( артикул '. $row->vendor_code . ')';
        }
        return $good;
    }
}
