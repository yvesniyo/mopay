<?php
namespace Yves\Mopay\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Yves\Mopay\Commands\DeleteFailedPaymentsCommand;
use Yves\Mopay\Commands\InstallCommand;
use Yves\Mopay\Helpers\PaymentForm;
use Yves\Mopay\Utils\PaymentCart;
use Yves\Mopay\Utils\PaymentFormItem;
use Yves\Mopay\Utils\PaymentProductCart;

class MopayServiceProvider extends ServiceProvider
{
    

    public function boot()
    {
        include __DIR__.'/../routes/web.php';
        $this->loadViewsFrom(__DIR__."/../resources/views", "mopay");
        $this->loadMigrationsFrom(__DIR__."/database/migrations");
        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations')
        ]);
        $this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/mopay')
        ]);
        $this->publishes([
            __DIR__.'/../config/app.php' => config_path('mopay.php')
        ]);
        $this->mergeConfigFrom(__DIR__."/../config/app.php","mopay");

        $this->commands([
            InstallCommand::class,
            DeleteFailedPaymentsCommand::class,
        ]);

        $this->app->bind(PaymentForm::class);
        $this->app->alias("PaymentForm", PaymentForm::class);
        $this->app->bind(PaymentCart::class);
        $this->app->alias("PaymentCart", PaymentCart::class);
        $this->app->bind(PaymentProductCart::class);
        $this->app->alias("PaymentProductCart", PaymentProductCart::class);
        $this->app->bind(PaymentFormItem::class);
        $this->app->alias("PaymentFormItem", PaymentFormItem::class);

        Blade::directive('money', function ($amount) {
            return "<?php echo number_format($amount, 2); ?>";
        });
    

    }

    public function register()
    {
        
       
    }
}