@extends('layouts.app')
@section('title')
    @section('pageTitle', 'VIEW AND DOWNLOAD REPORTS')
@endsection
@section('css')
<link href="{{ asset('/project/resources/assets/customer/css/top-comparable.css') }}" rel="stylesheet">
<link href="{{ asset('/project/resources/assets/customer/css/custom_common.css') }}" rel="stylesheet">
@endsection
@section('content')
	<?php
	$totalPriceGross = "";
	$subjectCompsDetail['subject']->lat;

		$subjectCompsDetail['subject']->long;

	
		//$sale_divide_grossARea=($compDetails->com_details['sale_price']/(($compDetails->com_details['gross_living_area'] != '')?($compDetails->com_details['gross_living_area']):1));
	 ?>
	<div class="outer-search-list">
	   <div class="inner-top-search-list">
	      <ul>
	         <li class="active">
	            <a href="javascript:;">01</a>
	         </li>
	         <li class="active">
	            <a href="javascript:;">02</a>
	         </li>
	      </ul>
	      <div class="inner-wrapper-top-search-list">
	         <ul>
	            <li>
	               Make Payment
	            </li>
	            <li>
	               View And Download Reports
	            </li>
	         </ul>
	      </div>
	   </div>
	   <div class="search-list">
	      <div class="estimate-value">
	         <div class="outer-estimate-text">
	            <div class="estimate-value-text">
	               <h4>Owner's Estimate of Value - Sale Comparable Report</h4>
	            </div>
	            <div class="estimate-value-text">
	               <a href="{{ asset($subjectCompsDetail['pdf_link']).'/'.Request::segment(2) }}">View and download reports</a>
	            </div>
	         </div> 
	         <?php 
	      //echo "<pre>e";print_r($subjectCompsDetail);exit;
	        $basementAreaSubject='0';
	        if($subjectCompsDetail['subject']->corelogic_response != ''){
	        	$corelogic_response=json_decode($subjectCompsDetail['subject']->corelogic_response,true);
	        	$basementAreaSubject=$corelogic_response['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_BASEMENT']['@attributes']['_AreaSquareFeet'];
	        	//echo "<pre>";print_r($corelogic_response['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_BASEMENT']['@attributes']['_AreaSquareFeet']);exit;
	        }
	         $grossLivingAreaSubject=$subjectCompsDetail['subject']->square_footage;

	         ?>
	         <div class="estimate-value-table">
	            <div class="estimate-value-inner-table">
	               <div class="inner-estimate-value-inner-table">
	                  <table  cellpadding="0" cellspacing="0" class="inner-wrapper-estimate-table">
	                     <tr data-name="1s" >
	                        <th>
	                           Subject
	                        </th>
	                     </tr>
	                     <tr data-name="2s"> 
	                        <td class="Kalorama">
	                           {{ $subjectCompsDetail['subject']->sub_address }}
	                        </td>
	                     </tr>
	                     <tr data-name="3s">
	                        <td>
	                           <div class="table-image">
	                              <img src="{{ $subjectCompsDetail['subject']->subjectImage }}" width="100%">
	                           </div> 
	                        </td>
	                     </tr>
	                     <tr data-name="4s">
	                        <td>
	                           <table cellpadding="0" cellspacing="0" class="inner-estimate-value-table">
	                              <tr data-name="5s">
	                                 <td class="Proximity">
	                                    Proximity to Subject
	                                 </td>
	                                 <td>
	                                 </td>
	                              </tr>
	                              <tr data-name="6s">
	                                 <td>
	                                    Sale Price
	                                 </td>
	                                 <td>
	                                 <?php
	                                 	setlocale(LC_MONETARY, 'en_US');
										//echo money_format('%i', $number) 

	                                 ?>
	                                    $ {{ ($subjectCompsDetail['subject']->subjectOtherData != null)?(money_format('%!n', $subjectCompsDetail['subject']->subjectOtherData->sale_price)):'N/A' }}
	                                 </td>
	                              </tr>
	                              <tr data-name="7s">
	                                 <td  class="">
	                                    Sale Price/Gross Living Area
	                                 </td>
	                                <?php
	                                $subjectSalePrice=($subjectCompsDetail['subject']->subjectOtherData != null)?($subjectCompsDetail['subject']->subjectOtherData->sale_price):0;
									$totalLivingAreaSubject=$subjectCompsDetail['subject']->square_footage;
									$subjectPriceDivideBYGrossLiving=($subjectSalePrice/$totalLivingAreaSubject);
									//echo $subjectSalePrice.'<br/>'.$grossLivingAreaSubject;
									?>
									<td>
	              						$ {{ money_format('%!n', $subjectPriceDivideBYGrossLiving) }}
	                                </td>
	                              </tr>
	                              <!--tr data-name="8s">
	                                 <td>
	                                    Data Source(s)
	                                 </td>
	                                 <td>
	                                 </td>
	                              </tr-->
	                              <tr data-name="9s">
	                                 <td>
	                                    VALUE ADJUSTMENTS
	                                 </td>
	                                 <td>
	                                    DESCRIPTION
	                                 </td>
	                              </tr>
	                              <!--tr data-name="10s">
	                                 <td class="sale">
	                                    Sale or Financing <br>Concessions
	                                 </td>
	                                 <td class="sale">
	                                    @ market
	                                 </td>
	                              </tr-->
	                              <tr data-name="11s">
	                                 <td>
	                                    Date of Sale
	                                 </td>
	                                 <td>
	                                 {{ ($subjectCompsDetail['subject']->subjectOtherData != null && $subjectCompsDetail['subject']->subjectOtherData != '' && $subjectCompsDetail['subject']->subjectOtherData->sale_date != '0000-00-00')?(date('m/d/Y',strtotime( $subjectCompsDetail['subject']->subjectOtherData->sale_date))):'N/A' }}
	                                 </td>
	                              </tr>
	                              <!--tr data-name="12s">
	                                 <td>
	                                    Leasehold/Fee Simple
	                                 </td>
	                                 <td>
	                                    Fee Simple
	                                 </td>
	                              </tr-->
	                              <tr data-name="13s">
	                                 <td class="site">
	                                    Site (SF)
	                                 </td>
	                                 <td>
	                                    {{ number_format(isset($subjectCompsDetail['subject']->parcel_size)?($subjectCompsDetail['subject']->parcel_size):'0') }}
	                                 </td>
	                              </tr>
	                              <tr data-name="14s">
	                                 <td class="exterior">
	                                    Exterior
	                                 </td>
	                                 <td class="exterior">
	                                    {{ $subjectCompsDetail['subject']->exterior}}
	                                 </td>
	                              </tr>
	                              <tr data-name="15s">
	                                 <td>
	                                    Year Built
	                                 </td>
	                                 <td>
	                                 {{ (count($subjectCompsDetail['subject']) && $subjectCompsDetail['subject']->year_built != '')?($subjectCompsDetail['subject']->year_built):'N/A' }}
	                                 
	                                 </td>
	                              </tr>
	                              <!--tr data-name="16s">
	                                 <td>
	                                    Year Renovated
	                                 </td>
	                                 <td>
	                                 </td>
	                              </tr-->
	                              <!--tr data-name="17s">
	                                 <td>
	                                 </td>
	                                 <td>
	                                    Total | Bdrms | Baths
	                                 </td>
	                              </tr-->
	                              <tr data-name="18s">
	                                 <td class="sale">
	                                    Bathroom Count
	                                 </td>
	                                 <td class="sale">
	                              {{ $subjectCompsDetail['subject']->bathrooms }}
	                                 </td>
	                              </tr>	
	                              <tr data-name="19s">
	                                 <td class="gross">
	                                    Total Living Area (SF)
	                                 </td>
	                                 <td>
	                                 {{ ($grossLivingAreaSubject)?number_format(($grossLivingAreaSubject)):number_format(0)   }}
	                                 </td>

	                              </tr>
	                              <tr data-name="20s">
	                                 <td>
	                                    Basement
	                                 </td>
	                                 <td>
	                                    {{ ($basementAreaSubject != '' && $basementAreaSubject>0)?'Yes':'No' }}
	                                 </td>
	                              </tr>
	                              <tr data-name="21s">
	                                 <td class="basement">
	                                    Basement Area (SF)
	                                 </td>
	                                 <td class="basement">
	                                   {{ ($basementAreaSubject != '')?(number_format($basementAreaSubject)):'0' }}
	                                 </td>
	                              </tr>
	                              <!--tr data-name="22s">
	                                 <td>
	                                    Heating/Cooling
	                                 </td>
	                                 <td>
	                                    Typical
	                                 </td>
	                              </tr>
	                              <tr data-name="23s">
	                                 <td>
	                                    Energy Efficient Items
	                                 </td>
	                                 <td>
	                                    Typical
	                                 </td>
	                              </tr-->
	                              <tr data-name="24s">
	                                 <td class="garage-car">
	                                    Garage/Carport  
	                                 </td>
	                                 <td>
                                 	<?php 
                                 		$garageCount = ($subjectCompsDetail['subject']->garage!="")?$subjectCompsDetail['subject']->garage:'0';
                                 	?>
	                                    {{ $garageCount }} / {{ $subjectCompsDetail['subject']->carport }}
	                                 </td>
	                              </tr>
	                              <tr data-name="25s">
	                                 <td>
	                                    Porch
	                                 </td>
	                                 <td>
	                                    {{ ($subjectCompsDetail['subject']->porch_deck != 0)?'Yes':'No' }}
	                                 </td>
	                              </tr>
	                              <tr data-name="25s">
	                                 <td>
	                                    Patio
	                                 </td>
	                                 <td>
	                                    {{ ($subjectCompsDetail['subject']->patio != 0)?'Yes':'No' }}
	                                 </td>
	                              </tr>
	                              <tr data-name="25s">
	                                 <td>
	                                    Deck
	                                 </td>
	                                 <td>
	                                    {{ ($subjectCompsDetail['subject']->porch_deck != 0)?'Yes':'No' }}
	                                 </td>
	                              </tr>
	                              <tr data-name="25s">
	                                 <td>
	                                    Pool
	                                 </td>
	                                 <td>
	                                 {{ ($subjectCompsDetail['subject']->swimming_pool != 0)?'Yes':'No' }}
	                           
	                                 </td>
	                              </tr>
	                              <tr data-name="26s">
	                                 <td class="fire">
	                                    Fireplace(s)
	                                 </td>
	                                 <td class="fire">
	                                    {{ $subjectCompsDetail['subject']->fireplace }}
	                                 </td>
	                                 <?php $subjectCompsDetail['subject']->fireplace.'<br/>'; ?>
	                              </tr>
	                              <!--tr data-name="27s">
	                                 <td>
	                                    Other
	                                 </td>
	                                 <td>
	                                    None
	                                 </td>
	                              </tr-->
	                              <tr data-name="28s">
	                                 <td class="net">
	                                    Net Adjustment (Total)
	                                 </td>
	                                 <td class="net">
	                                 </td>
	                              </tr>
	                              <tr data-name="29s">
	                                 <td colspan="2">
	                                    Adjusted Sale Price of Comparables
	                                 </td>
	                              </tr>
	                           </table>
	                        </td>
	                     </tr>
	                     <tr>
	                        <td class="table-footer">
	                           Initial Estimate of Value:
	                        </td>
	                     </tr>
	                  </table>
	               </div>
	            </div>
	            <div class="wrapper1">
				    <div class="div1">
				    </div>
				</div>
	            <div class="second-estimate-table wrapper2">
	            	<?php 
	            	$TotalCalculatedArray=array();
	            	//echo "<pre>";print_r($subjectCompsDetail['comparables']);exit;
	            	$counting = 0; $adjustedSalePrice=array(); ?>
	            	@if(isset($subjectCompsDetail['comparables']) && count($subjectCompsDetail['comparables'])>0)
						@foreach($subjectCompsDetail['comparables'] as $key => $compDetails)
			               <div class="middle-estimate-table">
			                  <table  cellpadding="0" cellspacing="0" class="inner-wrapper-estimate-table">
			                     <tr data-name="1">
			                        <th>
			                           COMPARABLE SALE NO. {{ $key+1 }}
			                        </th>
			                     </tr> 
			                     <tr data-name="2">
			                        <td class="Kalorama">
			                           {{ $compDetails->com_address }}
			                        </td>
			                     </tr>
			                     <tr data-name="3">
			                        <td>
			                           <div class="table-image">
			                              <img src="{{ $compDetails->comparableImage }}" width="100%">
			                           </div>
			                        </td>
			                     </tr>
			                     <tr data-name="4">
			                        <td>
			                           <table cellpadding="0" cellspacing="0" class="inner-estimate-value-table">
			                              <tr data-name="1n">
			                                 <td colspan="2" class="Proximity">
			                                    {{ $compDetails->com_details['distance_from_subject'] }} miles
			                                 </td>
			                              </tr>
			                              <tr data-name="2n">
			                                 <td>
			                                 </td>
			                                 <td class="dollar">
			                                    $ {{ money_format('%!n', $compDetails->com_details['sale_price']) }}
			                                 </td>
			                              </tr>
			                              <tr data-name="3n">
			                                 <td class="right">
			                                 <?php
			                                 	$sale_divide_totalARea=($compDetails->com_details['sale_price']/
			                                 	(($compDetails->com_details['square_footage'] != '')?($compDetails->com_details['square_footage']):1));
			                                

			                                 ?>
			                                    $ {{ money_format('%!n', $sale_divide_totalARea) }}
			                                 </td>
			                                 <td>
			                                 </td>
			                              </tr>
			                              <!--tr data-name="4n">
			                                 <td>
			                                    MRIS -
			                                 </td>
			                                 <td>
			                                 </td>
			                              </tr-->
			                              <tr data-name="5n">
			                                 <td>
			                                    DESCRIPTION
			                                 </td>
			                                 <td>
			                                    $ Adjustment
			                                 </td>
			                              </tr>
			                              <!--tr data-name="6n">
			                                 <td class="sale">
			                                    Subsidy
			                                 </td>
			                                 <td class="sale">
			                                    $ - {{ money_format('%!n', $compDetails->com_details['subsidy']) }}

			                                 </td>
			                              </tr-->
			                              <tr data-name="7n">
			                                 <td>
			                                    {{ date('m/d/Y', strtotime($compDetails->com_details['date_of_sale'])) }}
			                                 </td>
			                                 <td>
			                                    --
			                                 </td>
			                              </tr>
			                              <!--tr data-name="8n">
			                                 <td>
			                                    Fee Simple
			                                 </td>
			                                 <td>
			                                  	--
			                                 </td>
			                              </tr-->
			                              <tr data-name="9n">
			                                 <td>

			                                    {{ number_format($compDetails->parcel_size) }}
			                                 </td>
			                                 <td>
			                                   --
			                                 </td>
			                              </tr>
			                              <tr data-name="10n">
			                                 <td class="exterior">
			                                    {{ $compDetails->com_details->exterior }}
			                                 </td>
			                                 <td>
			                                   --
			                                 </td>
			                              </tr>
			                              <tr data-name="11n">
			                                 <td>
			                                    {{ $compDetails->com_details['year_built'] }}
			                                 </td>
			                                 <td>
			                                    <!--$- -->
			                                    --
			                                 </td>
			                              </tr>
			                              <!--tr data-name="12n">
			                                 <td>
			                                 </td>
			                                 <td>
			                                 
			                                    
			                                 </td>
			                              </tr-->
			                              <!--tr data-name="13n">
			                                 <td>
			                                    Total | Bdrms | Baths
			                                 </td>
			                                 <td>
			                                 </td>
			                              </tr-->
			                              <tr data-name="14n">
			                                 <td class="sale">
			                                    <!--($compDetails->bedrooms + $compDetails->bathrooms)  | $compDetails->bedrooms  |  $compDetails->bathrooms-->
			                                    {{ $compDetails->bathrooms }}		
			                                    </td>
			                                 <td class="sale">
			                                 <?php  $BatroomPrice=($compDetails->com_details['total_bathrooms'] != 0)?(money_format('%!n',($compDetails->com_details['total_bathrooms']))):'0'; ?>
			                                     $ <span class="special_price">{{ $BatroomPrice }}</span>
			                                 </td>
			                              </tr>
			                              <tr data-name="15n">
			                                 <td>
			                                    {{ ($compDetails->com_details['square_footage'] != '')?(number_format($compDetails->com_details['square_footage'])):0 }}
			                                 </td>
			                                 <td>
			                                 <?php
			                                 $grossLivingAreaCOMP=($compDetails->com_details['gross_living_area'] != '')?($compDetails->com_details['gross_living_area']):0;

			                                 ?>                                 	

			                                    $ <span class="special_price">{{ ($compDetails->com_details['square_footage_price'] != '')?(money_format('%!n',$compDetails->com_details['square_footage_price'])):0 }}</span>
			                                 </td>
			                              </tr>
			                              <tr data-name="16n">
			                                 <td>
			                                    {{ ($compDetails->com_details['basement'] > 0 && $compDetails->com_details['basement'] != '')?'Yes':'No' }}
			                                 </td>
			                                 <td>	
			                                    --
			                                 </td>
			                              </tr>
			                              <tr data-name="17n">
			                                 <td class="basement">
			                                    N/A
			                                 </td>
			                                <?php $baseMent=($compDetails->com_details['basement'] != '')?($compDetails->com_details['basement']):0; ?>
			                                
			                                 <td class="basement">--</td>
			                              </tr>
			                              <!--tr data-name="18n">
			                                 <td>
			                                    Typical
			                                 </td>
			                                 <td>
			                                  
			                                 </td>
			                              </tr>
			                              <tr data-name="19n">
			                                 <td>
			                                    Typical
			                                 </td>
			                                 <td>
			                                    
			                                 </td>
			                              </tr-->
			                              <tr data-name="20n">
			                                 <td>
			                                    {{ $compDetails->garage }} / {{ $compDetails->carport }}
			                                 </td>
			                                 <td>

			                                    $ <span class="special_price">{{ ($compDetails->com_details['garage'] !='' && $compDetails->com_details['garage'] != '-')?(money_format('%!n', $compDetails->com_details['garage'])): (money_format('%!n', 0)) }}</span>
			                                 </td>
			                              </tr>
			                              <tr data-name="21n">
			                                 <td>
			                                   No
			                                 </td>
			                                 <td>
			                                    $ <span class="special_price">0</span>
			                                 </td>
			                              </tr>
			                              <tr data-name="21n">
			                                 <td>
			                                    No
			                                 </td>
			                                 <td>
			                                    $ <span class="special_price">0</span>
			                                 </td>
			                              </tr>
			                              <tr data-name="21n">
			                                 <td>
			                                     No
			                                 </td>
			                                 <td>
			                                    $ <span class="special_price">0</span>
			                                 </td>
			                              </tr>
			                              <tr data-name="21n">
			                                 <td>
			                                    No
			                                 </td>
			                                 <td>
			                                    $ <span class="special_price">0</span>
			                                 </td>
			                              </tr>
			                              <tr data-name="22n">
			                                 <td>
			                                 <?php $fireplaceCount=($compDetails->fireplace != '')?($compDetails->fireplace):'0'; ?>
			                                    {{ $fireplaceCount }}
			                                 </td>
			                                 <td>
			                                 $  <span class="special_price">{{ money_format('%!n',$compDetails->com_details['fireplace']) }}</span>
			                                  <!--  money_format('%!n', (($subjectCompsDetail['subject']->fireplace-$fireplaceCount)*12000))-->
			                                 </td>
			                              </tr>
			                              <!--tr data-name="23n">
			                                 <td>
			                                    None
			                                 </td>
			                                 <td>
			                                    $ ({{ money_format('%!n', 0)}})
			                                 </td>
			                              </tr-->
			                              <tr data-name="24n">
			                                 <td>
			                                  
			                                 </td>
			                                 <td>
			                                 <?php
			                                  $adjustedPriceFinal=$compDetails->com_details['net_adjustment'];
			                                 /*$adjustedPriceFinal=(($compDetails->com_details['total_bathrooms']) + (($compDetails->com_details['square_footage_price'] != '')?($compDetails->com_details['square_footage_price']):0) + ($compDetails->com_details['basement']) + ($compDetails->com_details['fireplace']) + (($compDetails->com_details['garage'] !='' && $compDetails->com_details['garage'] != '-')?($compDetails->com_details['garage']): 0) + (($compDetails->com_details['cartport'] !='' && $compDetails->com_details['cartport'] != '-')?($compDetails->com_details['cartport']): 0));*/


			                                 ?>
			                                 <!-- 70175  Subsity + Other-->
			                                    $ <span class="special_price"> {{ money_format('%!n',$adjustedPriceFinal) }}</span>
			                                 </td>
			                              </tr>
			                              <tr data-name="25n">
			                                 <td>
			                                 <?php $Net_Adjustment_price=money_format('%!n',$compDetails->com_details['price_after_adjustment']); ?>
			                                   $ {{  $Net_Adjustment_price }}
			                                 </td>
			                                 <td>
			                                 <?php echo '$ <span class="special_price">'.money_format('%!n',$compDetails->com_details['sale_price_divided_sf']).'</span>'; ?>
			                                 
			                                </td>
			                              </tr>
			                           </table>
			                        </td>
			                     </tr> 
			                     
			                  </table>
			               </div>
			               <?php $counting++; ?>
		               @endforeach
		               <?php 
		               //echo round(((array_sum($TotalCalculatedArray)/sizeof($TotalCalculatedArray))/$grossLivingAreaSubject),5);exit;
		                ?>
		               <table style="width: 60%;" cellpadding="0" cellspacing="0" class="inner-wrapper-estimate-table"> 

			      			<tr>
				                <td class="table-footer" colspan="2">
				               		$ {{ money_format('%!n',$subjectCompsDetail['subject']->subjectOtherData->appeal_amount) }}
				                
				                    
				                </td>
				            </tr>
			            </table>
	               @endif
	            </div>
	            <!--second-estimate-table-->
	         </div>
	         <div class="bottom-table">
	            <table cellpadding="0" cellspacing="0" class="inner-bottom-table">

	               <tr>
	                  <td style="width: 34%;" class="additional">
	                     Additional Owner Adjustments
	                  </td>
	                  <td style="width: 10%;">
	                  </td>
	                  <td class="additional">
	                     Provide a brief description of these adjustments:
	                  </td>
	               </tr>
	               <?php  $totalLookupsPrice=0; 
	               $alreadyExists=array();
	               //echo "<pre>eee";print_r($subjectCompsDetail['subject']->lookUpsDetail);exit;
	               ?>
	               @if(count($subjectCompsDetail['subject']->lookUpsDetail))
	               		@foreach($subjectCompsDetail['subject']->lookUpsDetail as $lookupDetail)
			               <tr>
			               
			                  <td>
			                  <?php
			                  $var='';
			                  $lookup_count='';
			                  $FullLookupName=$lookupDetail->name;
			                  $lkDesc=$lookupDetail->description;
			                  	if($lookupDetail->parent_lookup_id==15){
			                 		$var='Renovate';
			                 		$lookup_count=$lookupDetail->lookup_count;
			                 		$FullLookupName=$var.' '.$lookup_count.' '.$lookupDetail->name;	
			                 		$lkDesc = '$8000 to $30,000/EACH';
			                 	}
			                 	if(!in_array($FullLookupName,$alreadyExists)){
                            		$alreadyExists[]=$FullLookupName;
			                  ?>
			                  {{ ucfirst($FullLookupName) }}
			                  </td>
			                 	<?php 

			                 	//echo $lookupDetail->parent_lookup_id.'<br/>';
			                 	$child_details = Helper::getChildLookupDetail($lookupDetail->lookup_id);
			                 		//echo "<pre>eeeee";print_r($child_details);
                                    $additional_homeowner_question_lookup_id = (count($child_details)) ? $child_details->lookup_id : $lookupDetail->lookup_id; 
                                    
                                    $maxRange = $lookupDetail->value;

                                    $maxLimitValue = "$".$maxRange;
                                  
                                    $minRange = $lookupDetail->value1;
                                    $minimitValue = "$".$minRange;

                                    $percentageValue = $lookupDetail->value2;

                                    $concateVal = $maxRange."-".$minRange."-".$percentageValue;
                                ?>

			               
			                  <td class="bottom-color test">

			                    $ {{ money_format('%!n', ($lookupDetail->lookup_value)) }}

			                    <?php $totalLookupsPrice = $totalLookupsPrice + $lookupDetail->lookup_value; ?>
			                  </td>
			                  <td>
			                  	<?php 
                                    $str = str_replace('$maxLimitValue', $maxLimitValue, $lkDesc);
                                    
                                    echo ucfirst(str_replace('$minimitValue', $minimitValue, $str));
                                ?>
			                     
			                  </td>
			               </tr>
			                <?php } ?>
	               		@endforeach
	               @endif
	               <tr>
	                  <td colspan="3">
	                     <table cellspacing="0" cellpadding="0"  class="final-owner">
	                        <tr>
	                           <td>
	                              FINAL OWNERSHIP ESTIMATE OF VALUE
	                           </td>
	                           <td> $ {{ money_format('%!n',(($subjectCompsDetail['subject']->subjectOtherData->appeal_amount) - ($totalLookupsPrice))) }}</td>
	                           <td> 
	                           </td>
	                        </tr>
	                     </table>
	                  </td>
	               </tr>
	               <!--tr>
	                  <td class="LIC">
	                     Documents prepared by: Assessment Solutions, LLC
	                  </td>
	                  <td>
	                  </td>
	                  <td>
	                  </td>
	               </tr-->
	            </table>
	         </div>
	      </div>
	   </div>
	</div>

@endsection
<style>
.wrapper1, .wrapper2{width: 1615px; border: none 0px RED;
overflow-x: scroll; overflow-y:hidden;}
.wrapper1{height: 20px; }
.wrapper2{height: 200px; }
.div1 {width:1000px; height: 20px; }
.div2 {width:1000px; height: 200px; background-color: #88FF88;
overflow: auto;}


</style>
@section('js')
<script>
$(function(){
	$(".wrapper1").css('width',($('.middle-estimate-table').length*323));
	$(".div1").css('width',($('.middle-estimate-table').length*323));
	//alert($('.middle-estimate-table').length*323);
    $(".wrapper1").scroll(function(){
        $(".wrapper2")
            .scrollLeft($(".wrapper1").scrollLeft());
    });
    $(".wrapper2").scroll(function(){
        $(".wrapper1")
            .scrollLeft($(".wrapper2").scrollLeft());
    });
});
function imgError(image) {
    image.onerror = "";
    image.src = "/images/noimage.gif";
    return true;
}
</script>
@endsection