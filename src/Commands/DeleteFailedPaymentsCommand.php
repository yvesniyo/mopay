<?php


namespace Yves\Mopay\Commands;
use Illuminate\Console\Command;

class DeleteFailedPaymentsCommand extends Command{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
     protected $signature = 'mopay:delete';

     /**
      * The console command description.
      *
      * @var string
      */
     protected $description = 'Delete mopay failed payments';
 
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
         
        



     }

}