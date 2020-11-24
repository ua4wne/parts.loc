<?php

namespace App\Console\Commands;

use App\Models\Currency;
use Illuminate\Console\Command;

class CheckRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проверка курсов валют на сайте сбербанка';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $path = "http://cbr.ru/scripts/XML_daily.asp?date_req=" . date('d.m.Y');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$path);
        curl_setopt($ch, CURLOPT_FAILONERROR,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $sXML = curl_exec($ch);
        //$data= curl_getinfo ($ch);
        //print_r($data);	 // смотрим статусы для отладки - http_code
        curl_close($ch);
        $oXML = new \SimpleXMLElement($sXML);
        //выбираем интересующие нас валюты
        $currs = Currency::all();
        if(!empty($currs)){
            $valutes = array();
            foreach ($currs as $row)
                array_push($valutes,$row->dcode);
        }

        foreach($oXML->Valute as $val){
            if(in_array($val->NumCode,$valutes)){
                $model = Currency::where('dcode',$val->NumCode)->first();
                $model->unit = $val->Nominal;
                $model->cource = (float)str_replace(',','.',$val->Value);
                $model->update();
            }
        }
    }
}
