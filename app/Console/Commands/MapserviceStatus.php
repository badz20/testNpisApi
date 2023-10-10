<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \App\Models\mapService;
use GuzzleHttp\Psr7\Response;


class MapserviceStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'status:MapserviceStatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Mapservice Status';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $getdata=mapService::all();
        $pautan_list=$getdata->toArray();
        for($i=0;$i<count($getdata->toArray());$i++){
            // print_r($pautan_list[$i]['pautan_api']);
            $client = new \GuzzleHttp\Client();
            $response = $client->get($pautan_list[$i]['pautan_api']); 
            $response = new Response();
            $responsecode= $response->getStatusCode();
            echo $responsecode.',';
            if($responsecode==200){
                mapService::where('id',$pautan_list[$i]['id'])->update([
                    'status'=>1
                ]);
            }
            else{
                mapService::where('id',$request->$pautan_list[$i]['id'])->update([
                    'status'=>0
                ]);
            }
        }
    }
}
