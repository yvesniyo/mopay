<?php


namespace Yves\Mopay\Commands;
use Illuminate\Console\Command;

class InstallCommand extends Command{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
     protected $signature = 'mopay:install';

     /**
      * The console command description.
      *
      * @var string
      */
     protected $description = 'Publish all mopay publishables, it will overwite if its the second time';
 
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
         
        $this->call("vendor:publish",[
            "--provider"=> \Yves\Mopay\Providers\MopayServiceProvider::class,
            "--force"
        ]);



     }

}