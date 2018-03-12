<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Auth\Guard;
use App\Helpers\Helper;
use Config;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
       /**
     * Login check
     *
     * @return void
     */
    public function checklogin()
    {
        // print_r(Auth::check());
        
        // if (Auth::check())
        // {
        //    echo 'am here ';
        // } else {
        //     echo "bi";
        // }
       
    }
    public function boot()
    {
        setlocale(LC_MONETARY, 'en_US.UTF-8');
        self::checklogin();
        $configurableItems = Helper::getConfigurableItemValue();
        if(count($configurableItems) > 0 ){
            foreach ($configurableItems as $key => $value) {
                switch ($value->name) {
                    case 'Phase1Amount':
                        Config::set('constants.phase1Amt', $value->value);
                        break;
                    
                    case 'Phase2Amount':
                        Config::set('constants.phase2Amt', $value->value);
                        break;
                    
                    case 'AssessmentRangeFrom':
                        Config::set('constants.caseRangeFrom', $value->value);
                        break;
                    
                    case 'AssessmentRangeTo':
                        Config::set('constants.caseRangeTo', $value->value);
                        break;
                    
                    case 'DifferentialPercentinRange1':
                        $getSixPercent = ((100-$value->value)/100);
                        Config::set('constants.getSixPercent', $getSixPercent);
                        break;
                    
                    case 'DifferentialPercentinRange2':
                        $getThreePercent = ((100-$value->value)/100);
                        Config::set('constants.getThreePercent', $getThreePercent);
                        break;
                    
                    case 'DifferentialPercentinRange3':
                        $getTwoPercent = ((100-$value->value)/100);
                        Config::set('constants.getTwoPercent', $getTwoPercent);
                        break;
                    
                    case 'OutlierPercentForRecentSale':
                        $getTwentyPercent = ((100-$value->value)/100);
                        Config::set('constants.getTwentyPercent', $getTwentyPercent);
                        break;
                    
                    case 'OutlierPercentForNonRecentSale':
                        $getTenPercent = ((100-$value->value)/100);
                        Config::set('constants.getTenPercent', $getTenPercent);
                        break;
                    
                    case 'ExcludeLivingAreaAboveAmount':
                        Config::set('constants.excludeLivingAreaAboveAmount', $value->value);
                        break;
                    
                    case 'RecentSaleMonthsBefore':
                        Config::set('constants.recentSaleMonthsBefore', $value->value);
                        break;
                    
                    case 'RecentSaleMonthsAfter':
                        Config::set('constants.recentSaleMonthsAfter', $value->value);
                        break;
                    
                    case 'MinimumTaxSavings':
                        Config::set('constants.minimumTaxSavings', $value->value);
                        break;
                    
                    case 'LivingAreaVarianceFilterPercent':
                        Config::set('constants.livingAreaVarianceFilterPercent', $value->value);
                        break;
                    
                    case 'livingAreaVarianceFilterPercentForGreater':
                        Config::set('constants.livingAreaVarianceFilterPercentForGreater', $value->value);
                        break;
                    
                    case 'Tier1OutlierPercent':
                        Config::set('constants.tier1OutlierPercent', $value->value);
                        break;
                    
                    case 'Tier1Outlier2Percent':
                        Config::set('constants.tier1Outlier2Percent', $value->value);
                        break;
                    
                    case 'MinCompsForAppeal':
                        Config::set('constants.minCompsForAppeal', $value->value);
                        break;
                    
                    case 'MaxCompsForAppeal':
                        Config::set('constants.maxCompsForAppeal', $value->value);
                        break;
                    
                    default:
                        # code...
                        break;
                }
            }
        }

        //echo config('constants.phase1Amt'); die;
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('path.public', function() {
            return realpath(__DIR__.'/../../../');
        });
    }

  
}
