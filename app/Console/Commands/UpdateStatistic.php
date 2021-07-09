<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateStatistic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:statistic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update table statistic';

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
     * @return int
     */
    public function handle()
    {
        $collection_keys = CompanyName::select('id','cyan_key','user_id')->get();
    foreach($collection_keys as $collection_key){
        $url = 'https://public-api.cian.ru/v1/get-order';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Bearer " .$collection_key->cyan_key));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);
        $res = json_decode($curl_response);
        curl_close($curl);
        $ar_offerIds = array();
        $x_auction = 0;
        $y_auction = 0;
        $collectionExtID = Statistic::select('id_flat','id_offer','url_offer')->where('id_company', $collection_key->id)->get();
        $array_statistic = array();
        $array_statistic_from_db = array();
        foreach($collectionExtID as $collectionExtIdItem){
            $array_statistic_from_db[$collectionExtIdItem['id_offer']]['id_flat'] = $collectionExtIdItem['id_flat'];
            $array_statistic_from_db[$collectionExtIdItem['id_offer']]['url_offer'] = $collectionExtIdItem['url_offer'];
        }
        if ($res->result->offers) {
            foreach ($res->result->offers as $item) {
                if ($item->externalId > 0) {
                    $offer_id = $item->offerId;
                    if($offer_id != null){
                        if(!array_key_exists($offer_id, $array_statistic)){
                            $array_statistic[$offer_id]['id_flat'] = $item->externalId;
                            $array_statistic[$offer_id]['url_offer'] = $item->url;
                        }
                        $ar_offerIds[$x_auction][$y_auction++] = 'offerIds='.$offer_id;
                        if ($y_auction >= 20) {
                            $x_auction++;
                            $y_auction = 0;
                        }
                    }
                }
            }
        }
        if (!empty($ar_offerIds)) {
            foreach ($ar_offerIds as $offerIds) {
                $url = 'https://public-api.cian.ru/v1/get-auction?'.implode('&', $offerIds);
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Bearer " . $collection_key->cyan_key));
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $curl_response = curl_exec($curl);
                $res_auction = json_decode($curl_response);
                curl_close($curl);
                if (!empty($res_auction->result->items)) {
                    foreach ($res_auction->result->items as $a) {
                        if($a->currentBet == null){
                            $current_bet = 0;
                        }
                        else{
                            $current_bet = $a->currentBet;
                        }
                        if($a->leaderBet == null){
                            $leaderBet  = 0;
                        }
                        else{
                            $leaderBet  = $a->leaderBet ;
                        }
                        if(!array_key_exists($a->offerId, $array_statistic_from_db)){
                            Statistic::create(array(
                                'id_flat'=>$array_statistic[$a->offerId]['id_flat'],
                                'id_offer'=>(int)$a->offerId,
                                'url_offer'=>$array_statistic[$a->offerId]['url_offer'],
                                'current_bet'=>$current_bet,
                                'leader_bet'=>$leaderBet ,
                                'position'=> $a->position,
                                'page'=>$a->page,
                                'id_user'=>$collection_key->user_id,
                                'id_company'=>$collection_key->id
                                ));
                        }
                        else{
                            Statistic::where('id_user', $collection_key->id_user)->
                                        where('id_flat', $array_statistic[$a->offerId]['id_flat'])-> 
                                        where('id_offer', (int)$a->offerId)->  
                                        where('id_user', $collection_key->user_id)-> 
                                        where('id_company', $collection_key->id)->     
                                        update(array(
                                            'url_offer'=>$array_statistic[$a->offerId]['url_offer'],
                                            'current_bet'=>$current_bet,
                                            'leader_bet'=>$leaderBet ,
                                            'position'=> $a->position,
                                            'page'=>$a->page,
                                        ));
                        }
                    }
                }
            }
        }
    }
    }
}
