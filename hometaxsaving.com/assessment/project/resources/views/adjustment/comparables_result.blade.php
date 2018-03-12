@extends('app')
@section('title')
    Comparables Result
@endsection
@section('css')
<!-- Data Table CSS -->
<link href="{{ asset('/project/resources/assets/customer/css/jqueryDataTableCss.css') }}" rel="stylesheet">
<style>
    .comparable_heading {
        text-align: center;
    } 
    .nosort.sorting, .nosort.sorting_asc, .nosort.sorting_desc {
        background-image: none !important;
    }
    .tsg-inner-wrapper {
        max-width: 100% !important;
            padding: 0 15px !important;
    }
    .tsg-latest {
    padding: 0 !important;
}
.contactdetailform {
    padding: 0px 0 25px 0 !important;
}
table.table.table-striped.dataTable th, table.table.table-striped.dataTable td {
    border: 1px solid #d2d2d2 !important;
    border-collapse: collapse !important;
    padding: 10px;
}
table.table.table-striped.dataTable th {
    background-color: #1570c3;
    color: #fff;
    vertical-align: middle;
}
table.dataTable{
    margin: 0 !important;
}
.tsg-whole-Wrapper h2 {
    color: #1570c3;
    font-size: 28px;
    font-weight: 600;
    margin-bottom: 15px;
}
.dataTables_wrapper.no-footer .dataTables_scrollBody{
    border-bottom: 0 !important;
}
.dataTables_wrapper .dataTables_filter input {
    margin-left: 0.5em;
    border: 1px solid #d2d2d2;
    margin-bottom: 10px;
    padding: 5px;
    outline: none;
}
.dataTables_length select {
    border: 1px solid #d2d2d2;
    -webkit-appearance: none;
       padding: 5px 15px;
    border-radius: 0;
        background: rgba(0, 0, 0, 0) url(/select-drop-down.png) no-repeat scroll right 18px center;
        outline: none;
}
</style>
@endsection
@section('content')

<div class="tsg-inner-wrapper"> 
    <div class="tsg-latest add-user-payment-page">
        <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12">
            <h2>Search Result:</h2>
            <a href="{{ asset('/documents/adjustment formulas.docx') }}" title="Ajustment Document" download>Adjustment Document</a>
        </div>
            
        <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12">    
            
            <div class="contactdetailform">
                <h2>Subject Property:</h2>
                @if(isset($subject_property) && count($subject_property) > 0)
                    <div class="box-body no-padding box-shadow colored-box">
                        <table class="table table-striped" id="subject_property">
                            <thead>
                                <tr>
                                    <th class="nosort comparable_heading">Type of House</th>
                                    <th class="nosort comparable_heading">Sale Price</th>
                                    <!--th class="nosort comparable_heading">Date of Sale</th-->
                                    <th class="nosort comparable_heading">Parcel Size</th>
                                    <th class="nosort comparable_heading">Above Grade Square Footage</th>
                                    <th class="nosort comparable_heading">Total Bedrooms</th>
                                    <th class="nosort comparable_heading">Total Bathrooms</th>
                                    <th class="nosort comparable_heading">Finished Space</th>
                                    <th class="nosort comparable_heading">Unfinished Space</th>
                                    <th class="nosortcomparable_heading">Garage</th>
                                    <th class="nosort nosort comparable_heading">Carport</th>
                                    <th class="comparable_heading">Swimming Pool</th>
                                    <th class="nosort comparable_heading">Fireplace</th>
                                    <th class="nosort comparable_heading">Sale Date</th>
                                    <th class="nosort comparable_heading">Applied Case 1</th>
                                    <th class="nosort comparable_heading">Appeal Recommendation</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($subject_property) > 0)
                                    <tr>
                                        <td>{{ $subject_property['type_of_house'] }}</td>
                                        <td>{{ $subject_property['sale_price'] }}</td>
                                        <!--td>{{ $subject_property['date_of_sale'] }}</td-->
                                        <td>{{ $subject_property['parcel_size'] }}</td>
                                        <td>{{ $subject_property['square_footage'] }}</td>
                                        <td>{{ $subject_property['total_bedrooms'] }}</td>
                                        <td>{{ $subject_property['total_bathrooms'] }}</td>                                      
                                        <td>{{ $subject_property['finished_space'] }}</td>
                                        <td>{{ $subject_property['unfinished_space'] }}</td>
                                        <td>{{ $subject_property['garage_count'] }}</td>
                                        <td>{{ $subject_property['carport_exist'] }}</td>
                                        <td>{{ $subject_property['pool_exist'] }}</td>
                                        <td>{{ $subject_property['fireplace_count'] }}</td>

                                        <td>{{ $subject_property['date_of_sale'] }}</td>
                                        @if($subject_property['case_1'] == 1 )
                                            <td>Yes</td>
                                        @else
                                            <td>No</td>
                                        @endif

                                        <!-- @if($subject_property['no_appeal_recommendation'] == 1 )
                                            <td>No Appeal Recommendation</td>
                                        @elseif($subject_property['case_1'] == 1 && $subject_property['no_appeal_recommendation'] == 0 )
                                            <td>{{ $subject_property['sale_price'] }}</td>
                                        @else
                                            <td> - </td>
                                        @endif -->
                                        @if($subject_property['appeal_amount'] != "" && $subject_property['no_appeal_recommendation'] == 0 )
                                            <td> {{ $subject_property['appeal_amount'] }} </td>
                                        @elseif ($subject_property['no_appeal_recommendation'] == 1)
                                            <td> No Appeal </td>
                                        @else
                                            <td> - </td>
                                        @endif
                                    </tr>
                                @endif
                            </tbody>
                        </table>                    
                    </div>
                @endif
            </div>
            <!-- Hidden form start here -->
            <div class="tsg-inner-wrapper"> 
               <!--  {!! Form::open(['url' => 'generate-sheet', 'id' => 'generate-sheet_form', 'novalidate' => 'novalidate']) !!}
                
                <input id="street" name="street" value="{{ $form_data['street'] }}" type="hidden">
                
                <input id="city" name="city" value="{{ $form_data['city'] }}" type="hidden">
                
                <input id="state" name="state" value="{{ $form_data['state'] }}" type="hidden">
                
                <input id="postal_code" name="postal_code" value="{{ $form_data['postal_code'] }}" type="hidden">
                
                <input id="total_assessment_value" name="total_assessment_value" value="{{ $form_data['total_assessment_value'] }}" type="hidden">
                
                <input id="county_id" name="county_id" value="{{ $form_data['county_id'] }}" type="hidden">

                <div class="tsg-btn-wrap add_user_button">  
                    {!! Form::submit('Download CSV File',array('class'=>"btn btn-ctrl")); !!} 
                </div>


                {!! Form::close() !!}  -->

                <a href="{{ url('/download-top-adjusted-sales') }}" class="btn btn-ctrl">Download Top Comparables</a>
                
            </div>
            <!-- Hidden form end here -->

            @if(isset($subject_property['no_appeal_recommendation']) && @$subject_property['no_appeal_recommendation'] == 1)
                <div class="contactdetailform">
                    <h2>Top 5 Comparables:</h2>
                    <div style="color:red; font-size:14pt;">
                        <strong>
                            {{ $subject_property['no_appeal_message'] }}
                        </strong>
                    </div>
                </div>
            @endif

            @if(count($subject_property) > 0 && isset($subject_property['case_1']) && @$subject_property['case_1'] == 1)
            <div class="contactdetailform">
                @if(isset($comparables['final_comparables']) && count($comparables['final_comparables']) > 0)
                    <h2>Top 5 Comparables:</h2>
                    <div class="box-body no-padding box-shadow colored-box">
                        <table class="table table-striped" id="final_comparables">
                            <thead>
                                <tr>
                                    <th class="nosort comparable_heading" colspan="6">Comparable Details</th>
                                    <th class="nosort comparable_heading" colspan="2">Applied Case 1</th>
                                    <!--th class="nosort comparable_heading" colspan="3">Date of Sale</th-->
                                    <th class="nosort comparable_heading" colspan="3">Parcel Size</th>
                                    <th class="nosort comparable_heading" colspan="3">Interior vs Corner</th>
                                    <th class="nosort comparable_heading" colspan="3">Above Grade Square Footage</th>
                                    <th class="nosort comparable_heading" colspan="3">Total Bedrooms</th>
                                    <th class="nosort comparable_heading" colspan="3">Total Bathrooms</th>
                                    <th class="nosort comparable_heading" colspan="3">Finished Space</th>
                                    <th class="nosort comparable_heading" colspan="3">Unfinished Space</th>
                                    <th class="nosort comparable_heading" colspan="3">Garage</th>
                                    <th class="nosort comparable_heading" colspan="3">Carport</th>
                                    <th class="nosort comparable_heading" colspan="3">Swimming Pool</th>
                                    <th class="nosort comparable_heading" colspan="3">Fireplace</th>
                                </tr>
                                <tr>
                                    <th class="nosort comparable_heading">Comparable Type of House</th>
                                    <th class="nosort comparable_heading">Comparable Sale Price</th>
                                    <th class="nosort comparable_heading">Comparable Sale Date</th>
                                    <th class="nosort comparable_heading">Comparable Address</th>
                                    <th class="nosort comparable_heading">Comparable Distance from Subject Property</th>
                                    <th class="nosort comparable_heading">Comparable Sale Price After Adjustment Valuee</th>

                                    <th class="nosort comparable_heading">Adjusted SalePrice/SF +/- 1% range {{ $subject_property['subject_salePrice_minus_1_percent'] }} - {{ $subject_property['subject_salePrice_plus_1_percent'] }}</th>
                                    <th class="nosort comparable_heading">
                                        Living area variance percent +/- 20% range
                                        @if(count($subject_property) > 0)
                                            {{$subject_property['living_area_minus_twenty'] }} - {{$subject_property['living_area_plus_twenty'] }}
                                        @endif
                                    </th>
                                    <!--th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th-->
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($comparables['final_comparables']) > 0)
                                    @foreach($comparables['final_comparables'] as $final_comparable)

                                    @if($final_comparable['is_one_percent'] == "Yes")
                                    <tr style="background-color: skyblue;">
                                    @else
                                    <tr>
                                    @endif
                                        <td>{{ $final_comparable['comparable_type_of_house'] }}</td>
                                        <td>{{ $final_comparable['comparable_sale_price'] }}</td>
                                        <td>{{ $final_comparable['comparable_date_of_sale'] }}</td>
                                        <td><b>Street</b>{{ $final_comparable['comparable_address_street'] }} <br> <b>City</b>{{ $final_comparable['comparable_address_city'] }} <br> <b>State</b>{{$final_comparable['comparable_address_state'] }} <br> <b>County</b>{{ $final_comparable['comparable_address_county'] }} <br> <b>Zip Code</b>{{$final_comparable['comparable_address_zipcode'] }}</td>
                                        <td>{{ $final_comparable['distance_from_subject'] }}</td>
                                        <td>{{ $final_comparable['price_after_adjustment'] }}</td>

                                        <td>{{ $final_comparable['sale_price_divided_square_footage'] }}</td>

                                        <td>{{ $final_comparable['comparable_square_footage'] }}</td>
                                        <?php /*<td>{{ $final_comparable['comparable_date_of_sale'] }}</td>
                                        <td>{{ $final_comparable['formula_date_of_sale'] }}</td>
                                        <td>{{ $final_comparable['date_of_sale'] }}</td> */ ?>
                                        <td>{{ $final_comparable['comparable_parcel_size'] }}</td>
                                        <td>{{ $final_comparable['formula_parcel_size'] }}</td>
                                        <td>{{ $final_comparable['parcel_size'] }}</td>
                                        <td>-</td> <td>-</td> <td>-</td>
                                        <td>{{ $final_comparable['comparable_square_footage'] }}</td>
                                        <td>{{ $final_comparable['formula_square_footage'] }}</td>
                                        <td>{{ $final_comparable['square_footage'] }}</td>
                                        <td>{{ $final_comparable['comparable_total_bedrooms'] }}</td>
                                        <td>{{ $final_comparable['formula_total_bedrooms'] }}</td>
                                        <td>{{ $final_comparable['total_bedrooms'] }}</td>
                                        <td>{{ $final_comparable['comparable_total_bathrooms'] }}</td>
                                        <td>{{ $final_comparable['formula_total_bathrooms'] }}</td>
                                        <td>{{ $final_comparable['total_bathrooms'] }}</td>                                        
                                        <td>{{ $final_comparable['comparable_finished_space'] }}</td>
                                        <td>{{ $final_comparable['formula_finished_space'] }}</td>
                                        <td>{{ $final_comparable['finished_space'] }}</td>
                                        <td>{{ $final_comparable['comparable_unfinished_space'] }}</td>
                                        <td>{{ $final_comparable['formula_unfinished_space'] }}</td>
                                        <td>{{ $final_comparable['unfinished_space'] }}</td>
                                        <td>{{ $final_comparable['garage'] }}</td>
                                        <td>{{ $final_comparable['formula_garage'] }}</td>
                                        @if($final_comparable['garage'] != "-")
                                            <td>{{ $final_comparable['garage_count'] }}</td>
                                        @else
                                            <td>0</td>
                                        @endif
                                        <td>{{ $final_comparable['comparable_carport_exist'] }}</td>
                                        <td>{{ $final_comparable['formula_carport'] }}</td>
                                        <td>{{ $final_comparable['carport'] }}</td>
                                        <td>{{ $final_comparable['comparable_pool_exist'] }}</td>
                                        <td>{{ $final_comparable['formula_pool'] }}</td>
                                        <td>{{ $final_comparable['pool'] }}</td>
                                        <td>{{ $final_comparable['comparable_fireplace_count'] }}</td>
                                        <td>{{ $final_comparable['formula_fireplace'] }}</td>
                                        <td>{{ $final_comparable['fireplace'] }}</td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>                    
                    </div>
                @endif
            </div>

            <div class="contactdetailform">
                <h2>All Comparables:</h2>
                @if(isset($comparables['all_comparables']) && count($comparables['all_comparables']) > 0)
                    <div class="box-body no-padding box-shadow colored-box">
                        <table class="table table-striped" id="all_comparables">
                            <thead>
                                <tr>
                                    <th class="nosort comparable_heading" colspan="6">Comparable Details</th>             
                                    <th class="nosort comparable_heading" colspan="2">Applied Case 1</th>
                                    <!--th class="nosort comparable_heading" colspan="3">Date of Sale</th-->
                                    <th class="nosort comparable_heading" colspan="3">Parcel Size</th>
                                    <th class="nosort comparable_heading" colspan="3">Interior vs Corner</th>
                                    <th class="nosort comparable_heading" colspan="3">Above Grade Square Footage</th>
                                    <th class="nosort comparable_heading" colspan="3">Total Bedrooms</th>
                                    <th class="nosort comparable_heading" colspan="3">Total Bathrooms</th>
                                    <th class="nosort comparable_heading" colspan="3">Finished Space</th>
                                    <th class="nosort comparable_heading" colspan="3">Unfinished Space</th>
                                    <th class="nosort comparable_heading" colspan="3">Garage</th>
                                    <th class="nosort comparable_heading" colspan="3">Carport</th>
                                    <th class="nosort comparable_heading" colspan="3">Swimming Pool</th>
                                    <th class="nosort comparable_heading" colspan="3">Fireplace</th>
                                </tr>
                                <tr>
                                    <th class="nosort comparable_heading">Comparable Type of House</th>
                                    <th class="nosort comparable_heading">Comparable Sale Price</th>
                                    <th class="nosort comparable_heading">Comparable Sale Date</th>
                                    <th class="nosort comparable_heading">Comparable Address</th>
                                    <th class="nosort comparable_heading">Comparable Distance from Subject Property</th>
                                    <th class="nosort comparable_heading">Comparable Sale Price After Adjustment Valuee</th>

                                    <th class="nosort comparable_heading">Adjusted SalePrice/SF +/- 1% range {{ $subject_property['subject_salePrice_minus_1_percent'] }} - {{ $subject_property['subject_salePrice_plus_1_percent'] }}</th>

                                    <th class="nosort comparable_heading">
                                        Living area variance percent +/- 20% range
                                        @if(count($subject_property) > 0)
                                            {{$subject_property['living_area_minus_twenty'] }} - {{$subject_property['living_area_plus_twenty'] }}
                                        @endif
                                    </th>
                                    <!--th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th-->
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($comparables['all_comparables']) > 0)
                                    @foreach($comparables['all_comparables'] as $comparable)
                                    <tr>
                                        <td>{{ $comparable['comparable_type_of_house'] }}</td>
                                        <td>{{ $comparable['comparable_sale_price'] }}</td>
                                        <td>{{ $comparable['comparable_date_of_sale'] }}</td>
                                        <td><b>Street</b>{{ $comparable['comparable_address_street'] }} <br> <b>City</b>{{ $comparable['comparable_address_city'] }} <br> <b>State</b>{{$comparable['comparable_address_state'] }} <br> <b>County</b>{{ $comparable['comparable_address_county'] }} <br> <b>Zip Code</b>{{$comparable['comparable_address_zipcode'] }}</td>
                                        <td>{{ $comparable['distance_from_subject'] }}</td>
                                        <td>{{ $comparable['price_after_adjustment'] }}</td>

                                        <td>{{ $comparable['sale_price_divided_square_footage'] }}</td>

                                        <td>{{ $comparable['comparable_square_footage'] }}</td>
                                        <?php /*<td>{{ $comparable['comparable_date_of_sale'] }}</td>
                                        <td>{{ $comparable['formula_date_of_sale'] }}</td>
                                        <td>{{ $comparable['date_of_sale'] }}</td> */ ?>
                                        <td>{{ $comparable['comparable_parcel_size'] }}</td>
                                        <td>{{ $comparable['formula_parcel_size'] }}</td>
                                        <td>{{ $comparable['parcel_size'] }}</td>
                                        <td>-</td> <td>-</td> <td>-</td>
                                        <td>{{ $comparable['comparable_square_footage'] }}</td>
                                        <td>{{ $comparable['formula_square_footage'] }}</td>
                                        <td>{{ $comparable['square_footage'] }}</td>
                                        <td>{{ $comparable['comparable_total_bedrooms'] }}</td>
                                        <td>{{ $comparable['formula_total_bedrooms'] }}</td>
                                        <td>{{ $comparable['total_bedrooms'] }}</td>
                                        <td>{{ $comparable['comparable_total_bathrooms'] }}</td>
                                        <td>{{ $comparable['formula_total_bathrooms'] }}</td>
                                        <td>{{ $comparable['total_bathrooms'] }}</td>                                        
                                        <td>{{ $comparable['comparable_finished_space'] }}</td>
                                        <td>{{ $comparable['formula_finished_space'] }}</td>
                                        <td>{{ $comparable['finished_space'] }}</td>
                                        <td>{{ $comparable['comparable_unfinished_space'] }}</td>
                                        <td>{{ $comparable['formula_unfinished_space'] }}</td>
                                        <td>{{ $comparable['unfinished_space'] }}</td>
                                        <td>{{ $comparable['garage'] }}</td>
                                        <td>{{ $comparable['formula_garage'] }}</td>
                                        @if($comparable['garage'] != "-")
                                            <td>{{ $comparable['comparable_garage_count'] }}</td>
                                        @else
                                            <td>0</td>
                                        @endif
                                        <td>{{ $comparable['comparable_carport_exist'] }}</td>
                                        <td>{{ $comparable['formula_carport'] }}</td>
                                        <td>{{ $comparable['carport'] }}</td>
                                        <td>{{ $comparable['comparable_pool_exist'] }}</td>
                                        <td>{{ $comparable['formula_pool'] }}</td>
                                        <td>{{ $comparable['pool'] }}</td>
                                        <td>{{ $comparable['comparable_fireplace_count'] }}</td>
                                        <td>{{ $comparable['formula_fireplace'] }}</td>
                                        <td>{{ $comparable['fireplace'] }}</td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>                    
                    </div>
                @endif
            </div>
            @elseif(count($subject_property) > 0 && isset($subject_property['case_1']) && $subject_property['case_1'] == 0 && isset($subject_property['no_appeal_recommendation']) )
            <div class="contactdetailform">
                @if(isset($comparables['final_comparables']) && count($comparables['final_comparables']) > 0)
                    <h2>Top 5 Comparables:</h2>
                    <div class="box-body no-padding box-shadow colored-box">
                        <table class="table table-striped" id="final_comparables">
                            <thead>
                                <tr>
                                    <th class="nosort comparable_heading" colspan="8">Comparable Details</th>
                                    <!--th class="nosort comparable_heading" colspan="3">Date of Sale</th-->
                                    <th class="nosort comparable_heading" colspan="3">Parcel Size</th>
                                    <th class="nosort comparable_heading" colspan="3">Interior vs Corner</th>
                                    <th class="nosort comparable_heading" colspan="3">Above Grade Square Footage</th>
                                    <th class="nosort comparable_heading" colspan="3">Total Bedrooms</th>
                                    <th class="nosort comparable_heading" colspan="3">Total Bathrooms</th>
                                    <th class="nosort comparable_heading" colspan="3">Finished Space</th>
                                    <th class="nosort comparable_heading" colspan="3">Unfinished Space</th>
                                    <th class="nosort comparable_heading" colspan="3">Garage</th>
                                    <th class="nosort comparable_heading" colspan="3">Carport</th>
                                    <th class="nosort comparable_heading" colspan="3">Swimming Pool</th>
                                    <th class="nosort comparable_heading" colspan="3">Fireplace</th>
                                </tr>
                                <tr>
                                    <th class="nosort comparable_heading">Comparable Type of House</th>
                                    <th class="nosort comparable_heading">Comparable Sale Price</th>
                                    <th class="nosort comparable_heading">Comparable Sale Date</th>
                                    <th class="nosort comparable_heading">Comparable Address</th>
                                    <th class="nosort comparable_heading">Comparable Distance from Subject Property</th>
                                    <th class="nosort comparable_heading">Comparable Sale Price After Adjustment Value</th>
                                    <th class="nosort comparable_heading">
                                        Living area variance percent +/- 20% range
                                        @if(count($subject_property) > 0)
                                            {{$subject_property['living_area_minus_twenty'] }} - {{$subject_property['living_area_plus_twenty'] }}
                                        @endif
                                    </th>
                                    <th class="nosort comparable_heading">Adjusted Sale Price/SF</th>
                                    <!--th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th-->
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($comparables['final_comparables']) > 0)
                                    @foreach($comparables['final_comparables'] as $final_comparable)
                                    @if($final_comparable['living_area_plus_minus_twenty'] == 1)
                                    <tr style="background-color: skyblue;">
                                    @else
                                    <tr>
                                    @endif
                                        <td>{{ $final_comparable['comparable_type_of_house'] }}</td>
                                        <td>{{ $final_comparable['comparable_sale_price'] }}</td>
                                        <td>{{ $final_comparable['comparable_date_of_sale'] }}</td>
                                        <td><b>Street</b>{{ $final_comparable['comparable_address_street'] }} <br> <b>City</b>{{ $final_comparable['comparable_address_city'] }} <br> <b>State</b>{{$final_comparable['comparable_address_state'] }} <br> <b>County</b>{{ $final_comparable['comparable_address_county'] }} <br> <b>Zip Code</b>{{$final_comparable['comparable_address_zipcode'] }}</td>
                                        <td>{{ $final_comparable['distance_from_subject'] }}</td>
                                        <td>{{ $final_comparable['price_after_adjustment'] }}</td>
                                        <td>{{ $final_comparable['comparable_square_footage'] }}</td>
                                        <td>{{ $final_comparable['sale_price_divided_square_footage'] }}</td>
                                        <?php /*<td>{{ $final_comparable['comparable_date_of_sale'] }}</td>
                                        <td>{{ $final_comparable['formula_date_of_sale'] }}</td>
                                        <td>{{ $final_comparable['date_of_sale'] }}</td> */ ?>
                                        <td>{{ $final_comparable['comparable_parcel_size'] }}</td>
                                        <td>{{ $final_comparable['formula_parcel_size'] }}</td>
                                        <td>{{ $final_comparable['parcel_size'] }}</td>
                                        <td>-</td> <td>-</td> <td>-</td>
                                        <td>{{ $final_comparable['comparable_square_footage'] }}</td>
                                        <td>{{ $final_comparable['formula_square_footage'] }}</td>
                                        <td>{{ $final_comparable['square_footage'] }}</td>
                                        <td>{{ $final_comparable['comparable_total_bedrooms'] }}</td>
                                        <td>{{ $final_comparable['formula_total_bedrooms'] }}</td>
                                        <td>{{ $final_comparable['total_bedrooms'] }}</td>
                                        <td>{{ $final_comparable['comparable_total_bathrooms'] }}</td>
                                        <td>{{ $final_comparable['formula_total_bathrooms'] }}</td>
                                        <td>{{ $final_comparable['total_bathrooms'] }}</td>                                        
                                        <td>{{ $final_comparable['comparable_finished_space'] }}</td>
                                        <td>{{ $final_comparable['formula_finished_space'] }}</td>
                                        <td>{{ $final_comparable['finished_space'] }}</td>
                                        <td>{{ $final_comparable['comparable_unfinished_space'] }}</td>
                                        <td>{{ $final_comparable['formula_unfinished_space'] }}</td>
                                        <td>{{ $final_comparable['unfinished_space'] }}</td>
                                        <td>{{ $final_comparable['garage'] }}</td>
                                        <td>{{ $final_comparable['formula_garage'] }}</td>
                                        @if($final_comparable['garage'] != "-")
                                            <td>{{ $final_comparable['garage_count'] }}</td>
                                        @else
                                            <td>0</td>
                                        @endif
                                        <td>{{ $final_comparable['comparable_carport_exist'] }}</td>
                                        <td>{{ $final_comparable['formula_carport'] }}</td>
                                        <td>{{ $final_comparable['carport'] }}</td>
                                        <td>{{ $final_comparable['comparable_pool_exist'] }}</td>
                                        <td>{{ $final_comparable['formula_pool'] }}</td>
                                        <td>{{ $final_comparable['pool'] }}</td>
                                        <td>{{ $final_comparable['comparable_fireplace_count'] }}</td>
                                        <td>{{ $final_comparable['formula_fireplace'] }}</td>
                                        <td>{{ $final_comparable['fireplace'] }}</td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>                    
                    </div>
                @endif
            </div>

            <div class="contactdetailform">
                <h2>All Comparables:</h2>
                @if(isset($comparables['all_comparables']) && count($comparables['all_comparables']) > 0)
                    <div class="box-body no-padding box-shadow colored-box">
                        <table class="table table-striped" id="all_comparables">
                            <thead>
                                <tr>
                                    <th class="nosort comparable_heading" colspan="8">Comparable Details</th>
                                    <!--th class="nosort comparable_heading" colspan="3">Date of Sale</th-->
                                    <th class="nosort comparable_heading" colspan="3">Parcel Size</th>
                                    <th class="nosort comparable_heading" colspan="3">Interior vs Corner</th>
                                    <th class="nosort comparable_heading" colspan="3">Above Grade Square Footage</th>
                                    <th class="nosort comparable_heading" colspan="3">Total Bedrooms</th>
                                    <th class="nosort comparable_heading" colspan="3">Total Bathrooms</th>
                                    <th class="nosort comparable_heading" colspan="3">Finished Space</th>
                                    <th class="nosort comparable_heading" colspan="3">Unfinished Space</th>
                                    <th class="nosort comparable_heading" colspan="3">Garage</th>
                                    <th class="nosort comparable_heading" colspan="3">Carport</th>
                                    <th class="nosort comparable_heading" colspan="3">Swimming Pool</th>
                                    <th class="nosort comparable_heading" colspan="3">Fireplace</th>
                                </tr>
                                <tr>
                                    <th class="nosort comparable_heading">Comparable Type of House</th>
                                    <th class="nosort comparable_heading">Comparable Sale Price</th>
                                    <th class="nosort comparable_heading">Comparable Sale Date</th>
                                    <th class="nosort comparable_heading">Comparable Address</th>
                                    <th class="nosort comparable_heading">Comparable Distance from Subject Property</th>
                                    <th class="nosort comparable_heading">Comparable Sale Price After Adjustment Valuee</th>
                                    <th class="nosort comparable_heading">
                                        Living area variance percent +/- 20% range
                                        @if(count($subject_property) > 0)
                                            {{$subject_property['living_area_minus_twenty'] }} - {{$subject_property['living_area_plus_twenty'] }}
                                        @endif
                                    </th>
                                    <th class="nosort comparable_heading">Adjusted Sale Price/SF</th>
                                    <!--th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th-->
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                    <th class="nosort comparable_heading">Comparable Value</th>
                                    <th class="nosort comparable_heading">Adjustment Formula</th>
                                    <th class="nosort comparable_heading">Price After Adjustment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($comparables['all_comparables']) > 0)
                                    @foreach($comparables['all_comparables'] as $comparable)
                                    <tr>
                                        <td>{{ $comparable['comparable_type_of_house'] }}</td>
                                        <td>{{ $comparable['comparable_sale_price'] }}</td>
                                        <td>{{ $comparable['comparable_date_of_sale'] }}</td>
                                        <td><b>Street</b>{{ $comparable['comparable_address_street'] }} <br> <b>City</b>{{ $comparable['comparable_address_city'] }} <br> <b>State</b>{{$comparable['comparable_address_state'] }} <br> <b>County</b>{{ $comparable['comparable_address_county'] }} <br> <b>Zip Code</b>{{$comparable['comparable_address_zipcode'] }}</td>
                                        <td>{{ $comparable['distance_from_subject'] }}</td>
                                        <td>{{ $comparable['price_after_adjustment'] }}</td>
                                        <td>{{ $comparable['comparable_square_footage'] }}</td>
                                        <td>{{ $comparable['sale_price_divided_square_footage'] }}</td>
                                        <?php /*<td>{{ $comparable['comparable_date_of_sale'] }}</td>
                                        <td>{{ $comparable['formula_date_of_sale'] }}</td>
                                        <td>{{ $comparable['date_of_sale'] }}</td> */ ?>
                                        <td>{{ $comparable['comparable_parcel_size'] }}</td>
                                        <td>{{ $comparable['formula_parcel_size'] }}</td>
                                        <td>{{ $comparable['parcel_size'] }}</td>
                                        <td>-</td> <td>-</td> <td>-</td>
                                        <td>{{ $comparable['comparable_square_footage'] }}</td>
                                        <td>{{ $comparable['formula_square_footage'] }}</td>
                                        <td>{{ $comparable['square_footage'] }}</td>
                                        <td>{{ $comparable['comparable_total_bedrooms'] }}</td>
                                        <td>{{ $comparable['formula_total_bedrooms'] }}</td>
                                        <td>{{ $comparable['total_bedrooms'] }}</td>
                                        <td>{{ $comparable['comparable_total_bathrooms'] }}</td>
                                        <td>{{ $comparable['formula_total_bathrooms'] }}</td>
                                        <td>{{ $comparable['total_bathrooms'] }}</td>                                        
                                        <td>{{ $comparable['comparable_finished_space'] }}</td>
                                        <td>{{ $comparable['formula_finished_space'] }}</td>
                                        <td>{{ $comparable['finished_space'] }}</td>
                                        <td>{{ $comparable['comparable_unfinished_space'] }}</td>
                                        <td>{{ $comparable['formula_unfinished_space'] }}</td>
                                        <td>{{ $comparable['unfinished_space'] }}</td>
                                        <td>{{ $comparable['garage'] }}</td>
                                        <td>{{ $comparable['formula_garage'] }}</td>
                                        @if($comparable['garage'] != "-")
                                            <td>{{ $comparable['comparable_garage_count'] }}</td>
                                        @else
                                            <td>0</td>
                                        @endif
                                        <td>{{ $comparable['comparable_carport_exist'] }}</td>
                                        <td>{{ $comparable['formula_carport'] }}</td>
                                        <td>{{ $comparable['carport'] }}</td>
                                        <td>{{ $comparable['comparable_pool_exist'] }}</td>
                                        <td>{{ $comparable['formula_pool'] }}</td>
                                        <td>{{ $comparable['pool'] }}</td>
                                        <td>{{ $comparable['comparable_fireplace_count'] }}</td>
                                        <td>{{ $comparable['formula_fireplace'] }}</td>
                                        <td>{{ $comparable['fireplace'] }}</td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>                    
                    </div>
                	
				@endif
            </div>
            @endif
        </div>
		<div>
        <a href="{{ url('/download-all-adjusted-sales') }}" class="btn btn-ctrl">Download all comparables</a>
		</div
     
    </div><!-- tsg-latest -->
    
</div><!-- tsg-inner-wrapper -->



@endsection

@section('js')
<!-- Data Table JS -->
<script src="{{ asset('/project/resources/assets/customer/js/jqueryDataTable.js') }}"></script> 
<script type="text/javascript">
$(document).ready(function() {
    
    var subject_property_table = $('#subject_property').DataTable({
            paging: true,
            "scrollX": true,
            sorting: false,
            //columnDefs: [{orderable: false, targets: [4]}],
            //columnDefs: [ { orderable: false, targets: [0] } ],
    });   //pay attention to capital D, which is mandatory to retrieve "api" datatables' object, as @Lionel said
    
    //subject_property_table.columns.adjust().draw();
    
    
    var final_comparables_table = $('#final_comparables').DataTable({
            paging: true,
            "scrollX": true,
            sorting: false,
            //columnDefs: [{orderable: false, targets: [4]}],
            //columnDefs: [ { orderable: false, targets: [0] } ],
    });   //pay attention to capital D, which is mandatory to retrieve "api" datatables' object, as @Lionel said
    
    final_comparables_table.columns.adjust().draw();
    
    
    var all_comparables_table = $('#all_comparables').DataTable({
            paging: true,
            "scrollX": true,
            sorting: false,
            //columnDefs: [{orderable: false, targets: [4]}],
            //columnDefs: [ { orderable: false, targets: [0] } ],
    });   //pay attention to capital D, which is mandatory to retrieve "api" datatables' object, as @Lionel said
    
    //all_comparables_table.columns.adjust().draw();
    
});		
</script>

@endsection
