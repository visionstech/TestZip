<table cellpadding="0" cellspacing="0" style="width:100%; margin:0 auto;font-weight: normal;font-family: Arial, sans-serif;color:#000000;border:0;line-height: 1.2;font-size: 11pt;max-width: 650px;">
  <tbody>
    <tr>
    <?php //echo "<pre>eeeee";print_r($topComparableData);exit;?>
      <td>
        <table cellspacing="0" cellpadding="0" style="width: 100%;font-size: 12pt;">
        <tr>
          <td style="font-weight: bold;text-align: center;font-size: 10pt;padding-bottom: 10px;">
            DO NOT SUBMIT THIS PAGE – THIS IS JUST FOR YOUR RECORDS
          </td>
        </tr>
          <tr>
            <td style="text-align: center;padding-top: 10px;">
             <img src="{{ public_path('/project/resources/assets/customer/css/images/logoblue.jpg') }}">
            </td>
          </tr>
          <!--tr>
            <td style="font-size: 30pt;font-weight: bold;text-align: center;padding-top: 70px;">
              ASSESSMENT SOLUTIONS
            </td>
          </tr-->
          <tr>
            <td style="height: 45px;">
              
            </td>
          </tr>
          <tr>
            <td style="font-size: 14pt;font-weight: bold;">
              Fairfax County, VA
            </td>
          </tr>
          <tr>
            <td style="font-size: 14pt;font-weight: bold;">
             Appeal Checklist
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td style="height: 30px;">
        
      </td>
    </tr>
    <tr>
      <td>
        <table cellspacing="0" cellpadding="0" style="width: 100%;font-size: 12pt;">
          <tr>
            <td style="width: 5%;padding-bottom: 14px;">
              
            </td>
            <td style="width: 5%;padding-bottom: 14px;">
              <b style="width: 14px;height: 14px;border:1px solid #000;display: inline-block;background-color: #ccc;"></b>
            </td>
            <td style="padding-bottom: 14px;">
              Sign the appeal form
            </td>
          </tr>
          <tr>
            <td style="width: 5%;padding-bottom: 14px;">
              
            </td>
            <td style="width: 5%;padding-bottom: 14px;">
              <b style="width: 14px;height: 14px;border:1px solid #000;display: inline-block;background-color: #ccc;"></b>
            </td>
            <td style="padding-bottom: 14px;">
             Include the “Sale Comparable Report” with appeal form in envelope
            </td>
          </tr>
          <tr>
            <td style="width: 5%;padding-bottom: 14px;">
              
            </td>
            <td style="width: 5%;padding-bottom: 14px;">
              <b style="width: 14px;height: 14px;border:1px solid #000;display: inline-block;background-color: #ccc;"></b>
            </td>
            <td style="padding-bottom: 14px;">
              Address envelope to:
            </td>
          </tr>
          <tr>
            <td style="width: 5%;padding-bottom: 14px;">
              
            </td>
            <td style="width: 5%;padding-bottom: 14px;">
              
            </td>
            <td style="padding-bottom: 14px;">
              <table cellspacing="0" cellpadding="0" style="width: 100%;font-size: 12pt;font-weight: bold;">
                <tr>
                  <td>
					Department Of Tax Administration (DTA)
                  </td>
                </tr>
                <tr>
                  <td>
                   Fairfax County Real Estate Division
                  </td>
                </tr>
                <tr>
                  <td>
                   12000 Government Center Parkway, Suite 357
                  </td>
                </tr>
                <tr>
                  <td>
                   Fairfax, Virginia 22035
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="width: 5%;padding-bottom: 14px;">
              
            </td>
            <td style="width: 5%;padding-bottom: 14px;">
              <b style="width: 14px;height: 14px;border:1px solid #000;display: inline-block;background-color: #ccc;"></b>
            </td>
            <td style="padding-bottom: 14px;">
              Keep a copy of all documents for your files
            </td>
          </tr>
          <tr>
            <td style="width: 5%;padding-bottom: 14px;">
              
            </td>
            <td style="width: 5%;padding-bottom: 14px;">
              <b style="width: 14px;height: 14px;border:1px solid #000;display: inline-block;background-color: #ccc;"></b>
            </td>
            <td style="padding-bottom: 14px;">
              Be on the lookout for correspondence from Fairfax County, VA regarding your appeal.
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td style="height: 40px;">
        
      </td>
    </tr>
    <tr>
      <td style="font-weight: bold;font-size: 20pt;padding-bottom: 70px;">
        Deadline to postmark the appeal is {{ $appeal_deadline_date }}
      </td>
    </tr>
    <tr>
    <!-- iF ONLY ASSESMENT THEN 320 PX -->
    <!-- iF bOTH LOGO AND ASSESMENT THEN 250 PX -->
    <!-- iF ONLY LOGO THEN 320 PX -->
      <td style="height: 320px;">
        
      </td>
    </tr>
    <tr>
          <td style="font-weight: bold;text-align: center;font-size: 10pt;">
            DO NOT SUBMIT THIS PAGE – THIS IS JUST FOR YOUR RECORDS
          </td>
        </tr>
		<tr>
			<td>
				<table style="width: 100%;" cellspacing="0">
					<tr> 
						<td style="width: 20%;"><img style="width: 100px;" src="{{ public_path('/project/resources/assets/customer/images/virgina-logo.png') }}"></td>
						<td style="width: 80%;text-align: center;font-size: 20pt;line-height: 1;vertical-align: top;font-weight: bold;">{{ $appeal_year }} REAL ESTATE ASSESSMENT APPEAL <br>APPLICATION</td>
					</tr>
				</table>
			</td>
		</tr>
		<?php
		    $parcelIdentyfire='';
		    if($topComparableData['subject']->corelogic_response != ''){
		        $corelogic_response=json_decode($topComparableData['subject']->corelogic_response,true);
		        $parcelIdentyfire=$corelogic_response['PROPERTY']['@attributes']['_AssessorsParcelIdentifier'];
		    }
		?>
		<tr>
			<td>
				<table style="width: 100%;" cellspacing="0" cellpadding="0">
					<tr>
						<td style="width:50%;line-height: 1.2;"><b>Department Of Tax Administration (DTA)<br>Fairfax County Real Estate Division</b><br> 12000 Government Center Parkway, Suite 357<br> Fairfax, Virginia 22035</td>
						<td style="width:50%; vertical-align: top;">
							<table style="width:100%;border: 1px solid #000;" cellspacing="0">
								<tr>
									<td style="background-color: #000;color: #fff; text-align: center;height: 25px;">Tax Map Reference Number</td>
								</tr>
								<tr>
									<td style="vertical-align: bottom;padding-top: 10px;">
									&nbsp; {{ $parcelIdentyfire }}
									</td>
								</tr>
								<tr>
									<td style="vertical-align: top;padding-bottom: 10px;">&nbsp; __ __ __ __ &nbsp; __ __ __ __ __ __ __ __ __ __ __ __ </td>
								</tr>
								<tr>
									<td style="height: 2px;"></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td style="width:50%;line-height: 1.2;">
						<b>Internet:</b> <a style="color: #000;text-decoration: underline;" href="">www.fairfaxcounty.gov/dta</a><br>
						<b>Telephone:</b> 703-222-8234; TTY 711<br>
						<b>E-mail:</b> http://iCare.fairfaxcounty.gov/ContactUs/
						</td>
						<td style="width:50%; vertical-align: top;">
							<table style="width:100%;border: 1px solid #000;line-height: 1;" cellspacing="0" cellpadding="0">
								<tr>
									<td style="height: 25px;"> &nbsp; <b>DTA USE ONLY</b> NBHD #: &nbsp; <b style="display: inline-block;width:43%;border-bottom: 1px solid #000;height: 20px;"></b></td>
								</tr>
								<tr>
									<td style="height: 25px;"> &nbsp; Appeal Number: &nbsp; <b style="display: inline-block;width:63%;border-bottom: 1px solid #000;height: 20px;"></b></td>
								</tr>
								<tr>
									<td style="height: 25px;"> &nbsp; Assigned to Appr:  <b style="display: inline-block;width:12%;border-bottom: 1px solid #000;height: 20px;"></b> Date Due: <b style="display: inline-block;width:7%;border-bottom: 1px solid #000;height: 20px;"></b> /<b style="display: inline-block;width:7%;border-bottom: 1px solid #000;height: 20px;"></b> /<b style="display: inline-block;width:10%;border-bottom: 1px solid #000;height: 20px;"></b></td>
								</tr>
								<tr>
									<td style="height: 2px;"></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td></td></tr>
		<tr>
			<td style="vertical-align: top;font-weight: bold;font-size: 14pt;font-style: italic;"><b style="display: inline-block;width:24%;border-bottom: 4px dotted #000;height: 8px;"></b>&nbsp; APPEAL DEADLINE IS {{ $appeal_deadline_date }} &nbsp;<b style="display: inline-block;width:24%;border-bottom: 4px dotted #000;height: 8px;"></b></td>
		</tr>
		<tr>

			<td style="font-style: italic;font-weight: bold;text-align: center;">Appeals received after the {{ date('F d',strtotime($appeal_deadline_date)) }} deadline <u> will not be accepted</u>.</td>
		</tr>
		<tr>
			<td style="height: 2px;"></td>
		</tr>
		<tr>
			<td style="border:2px solid #000;">
				<table style="border:1px solid #000;width: 100%;" cellspacing="0">
					 <tr>
					 	<td colspan="2" style="width: 100%;border-bottom: 1px solid #000;">&nbsp;Address of Property Being Appealed: {{ $search_address }}</td>
					 </tr>
					 <tr>
					 	<td style="width: 70%;border-right: 1px solid #000;border-bottom: 1px solid #000;"> &nbsp;Property Location (City): {{ $city }}</td>
					 	<td style="width: 30%;border-bottom: 1px solid #000;"> &nbsp;Property Zip Code: {{ $zip_code }}</td>
					 </tr>
					 <tr>
					 	<td colspan="2" style="width: 100%;border-bottom: 1px solid #000;"> &nbsp;Building Name (if any):</td>
					 </tr>
					 <tr>
					 	<td colspan="2" style="width: 100%;border-bottom: 1px solid #000;"> &nbsp;Name of owner (s) on {{ $appeal_deadline_date }}: {{ ucwords($owner_name) }}</td>
					 </tr>
					 <tr>
					 	<td colspan="2">
					 		<table style="width: 100%;" cellpadding="0" cellspacing="0">
					 			<tr>
					 				<td style="width: 30%;"> &nbsp;{{ date('Y') }} Assessment Notice Values:</td>
								 	<td style="width: 20%;">Land: {{ ($land_assessment_value != "" && $land_assessment_value != null && $land_assessment_value != '$0.00') ?( $land_assessment_value) : " --" }}</td>
								 	<td style="width: 25%;">Building: {{ ($improvement_assessment_value != "" && $improvement_assessment_value != null && $improvement_assessment_value != '$0.00') ?( $improvement_assessment_value) : " --" }}</td>
								 	<td style="width: 25%;">Total: {{ ($total_assessment_value != "" && $total_assessment_value != null && $total_assessment_value != '$0.00') ?( $total_assessment_value) : " --" }}</td>
					 			</tr>
					 		</table>
					 	</td>
					 </tr>
				</table>
			</td>
		</tr>
		<tr>
			<td style="width: 100%;font-weight: bold;height: 60px;">Under state law, financial impact and/or the rate of value change <u>is not</u> sufficient grounds for appeal. As required, the county's assessment is an estimate of fair market value as of {{ $date_of_value }}. Appeals should be based on at least one of the three categories noted below. Check one or more for your appeal basis.</td>
		</tr>
		<tr>
			<td style="border:2px solid #000;">
				<table style="border:1px solid #000;width: 100%;" cellspacing="0">
					 <tr>
					 	<td style="width: 4%;border-right: 1px solid #000;border-bottom: 1px solid #000;"> &nbsp;<input type="checkbox" checked="checked"></td>
					 	<td style="width: 1%;border-bottom: 1px solid #000;"></td>
					 	<td colspan="2" style="width: 95%;border-bottom: 1px solid #000;"><b>FAIR MARKET VALUE:</b> <span style="font-size: 10pt;">This property is assessed greater or less than its Fair Market Value as indicated by a review of comparable properties (see reverse side of form).</span></td>
					 </tr>
					 <tr>
					 	<td style="width: 4%;border-right: 1px solid #000;border-bottom: 1px solid #000;"> &nbsp;<input type="checkbox" checked="checked" ></td>
					 	<td style="width: 1%;border-bottom: 1px solid #000;"></td>
					 	<td colspan="2" style="width: 95%;border-bottom: 1px solid #000;"><b>LACK OF UNIFORMITY:</b> <span style="font-size: 10pt;">This property assessment is out of line generally with similar properties (see reverse side of form).</span></td>
					 </tr>
					 <tr>
					 	<td style="width: 4%;border-right: 1px solid #000;"> &nbsp;<input type="checkbox" ></td>
					 	<td style="width: 1%;"></td>
					 	<td colspan="2" style="width: 95%;height: 65px;"><b>ERRORS IN PROPERTY DESCRIPTION:</b> <span style="font-size: 10pt;">
					 	Assessment is based upon inaccurate information concerning this property such as lot size, square footage, condition of property, flood plain, topography, zoning, etc. (List accurate property characteristic details on the reverse side of this form.
					  </span></td>
					 </tr>
					 
				</table>
			</td>
		</tr>
		<tr>
			<td style="font-weight: bold;font-size: 12.5pt;height: 30px;">OWNER/APPLICANT INFORMATION (must be completed by all owners or applicants):</td>
		</tr>
		<tr>
			<td style="border:2px solid #000;border-bottom: 0;">
				<table style="border:1px solid #000;width: 100%;" cellspacing="0">
					 <tr>
					 	<td style="width: 100%;height: 28px;font-style: italic;">&nbsp;Based on this appeal information, I believe the proper assessment of this property as of {{ $appeal_deadline_date }} should be:</td>
					 </tr>
				</table>
			</td>
		</tr>
		<tr>
			<td style="border:2px solid #000;border-bottom: 0;border-top: 0;">
				<table style="border:0px solid #000;width: 100%;" cellspacing="0">
					 <tr>
					 	<td style="height: 28px;width: 33%;border:1px solid #000;">&nbsp;Land: --</td>
					 	<td style="height: 28px;width: 0.1%;"></td>
					 	<td style="height: 28px;width: 33%;border:1px solid #000;">&nbsp;Building: --</td>
					 	<td style="height: 28px;width: 0.1%;"></td>
					 	<td style="height: 28px;width: 33%;border:1px solid #000;">&nbsp;Total: ${{ money_format('%!n',$topComparableData['subject']->subjectOtherData->appeal_amount) }}</td>
					 </tr>
				</table>
			</td>
		</tr>
		<tr>
			<td style="border:2px solid #000;border-top: 0;">
				<table style="border:1px solid #000;width: 100%;" cellspacing="0">
					 <tr>
					 	<td style="width: 0.5%;"></td>
					 	<td style="width: 55%;font-weight: bold;">I hereby certify that the facts contained herein and attached hereto are true, accurate and correct to the best of my knowledge and belief.</td>
					 	<td style="width: 5%;"></td>
					 	<td rowspan="4" style="width: 34.5%;vertical-align: top;">
					 		<table style="width: 100%;" cellspacing="0"><tr><td style="height: 5px;"></td></tr></table>
					 		<table style="width: 100%;border: 1px solid #000;" cellpadding="5" cellspacing="0">
					 			<tr>
					 				<td style="text-align: center;font-size: 9pt;line-height: 1.1;vertical-align: top;">If applicant is not the owner of record, application must include a <b><u>Letter of Authorization</u></b> from the owner, signed prior to date of application, either notarized or on owner's commercial letterhead. Two most recent annual income/expense surveys along with current rent roll <u>must be submitted with appeals on income producing properties.</u></td>
					 			</tr>
					 		</table>
					 	</td>
					 	<td style="width: 5%;"></td>
					 </tr>
					 <tr>
					 	<td style="width: 0.5%;"></td>
					 	<td style="width: 55%;" style="font-size: 10pt;">
					 		<table style="width: 100%;" cellspacing="0">
					 			<tr>
					 				<td style="width:42%;">Given under my hand this</td>
					 				<td style="width:11%;"><b style="display: inline-block;width:100%;border-bottom: 1px solid #000;height: 20px;"></b></td>
					 				<td style="width: 11%;">day of</td>
					 				<td style="width: 20%;"><b style="display: inline-block;width:100%;border-bottom: 1px solid #000;height: 20px;"></b></td>
					 				<td style="width: 8%;">, 20</td>
					 				<td style="width: 8%;"><b style="display: inline-block;width:100%;border-bottom: 1px solid #000;height: 20px;"></b></td>
					 			</tr>
					 		</table>
					 	</td>
					 	<td style="width: 5%;"></td>
					 	<td style="width: 5%;"></td>
					 </tr>
					 <tr>
					 	<td style="width: 0.5%;"></td>
					 	<td style="width: 55%;" style="font-size: 10pt;">
					 		<table style="width: 100%;" cellspacing="0">
					 			<tr>
					 				<td style="width:50%;">Signature of Applicant/Owner:</td>
					 				<td colspan="4" style="width:50%;"><b style="display: inline-block;width:100%;border-bottom: 1px solid #000;height: 20px;"></b></td>
					 			</tr>
					 		</table>
					 	</td>
					 	<td style="width: 5%;"></td>
					 	<td style="width: 5%;"></td>
					 </tr>
					 <tr>
					 	<td style="width: 0.5%;"></td>
					 	<td style="width: 55%;" style="font-size: 10pt;">
					 		<table style="width: 100%;" cellspacing="0">
					 			<tr>
					 				<td style="width:52%;">Print name of Applicant/Owner :</td>
					 				<td colspan="4" style="width:48%;"><b style="display: inline-block;width:100%;border-bottom: 1px solid #000;height: 20px;"></b></td>
					 			</tr>
					 		</table>
					 	</td>
					 	<td style="width: 5%;"></td>
					 	<td style="width: 5%;"></td>
					 </tr>
					 <tr>
					 	<td style="width: 0.5%;"></td>
					 	<td colspan="3" style="font-size: 10pt;">
					 		<table style="width: 100%;" cellspacing="0">
					 			<tr>
					 				<td style="width:17.5%">Phone: Day ( &nbsp; &nbsp; &nbsp; &nbsp; )</td>
					 				<td style="width:20%"><b style="display: inline-block;width:100%;border-bottom: 1px solid #000;height: 20px;"></b></td>
					 				<td style="width: 12%">Other ( &nbsp; &nbsp; &nbsp; &nbsp; )</td>
					 				<td style="width: 16%"><b style="display: inline-block;width:98%;border-bottom: 1px solid #000;height: 20px;"></b></td>
					 				<td style="width: 7%">E-Mail</td>
					 				<td style="width: 29.5%"><b style="display: inline-block;width:100%;border-bottom: 1px solid #000;height: 20px;"></b></td>
					 			</tr>
					 		</table>
					 	</td>
					 	<td style="width: 5%;"></td>
					 </tr>
					 <tr>
					 	<td style="width: 0.5%;"></td>
					 	<td colspan="3" style="font-size: 10pt;">
					 		<table style="width: 100%;" cellspacing="0">
					 			<tr>
					 				<td style="width:59%">Applicant/Owner Mailing Address (if different from property address):</td>
					 				<td style="width:41%"><b style="display: inline-block;width:100%;border-bottom: 1px solid #000;height: 20px;"></b></td>
					 			</tr>
					 		</table>
					 	</td>
					 	<td style="width: 5%;"></td>
					 </tr>
					 <tr>
					 	<td style="width: 0.5%;"></td>
					 	<td colspan="3" style="border-bottom: 1px solid #000;height: 15px;">
					 	</td>
					 	<td style="width: 5%;"></td>
					 </tr>
					 <tr>
					 	<td style="width: 0.5%;"></td>
					 	<td colspan="3" style="line-height: 1;font-size: 10pt;height:20px;">
					 		<table style="width: 100%;" cellspacing="0" cellpadding="0">
					 			<tr>
					 				<td style="width:20%;height:20px;font-weight:bold;"> &nbsp; <span style="font-size: 12pt;">C</span>HECK <span style="font-size: 12pt;">O</span>NE:</td>
					 				<td style="width:4%;height:20px;"><input type="checkbox"></td>
					 				<td style="width:37%;height:20px;">I AM THE OWNER OF RECORD</td>
					 				<td style="width:4%;height:20px;"><input type="checkbox"></td>
					 				<td style="width:35%;height:20px;">I AM NOT THE OWNER OF RECORD</td>
					 			</tr>
					 		</table>
					 	</td>
					 	<td style="width: 5%;"></td>
					 </tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table style="width: 100%;" cellspacing="0">
					<tr>
						<td colspan="2" style="height: 2px;"></td>
					</tr>
					<tr>
						<td style="width: 15%;height: 25px;"></td>
						<td style="width: 55%;font-size: 12.8pt;font-weight: bold;text-align: center;height: 25px;">CONTINUED ON REVERSE SIDE </td>
						<td style="width: 20%;font-size: 9pt;height: 25px;">Revised {{ $date_of_value }}</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table style="width: 100%;border:1px solid #000;" cellspacing="0">
					<tr>
						<td colspan="2" style="background-color: #000000;color: #ffffff;font-weight: bold;">&nbsp; Physical Characteristics of Property Being Appealed <i>(please verify all: "n/a" if not applicable):</i></td>
					</tr>
					<tr>
						<td style="font-size: 10pt;width: 50%;border-right:1px solid #000;border-bottom:1px solid #000;">&nbsp; Year Built: {{ $year_built }}</td>
						<td style="font-size: 10pt;width: 50%;border-bottom:1px solid #000;">&nbsp; Total number of fireplaces (incl bsmt): {{ $total_fireplaces }}</td>
					</tr>
					<tr>
						<td style="font-size: 10pt;width: 50%;border-right:1px solid #000;border-bottom:1px solid #000;">&nbsp; Year house remodeled & cost:  
						{{ ($lookupdetail['needs_new_roof'] != "" && $lookupdetail['needs_new_roof'] != null && $lookupdetail['needs_new_roof'] != '0.00') ?('$'.$lookupdetail['needs_new_roof']) : " --" }}</td>
						<td style="font-size: 10pt;width: 50%;border-bottom:1px solid #000;">&nbsp; Central air conditioning (yes or no): {{ $air_conditioning }}</td>
					</tr>
					<tr>
						<td style="font-size: 10pt;width: 50%;border-right:1px solid #000;border-bottom:1px solid #000;">&nbsp; Year kitchen remodeled & cost: 
						{{ ($lookupdetail['kitchen'] != "" && $lookupdetail['kitchen'] != null && $lookupdetail['kitchen'] != '0.00') ?('$'.$lookupdetail['kitchen']) : " --" }}</td>
						<td style="font-size: 10pt;width: 50%;border-bottom:1px solid #000;">&nbsp; Number of bedrooms in basement:</td>
					</tr>
					<tr>
						<td style="font-size: 10pt;width: 50%;border-right:1px solid #000;border-bottom:1px solid #000;">&nbsp; Year bath/baths remodeled & cost:
						{{ ($lookupdetail['bathrooms'] != "" && $lookupdetail['bathrooms'] != null && $lookupdetail['bathrooms'] != '0.00') ?('$'.$lookupdetail['bathrooms']) : " --" }}
						</td>
						<td style="font-size: 10pt;width: 50%;border-bottom:1px solid #000;">&nbsp; Number of dens in basement:</td>
					</tr>
					<tr>
						<td style="font-size: 10pt;width: 50%;border-right:1px solid #000;border-bottom:1px solid #000;">&nbsp; Total number of rooms - condos only (incl bsmt): {{ $bathrooms + $bedrooms }}</td>
						<td style="font-size: 10pt;width: 50%;border-bottom:1px solid #000;">&nbsp; Size of basement rec room (square feet): {{ $square_footage }}</td>
					</tr>
					<tr>
						<td style="font-size: 10pt;width: 50%;border-right:1px solid #000;border-bottom:1px solid #000;">&nbsp; Total number of bedrooms: {{ $total_bedrooms }}</td>
						<td style="font-size: 10pt;width: 50%;border-bottom:1px solid #000;">&nbsp; Second kitchen (yes or no):</td>
					</tr>
					<tr>
						<td style="font-size: 10pt;width: 50%;border-right:1px solid #000;border-bottom:1px solid #000;">&nbsp; Total number of full bathrooms w/tub or shower (incl bsmt): {{ $bathrooms }}</td>
						<td style="font-size: 10pt;width: 50%;border-bottom:1px solid #000;">&nbsp; Elevator (yes or no):</td>
					</tr>
					<tr>
						<td style="font-size: 10pt;width: 50%;border-right:1px solid #000;border-bottom:1px solid #000;">&nbsp; Total number of half bathrooms (incl bsmt): {{ $half_bath_count }}</td>
						<td style="font-size: 10pt;width: 50%;border-bottom:1px solid #000;">&nbsp; Utilities (circle): Water &nbsp; &nbsp; Sewer &nbsp; &nbsp; Gas &nbsp; &nbsp; Septic &nbsp; &nbsp; Wel</td>
					</tr>
					<tr>
						<td colspan="2" style="background-color: #000000;color: #ffffff;font-weight: bold;">&nbsp; Sale Information on Property Being Appealed:</i></td>
					</tr>
					<tr>
						<td colspan="2" style="border-bottom:1px solid #000;">&nbsp; Most recent sale date and price:</i> {{ $sale_date }}, {{ $sale_price }}</td>
					</tr>
					<tr>
						<td colspan="2">
							<table style="width: 100%" cellspacing="0">
								<tr>
									<td style="width: 1%;"></td>
									<td style="width: 99%">Has the property under appeal been listed for sale in the last 3 years (yes or no - provide dates and prices):</i></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="height: 10px;border-bottom:1px solid #000;"></td>
					</tr>
					<tr>
						<td colspan="2">
							<table style="width: 100%" cellspacing="0">
								<tr>
									<td style="width: 1%;"></td>
									<td style="width: 99%">Has the property under appeal been professionally appraised in the last 3 years (list appraised value and date; <b>submitting a copy of the appraisal may help expedite the review):</b></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="height: 10px;"></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td style="height: 7px;"></td>
		</tr>
		<tr>
			<td>
				<table style="width: 100%;border:1px solid #000;" cellspacing="0">
					<tr>
						<td style="background-color: #000000;color: #ffffff;font-weight: bold;">&nbsp; Comparable Properties <i>(attach additional pages to submit more comparables or other comments):</i></td>
					</tr>
					<tr>
						<td>
							<table style="width: 100%" cellspacing="0">
								<tr>
									<td style="width: 1%;"></td>
									<td style="width: 99%;">Provide information below relating to properties with characteristics, assessments or sales prices that support your assessment appeal. Sales in {{ (date('Y')-1) }} can be considered for the 1/1/{{ date('Y') }} assessment; sales that occur in {{ date('Y') }} are not applicable until the Jan. 1, {{ (date('Y')+1) }} assessment. Assistance information is noted at the bottom of this page.</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table style="width: 100%;" cellspacing="0">
								<tr>
									<td style="width: 1%;"></td>
									<td style="width: 25%;"></td>
									<td style="width: 24%;text-align: center;"><span style="background-color: #000;color: #ffffff;font-weight: bold;height: 20px;border-top: 2px solid #000000;border-bottom: 2px solid #000000;">Comparable #1</span></td>
									<td style="width: 24%;text-align: center;"><span style="background-color: #000;color: #ffffff;font-weight: bold;border-top: 2px solid #000000;border-bottom: 2px solid #000000;">Comparable #2</span></td>
									<td style="width: 24%;text-align: center;"><span style="background-color: #000;color: #ffffff;font-weight: bold;border-top: 2px solid #000000;border-bottom: 2px solid #000000;">Comparable #3</span></td>
									<td style="width: 2%;"></td>
								</tr>
								<tr>
									<td colspan="6" style="height: 10px;"></td>
								</tr>
								<tr>
									<td style="width: 1%;"></td>
									<td style="width: 25%;">Property Address:</td>
									<td style="width: 24%;text-align: center;"><span style="display: inline-block;width:90%;border-bottom: 1px solid #000;height: 20px;">{{ (isset($comparables_detail['1']['property_address'])) ? $comparables_detail['1']['property_address'] : '' }}</span></td>
									<td style="width: 24%;text-align: center;"><span style="display: inline-block;width:90%;border-bottom: 1px solid #000;height: 20px;">{{ (isset($comparables_detail['2']['property_address'])) ? $comparables_detail['2']['property_address'] : '' }}</span></td>
									<td style="width: 24%;text-align: center;"><span style="display: inline-block;width:90%;border-bottom: 1px solid #000;height: 20px;">{{ (isset($comparables_detail['3']['property_address'])) ? $comparables_detail['3']['property_address'] : '' }}</span></td>
									<td style="width: 2%;"></td>
								</tr>
								<tr>
									<td style="width: 1%;"></td>
									<td style="width: 25%;">Map Reference #:</td>
									<td style="width: 24%;text-align: center;"><b style="display: inline-block;width:90%;border-bottom: 1px solid #000;height: 20px;"></b></td>
									<td style="width: 24%;text-align: center;"><b style="display: inline-block;width:90%;border-bottom: 1px solid #000;height: 20px;"></b></td>
									<td style="width: 24%;text-align: center;"><b style="display: inline-block;width:90%;border-bottom: 1px solid #000;height: 20px;"></b></td>
									<td style="width: 2%;"></td>
								</tr>
								<tr>
									<td style="width: 1%;"></td>
									<td style="width: 25%;">Land Assessed Value:</td>
									<td style="width: 24%;text-align: center;"><span style="display: inline-block;width:90%;border-bottom: 1px solid #000;height: 20px;">{{ (isset($comparables_detail['1']['land_assessment_value'])) ? $comparables_detail['1']['land_assessment_value'] : '' }}</span></td>
									<td style="width: 24%;text-align: center;"><span style="display: inline-block;width:90%;border-bottom: 1px solid #000;height: 20px;">{{ (isset($comparables_detail['2']['land_assessment_value'])) ? $comparables_detail['2']['land_assessment_value'] : '' }}</span></td>
									<td style="width: 24%;text-align: center;"><span style="display: inline-block;width:90%;border-bottom: 1px solid #000;height: 20px;">{{ (isset($comparables_detail['3']['land_assessment_value'])) ? $comparables_detail['3']['land_assessment_value'] : '' }}</span></td>
									<td style="width: 2%;"></td>
								</tr>
								<tr>
									<td style="width: 1%;"></td>
									<td style="width: 25%;">Building Assessed Value:</td>
									<td style="width: 24%;text-align: center;"><b style="display: inline-block;font-weight:normal;width:90%;border-bottom: 1px solid #000;height: 20px;">{{ (isset($comparables_detail['1']['improvement_assesment_value'])) ? $comparables_detail['1']['improvement_assesment_value'] : '' }}</b></td>
									<td style="width: 24%;text-align: center;"><b style="display: inline-block;font-weight:normal; width:90%;border-bottom: 1px solid #000;height: 20px;">{{ (isset($comparables_detail['2']['improvement_assesment_value'])) ? $comparables_detail['2']['improvement_assesment_value'] : '' }}</b></td>
									<td style="width: 24%;text-align: center;"><b style="display: inline-block;font-weight:normal; width:90%;border-bottom: 1px solid #000;height: 20px;">{{ (isset($comparables_detail['3']['improvement_assesment_value'])) ? $comparables_detail['3']['improvement_assesment_value'] : '' }}</b></td>
									<td style="width: 2%;"></td>
								</tr>
								<tr>
									<td style="width: 1%;"></td>
									<td style="width: 25%;">Total Assessed Value:</td>
                                                                        <td style="width: 24%;text-align: center;"><span style="display: inline-block;width:90%;border-bottom: 1px solid #000;height: 20px;">{{ (isset($comparables_detail['1']['total_assessment_value'])) ? $comparables_detail['1']['total_assessment_value'] : '' }}</span></td>
                                                                        <td style="width: 24%;text-align: center;"><span style="display: inline-block;width:90%;border-bottom: 1px solid #000;height: 20px;">{{ (isset($comparables_detail['2']['total_assessment_value'])) ? $comparables_detail['2']['total_assessment_value'] : '' }}</span></td>
                                                                        <td style="width: 24%;text-align: center;"><span style="display: inline-block;width:90%;border-bottom: 1px solid #000;height: 20px;">{{ (isset($comparables_detail['3']['total_assessment_value'])) ? $comparables_detail['3']['total_assessment_value'] : '' }}</span></td>
									<td style="width: 2%;"></td>
								</tr>
								<tr>
									<td style="width: 1%;"></td>
									<td style="width: 25%;">Sale Date:</td>
                                                                        <td style="width: 24%;text-align: center;"><span style="display: inline-block;width:90%;border-bottom: 1px solid #000;height: 20px;">{{ (isset($comparables_detail['1']['sale_date'])) ? $comparables_detail['1']['sale_date'] : '' }}</span></td>
                                                                        <td style="width: 24%;text-align: center;"><span style="display: inline-block;width:90%;border-bottom: 1px solid #000;height: 20px;">{{ (isset($comparables_detail['2']['sale_date'])) ? $comparables_detail['2']['sale_date'] : '' }}</span></td>
                                                                        <td style="width: 24%;text-align: center;"><span style="display: inline-block;width:90%;border-bottom: 1px solid #000;height: 20px;">{{ (isset($comparables_detail['3']['sale_date'])) ? $comparables_detail['3']['sale_date'] : '' }}</span></td>
									<td style="width: 2%;"></td>
								</tr>
								<tr>
									<td style="width: 1%;"></td>
									<td style="width: 25%;">Sale Price:</td>
									<td style="width: 24%;text-align: center;"><span style="display: inline-block;width:90%;border-bottom: 1px solid #000;height: 20px;">{{ (isset($comparables_detail['1']['sale_price'])) ? $comparables_detail['1']['sale_price'] : '' }}</span></td>
                                                                        <td style="width: 24%;text-align: center;"><span style="display: inline-block;width:90%;border-bottom: 1px solid #000;height: 20px;">{{ (isset($comparables_detail['2']['sale_price'])) ? $comparables_detail['2']['sale_price'] : '' }}</span></td>
                                                                        <td style="width: 24%;text-align: center;"><span style="display: inline-block;width:90%;border-bottom: 1px solid #000;height: 20px;">{{ (isset($comparables_detail['3']['sale_price'])) ? $comparables_detail['3']['sale_price'] : '' }}</span></td>
									<td style="width: 2%;"></td>
								</tr>
								<tr>
									<td style="width: 1%;"></td>
									<td style="width: 25%;">Style:</td>
									<td style="width: 24%;text-align: center;"><b style="display: inline-block;width:90%;border-bottom: 1px solid #000;height: 20px;"></b></td>
									<td style="width: 24%;text-align: center;"><b style="display: inline-block;width:90%;border-bottom: 1px solid #000;height: 20px;"></b></td>
									<td style="width: 24%;text-align: center;"><b style="display: inline-block;width:90%;border-bottom: 1px solid #000;height: 20px;"></b></td>
									<td style="width: 2%;"></td>
								</tr>
								<tr>
									<td style="width: 1%;"></td>
									<td style="width: 25%;">Model Name:</td>
									<td style="width: 24%;text-align: center;"><b style="display: inline-block;width:90%;border-bottom: 1px solid #000;height: 20px;"></b></td>
									<td style="width: 24%;text-align: center;"><b style="display: inline-block;width:90%;border-bottom: 1px solid #000;height: 20px;"></b></td>
									<td style="width: 24%;text-align: center;"><b style="display: inline-block;width:90%;border-bottom: 1px solid #000;height: 20px;"></b></td>
									<td style="width: 2%;"></td>
								</tr>
								<tr>
									<td style="width: 1%;"></td>
									<td colspan="3" style="width: 25%;line-height: 1">Comments:<br>(attach additional pages if necessary)</td>
									<td style="width: 2%;"></td>
								</tr>
								<tr>
									<td colspan="5" style="height: 50px;"></td>
								</tr>
							</table>
						</td>
					</tr>
					
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table style="width: 100%;" cellspacing="0">
					<tr>
						<td style="width: 1%;"></td>
						<td style="font-weight: bold;width: 97%;">You will receive a written response to your appeal (whether the assessment is affirmed, or if adjustments are made either up or down). You have a right to examine in our office the property appraisal cards; working papers used to derive the assessment of your property, if any; and, any available information regarding the methodology employed in the calculation of your property’s assessment, to include a list of comparable properties or sales figures considered; the capitalization rate (for commercial properties); and, any other market surveys, formulas, matrices or other factors that may have been considered, if any. [Va. Code &#167; 58.1-3331; subject to restrictions of Va. Code &#167; 58.1-3]</td>
						<td style="width: 2%;"> </td>
					</tr>
					<tr>
						<td colspan="3"></td>
					</tr>
					<tr>
						<td style="width: 1%;"></td>
						<td style="font-weight: bold;font-size: 14pt;width: 97%">How to receive assistance and research comparable properties:</td>
						<td style="width: 2%;"></td>
					</tr> 
					<tr>
						<td style="width: 1%;"></td>
						<td style="width: 97%">
							<table style="width: 100%;" cellpadding="0" cellspacing="0">
								<tr>
									<td style="width: 3%;"><b>1)</b></td>
									<td style="width: 97%;">Go online to <a href="" style="color: #0000ff;">www.fairfaxcounty.gov/dta</a> (click on "Real Estate Tax" and "View My Property").</td>
								</tr>
							</table>
						</td>
						<td style="width: 2%;"> </td>
					</tr>
					<tr>
						<td style="width: 1%;"></td>
						<td style="width: 97%">
							<table style="width: 100%;" cellpadding="0" cellspacing="0">
								<tr>
									<td style="width: 3%;"><b>2)</b></td>
									<td style="width: 97%;">Call the Automated Information System 703-222-6740, TTY 711, Monday-Saturday from 7 a.m. to 7 p.m.</td>
								</tr>
							</table>
						</td>
						<td style="width: 2%;"> </td>
					</tr>
					<tr>
						<td style="width: 1%;"></td>
						<td style="width: 97%">
							<table style="width: 100%;" cellpadding="0" cellspacing="0">
								<tr>
									<td style="width: 3%;"><b>3)</b></td>
									<td style="width: 97%;">Call DTA staff at 703-222-8234, TTY 711 Monday through Friday between 8 a.m. and 4:30 p.m.</td>
								</tr>
							</table>
						</td>
						<td style="width: 2%;"> </td>
					</tr>
					<tr>
						<td style="width: 1%;"></td>
						<td style="width: 97%">
							<table style="width: 100%;" cellpadding="0" cellspacing="0">
								<tr>
									<td style="width: 3%;vertical-align: top;"><b>4)</b></td>
									<td style="width: 97%;">Visit the Department of Tax Administration, Real Estate Division at 12000 Government Center Parkway, Suite 357, Fairfax, VA 22035, between 8 a.m. and 4:30 p.m. Monday through Friday.</td>
								</tr>
							</table>
						</td>
						<td style="width: 2%;"> </td>
					</tr>
					<tr>
						<td colspan="3" style="height: 10px;"></td>
					</tr>
					<tr>
						<td style="width: 1%;"></td>
						<td style="width: 97%;font-size: 9pt;font-style: italic;">To request this form in an alternate format, contact the Fairfax County Department of Tax Administration at 703-222-8234.</td>
						<td style="width: 2%;"> </td>
					</tr>
					
				</table>
			</td>
		</tr>
		<tr>
			<td style="height: 280px;">
				
			</td>
		</tr>

<!--third-page-->

<?php
   $totalPriceGross = "";
    $topComparableData['subject']->lat;

    $topComparableData['subject']->long;

    $basementAreaSubject='0';
    if($topComparableData['subject']->corelogic_response != ''){
        $corelogic_response=json_decode($topComparableData['subject']->corelogic_response,true);
        $basementAreaSubject=$corelogic_response['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_BASEMENT']['@attributes']['_AreaSquareFeet'];
       // _AssessorsParcelIdentifier
        
    }
    $grossLivingAreaSubject=$topComparableData['subject']->square_footage;

    ?>
        <tr>
          <td style="font-weight: bold;font-size: 10pt;">
            Owner's Estimate of Value - Sale Comparable Report
          </td>
        </tr>
        <tr>
          <td>
            <table cellspacing="0" cellpadding="0" style="width: 100%;font-size: 7pt;">
              <tr>
                <td style="width: 34%;">
                  <table cellspacing="0" cellpadding="0" style="width: 100%;font-size: 7pt;">
                    <tr>
                      <td style="border-bottom: 1px solid #000;border-left: 1px solid #000;border-top: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                        SUBJECT
                      </td>
                    </tr>
                    <tr>
                      <td style="border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;height: 35px;">
                        {{ $topComparableData['subject']->sub_address }}
                      </td>
                    </tr>
                    <tr>
                      <td style="border-bottom: 1px solid #000;height: 80px;border-left: 1px solid #000;border-right: 1px solid #000;text-align: center;">
                      <?php
                        $subjectImageName=$topComparableData['subject']->comDetail->user_searches_id.'_0.jpg';
                        $subjectImagePath='/project/google_images/'.$topComparableData['subject']->comDetail->user_searches_id.'/'.$subjectImageName; 
                        //echo $subjectImagePath;exit;
                      ?>
                      
                        <img src="{{ public_path($subjectImagePath) }}" width="120px">
                        <!--img src="https://maps.googleapis.com/maps/api/streetview?size=300x150&location={{($topComparableData['subject']->lat)?($topComparableData['subject']->lat): 0 }},{{ ($topComparableData['subject']->long)?($topComparableData['subject']->long):0 }}&fov=90&heading=235&pitch=10&key=AIzaSyDZNqX2CPKidMMQgqkaGGm3FMqZ9KX5WVw" width="120px"-->
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <table cellspacing="0" cellpadding="0" style="width: 100%;font-size: 7pt;">
                          <tr>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                              Proximity to Subject
                            </td>
                            <td style="width: 50%;background-color: #ccc;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;padding-top: 2px;">
                              
                            </td>
                          </tr>
                          <tr>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                              Sale Price
                            </td>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                               <?php
                                    setlocale(LC_MONETARY, 'en_US');
                                        //echo money_format('%i', $number) 

                                ?>
                                $ {{ ($topComparableData['subject']->subjectOtherData != null)?(money_format('%!n', $topComparableData['subject']->subjectOtherData->sale_price)):'N/A' }}
                            </td>
                          </tr>
                          <tr>
                            <td style="border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;height: 22.8px;">
                             Sale Price/Gross Living Area
                            </td>
                            <?php
	                            $subjectSalePrice=($topComparableData['subject']->subjectOtherData != null)?($topComparableData['subject']->subjectOtherData->sale_price):0;
								$totalLivingAreaSubject=$topComparableData['subject']->square_footage;
								$subjectPriceDivideBYGrossLiving=($subjectSalePrice/$totalLivingAreaSubject);
									//echo $subjectSalePrice.'<br/>'.$grossLivingAreaSubject;
							?>
							<td style="border-bottom: 1px solid #000;border-right: 1px solid #000;border-bottom: 1px solid #000;padding-left: 2px;">
	              				$ {{ money_format('%!n', $subjectPriceDivideBYGrossLiving) }}
	                        </td>
                          </tr>
                         <!--  <tr>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                             Data Source(s)
                            </td>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;">
                              
                            </td>
                          </tr> -->
                          <tr>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                            VALUE ADJUSTMENTS
                            </td>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                              DESCRIPTION
                            </td>
                          </tr>
                          <!-- <tr>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                              Sale or Financing Concessions
                            </td>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                              @ market
                            </td>
                          </tr> -->
                          <tr>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                             Date of Sale
                            </td>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;">
                             {{ ($topComparableData['subject']->subjectOtherData != null && $topComparableData['subject']->subjectOtherData != '' && $topComparableData['subject']->subjectOtherData->sale_date != '0000-00-00')?(date('m/d/Y',strtotime( $topComparableData['subject']->subjectOtherData->sale_date))):'N/A' }}
                            </td>
                          </tr>
                          <!-- <tr>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                             Leasehold/Fee Simple
                            </td>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                             Fee Simple
                            </td>
                          </tr> -->
                          <tr>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                               Site (SF)
                            </td>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                               {{ number_format(isset($topComparableData['subject']->parcel_size)?($topComparableData['subject']->parcel_size):'0') }}
                            </td>
                          </tr>
                          <tr>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                               Exterior
                            </td>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                               {{ $topComparableData['subject']->exterior}}
                            </td>
                          </tr>
                          <tr>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                              Year Built  
                            </td>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                              {{ (count($topComparableData['subject']) && $topComparableData['subject']->year_built != '')?($topComparableData['subject']->year_built):'N/A' }}
                            </td>
                          </tr>
                         <!--  <tr>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                              Year Renovated 
                            </td>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                              2010
                            </td>
                          </tr> -->
                         <!--  <tr>
                            <td style="width: 50%;border-bottom: 1px solid #000;">
                            
                            </td>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;height: 22.5px;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                              Total | Bdrms | Baths 
                            </td>
                          </tr> -->
                          <tr>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                               Bathroom Count
                            </td>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                               {{ $topComparableData['subject']->bathrooms }}
                            </td>
                          </tr>
                          <tr>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                               Total Living Area (SF)
                            </td>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                              {{ ($grossLivingAreaSubject)?number_format(($grossLivingAreaSubject)):number_format(0)   }}
                            </td>
                          </tr>
                          <tr>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                              Basement  
                            </td>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                               Yes
                            </td>
                          </tr>
                          <tr>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                             Basement Area (SF)
                            </td>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                              {{ ($basementAreaSubject != '')?(number_format($basementAreaSubject)):'0' }}
                            </td>
                          </tr>
                         <!--  <tr>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                              Heating/Cooling 
                            </td>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                              Typical
                            </td>
                          </tr>
                          <tr>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                              Energy Efficient Items
                            </td>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                               Typical
                            </td>
                          </tr> -->
                          <tr>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                             Garage/Carport
                            </td>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                               <?php 
                                    $garageCount = ($topComparableData['subject']->garage!="")?$topComparableData['subject']->garage:'0';
                                ?>
                                {{ $garageCount }} / {{ $topComparableData['subject']->carport }}
                            </td>
                          </tr>
                         <!--  <tr>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;height: 136px;padding-left: 2px;padding-top: 2px;">
                             Exterior Amenities (ex.
                             Porch/Patio/Deck/Pool)
                            </td>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                               None
                            </td>
                          </tr> -->
                          <tr>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                            Porch
                            </td>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                               {{ ($topComparableData['subject']->porch_deck != 0)?'Yes':'No' }}
                            </td>
                          </tr> <tr>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                            Patio
                            </td>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                               {{ ($topComparableData['subject']->patio != 0)?'Yes':'No' }}
                            </td>
                          </tr> <tr>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                           Deck
                            </td>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                               {{ ($topComparableData['subject']->porch_deck != 0)?'Yes':'No' }}
                            </td>
                          </tr> <tr>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                             Pool
                            </td>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                               {{ ($topComparableData['subject']->swimming_pool != 0)?'Yes':'No' }}
                            </td>
                          </tr> 
                          <tr>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                             Fireplace(s)
                            </td>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                              {{ $topComparableData['subject']->fireplace }}
                            </td>
                          </tr>
                          <!-- <tr>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                             Other
                            </td>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                              None
                            </td>
                          </tr> -->
                          <tr>
                              <td colspan="2" style="height: 5px;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;">
                                  
                              </td>
                          </tr>
                           <tr>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;background-color: #ccc;font-weight: bold;padding-left: 2px;padding-top: 2px;">
                               Net Adjustment (Total)
                            </td>
                            <td style="width: 50%;border-bottom: 1px solid #000;border-left: 1px solid #000;background-color: #ccc;border-right: 1px solid #000;">
                            
                            </td>
                          </tr>
                          <tr>
                            <td colspan="2" style="border-bottom: 1px solid #000;border-left: 1px solid #000;background-color: #ccc;border-right: 1px solid #000;font-weight: bold;padding-left: 2px;padding-top: 2px;">
                              Adjusted Sale Price of Comparables
                            </td>
                          </tr>
                           <tr>
                              <td colspan="2" style="border-bottom: 1px solid #000;border-left: 1px solid #000;background-color: #ccc;font-size: 9pt;font-weight: bold;padding-left: 2px;padding-top: 2px;">
                                 Initial Estimate of Value: 
                              </td>
                          </tr> 
                          <tr>
                              <td colspan="2" style="height: 3px;border-bottom: 1px solid #000;border-left: 1px solid #000;">
                                  
                              </td>
                          </tr>

                        </table>
                      </td>
                    </tr>
                  </table>
                </td>
                <?php 
                $TotalCalculatedArray=array();
                    
                $counting = 0; $adjustedSalePrice=array(); ?>
                @if(isset($topComparableData['comparables']) && count($topComparableData['comparables'])>0)
                    @foreach($topComparableData['comparables'] as $key => $compDetails)
                <td style="width: 22%;">
                	<?php
                        $compImageName=$topComparableData['subject']->comDetail->user_searches_id.'_'.($key+1).'.jpg';
                        $compImagePath='/project/google_images/'.$topComparableData['subject']->comDetail->user_searches_id.'/'.$compImageName;
                    ?>     
                    <table cellspacing="0" cellpadding="0" style="width: 100%;font-size: 7pt;">
                        <tr>
                            <td style="font-weight: bold;border-bottom: 1px solid #000;border-top: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                              COMPARABLE SALE NO. {{ $key+1 }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;height: 35px;">
                             
                              {{ $compDetails->com_address }} 
                            </td>
                        </tr>
                        <tr>
                          <td style="border-bottom: 1px solid #000;height: 80px;border-right: 1px solid #000;text-align: center;">
                           <!--img style="width: 120px;" src="{{ public_path('/project/resources/assets/customer/images/comparable1.png') }}"-->
                           <img src="{{ public_path($compImagePath) }}" style="width: 120px;">

                          </td>
                        </tr>
                        <tr>
                            <td>
                               <table cellspacing="0" cellpadding="0" style="width: 100%;font-size: 7pt;">
                                <tr>
                                    <td colspan="2" style="border-bottom: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                      {{ $compDetails->com_details['distance_from_subject'] }} miles  
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 50%;background-color: #ccc;border-bottom: 1px solid #000;">
                                        
                                    </td>
                                    <td style="width: 50%;border-left: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                       $ {{ money_format('%!n', $compDetails->com_details['sale_price']) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-bottom: 1px solid #000;padding-left: 2px;padding-top: 2px;height: 22.8px;">
                                        $ <span><?php
                                                $sale_divide_totalARea=($compDetails->com_details['sale_price']/
                                                (($compDetails->com_details['square_footage'] != '')?($compDetails->com_details['square_footage']):1));
                                             ?>
                                                 {{ money_format('%!n', $sale_divide_totalARea) }}</span> 
                                    </td>
                                    <td style="background-color: #ccc;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;">
                                        
                                    </td>
                                </tr>
                                <!-- <tr>
                                    <td colspan="2" style="border-bottom: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                        MRIS #DC8567375
                                    </td>
                                </tr> -->
                                <tr>
                                    <td style="border-bottom: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                        DESCRIPTION 
                                    </td>
                                    <td style="color: #ff0000;border-left: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                         $
                                        Adjustment
                                    </td>
                                </tr>
                               <!--  <tr>
                                    <td style="border-bottom: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                        Subsidy 
                                    </td>
                                    <td style="color: #ff0000;border-left: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                         $ <span style="padding-left: 50px;">-</span> 
                                    </td>
                                </tr> -->
                                <tr>
                                    <td style="border-bottom: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                       {{ date('m/d/Y', strtotime($compDetails->com_details['date_of_sale'])) }}
                                    </td>
                                    <td style="color: #ff0000;border-left: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;text-align: right;padding-right: 5px;">
                                      --  
                                    </td>
                                </tr>
                                <!-- <tr>
                                    <td style="border-bottom: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                        Fee Simple 
                                    </td>
                                    <td style="color: #ff0000;border-left: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                         $ <span style="padding-left:50px;">-</span>  
                                    </td>
                                </tr> -->
                                <tr>
                                    <td style="border-bottom: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                        <span>  {{ number_format($compDetails->parcel_size) }} </span>
                                    </td>
                                    <td style="color: #ff0000;border-left: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;text-align: right;padding-right: 5px;">
                                        -- 
                                    </td>
                                </tr>
                                 <tr>
                                    <td style="border-bottom: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                       {{ $compDetails->com_details->exterior }}
                                    </td>
                                    <td style="color: #ff0000;border-left: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;text-align: right;padding-right: 5px;">
                                        --
                                    </td>
                                </tr>
                                 <tr>
                                    <td style="border-bottom: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                      {{ $compDetails->com_details['year_built'] }}
                                    </td>
                                    <td style="color: #ff0000;border-left: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;text-align: right;padding-right: 5px;">
                                         --  
                                    </td>
                                </tr>
                                 <!-- <tr>
                                    <td style="border-bottom: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                        2004
                                    </td>
                                    <td style="color: #ff0000;border-left: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                          $  <span style="padding-left:50px;">-</span>   
                                    </td>
                                </tr> -->
                                <!-- <tr>
                                    <td style="border-bottom: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                        Total | Bdrms | Baths
                                    </td>
                                    <td style="border-left: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;">
                                            
                                    </td>
                                </tr> -->
                                <tr>
                                    <td style="border-bottom: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                        {{ $compDetails->bathrooms }}
                                    </td>
                                    <?php  $BatroomPrice=($compDetails->com_details['total_bathrooms'] != 0)?(money_format('%!n',($compDetails->com_details['total_bathrooms']))):'0'; ?>
                                    <td style="color: #ff0000;border-left: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                         <table cellspacing="0" cellpadding="0" style="width: 100%;">
                                         	<tr>
                                         		<td style="width: 10%;">
                                         			$ 
                                         		</td>
                                         		<td style="width: 90%;text-align: right;padding-right: 5px;">
                                         			<span>{{ $BatroomPrice }}</span> 
                                         		</td>
                                         	</tr>
                                         </table> 
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-bottom: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                        <span>{{ ($compDetails->com_details['square_footage'] != '')?(number_format($compDetails->com_details['square_footage'])):0 }}</span> 
                                    </td>
                                    <?php
                                        $grossLivingAreaCOMP=($compDetails->com_details['gross_living_area'] != '')?($compDetails->com_details['gross_living_area']):0;

                                    ?>   
                                    <td style="color: #ff0000;border-left: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                        <table cellspacing="0" cellpadding="0" style="width: 100%;">
                                         	<tr>
                                         		<td style="width: 10%;">
                                         			$ 
                                         		</td>
                                         		<td style="width: 90%;text-align: right;padding-right: 5px;">
                                         			<span>{{ ($compDetails->com_details['square_footage_price'] != '')?(money_format('%!n',$compDetails->com_details['square_footage_price'])):0 }}</span>
                                         		</td>
                                         	</tr>
                                        </table>     
                                    </td>
                                </tr>
                                 <tr>
                                    <td style="border-bottom: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                        {{ ($compDetails->com_details['basement'] > 0 && $compDetails->com_details['basement'] != '')?'Yes':'No' }}
                                    </td>
                                    <td style="color: #ff0000;border-left: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;text-align: right;padding-right: 5px;">
                                        --
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-bottom: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                      N/A
                                    </td>
                                    <?php $baseMent=($compDetails->com_details['basement'] != '')?($compDetails->com_details['basement']):0; ?>
                                    <td style="color: #ff0000;border-left: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;text-align: right;padding-right: 5px;">
                                        --
                                    </td>
                                </tr>
                                <!--  <tr>
                                    <td style="border-bottom: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                        Typical
                                    </td>
                                    <td style="color: #ff0000;border-left: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                          $ <span style="padding-left:50px;">-</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-bottom: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                        Typical
                                    </td>
                                    <td style="color: #ff0000;border-left: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                          $ <span style="padding-left:50px;">-</span>
                                    </td>
                                </tr> -->
                                <tr>
                                    <td style="border-bottom: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                         {{ $compDetails->garage }} / {{ $compDetails->carport }}
                                    </td>
                                    <td style="color: #ff0000;border-left: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                        <table cellspacing="0" cellpadding="0" style="width: 100%;">
                                         	<tr>
                                         		<td style="width: 10%;">
                                         			$ 
                                         		</td>
                                         		<td style="width: 90%;text-align: right;padding-right: 5px;">
                                         		   <span>{{ ($compDetails->com_details['garage'] !='' && $compDetails->com_details['garage'] != '-')?(money_format('%!n', $compDetails->com_details['garage'])): (money_format('%!n', 0)) }}</span>
                                         		</td>
                                         	</tr>
                                        </table>              
                                    </td>
                                </tr>
                                 <tr>
                                    <td style="border-bottom: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                       No
                                    </td>
                                    <td style="color: #ff0000;border-left: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                        <table cellspacing="0" cellpadding="0" style="width: 100%;">
                                         	<tr>
                                         		<td style="width: 10%;">
                                         			$ 
                                         		</td>
                                         		<td style="width: 90%;text-align: right;padding-right: 5px;">
                                         		   <span>0</span>
                                         		</td>
                                         	</tr>
                                        </table>         
                                    </td>
                                </tr>
                                 <tr>
                                    <td style="border-bottom: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                       No
                                    </td>
                                    <td style="color: #ff0000;border-left: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                        <table cellspacing="0" cellpadding="0" style="width: 100%;">
                                         	<tr>
                                         		<td style="width: 10%;">
                                         			$ 
                                         		</td>
                                         		<td style="width: 90%;text-align: right;padding-right: 5px;">
                                         		   <span>0</span>
                                         		</td>
                                         	</tr>
                                        </table>  
                                    </td>
                                </tr>
                                 <tr>
                                    <td style="border-bottom: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                       No
                                    </td>
                                    <td style="color: #ff0000;border-left: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                        <table cellspacing="0" cellpadding="0" style="width: 100%;">
                                         	<tr>
                                         		<td style="width: 10%;">
                                         			$ 
                                         		</td>
                                         		<td style="width: 90%;text-align: right;padding-right: 5px;">
                                         		   <span>0</span>
                                         		</td>
                                         	</tr>
                                        </table> 
                                    </td>
                                </tr>
                                 <tr>
                                    <td style="border-bottom: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                       No
                                    </td>
                                    <td style="color: #ff0000;border-left: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                        <table cellspacing="0" cellpadding="0" style="width: 100%;">
                                         	<tr>
                                         		<td style="width: 10%;">
                                         			$ 
                                         		</td>
                                         		<td style="width: 90%;text-align: right;padding-right: 5px;">
                                         		   <span>0</span>
                                         		</td>
                                         	</tr>
                                        </table> 
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-bottom: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                       <?php $fireplaceCount=($compDetails->fireplace != '')?($compDetails->fireplace):'0'; ?>
                                        {{ $fireplaceCount }}
                                    </td>
                                    <td style="color: #ff0000;border-left: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                        <table cellspacing="0" cellpadding="0" style="width: 100%;">
                                         	<tr>
                                         		<td style="width: 10%;">
                                         			$ 
                                         		</td>
                                         		<td style="width: 90%;text-align: right;padding-right: 5px;">
                                         		   <span>{{ money_format('%!n',$compDetails->com_details['fireplace']) }}</span>
                                         		</td>
                                         	</tr>
                                        </table>    
                                    </td>
                                </tr>
                               <!--  <tr>
                                    <td style="border-bottom: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                       None
                                    </td>
                                    <td style="color: #ff0000;border-left: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                          
                                    </td>
                                </tr> -->
                                <tr>
                                    <td colspan="2" style="height: 5px;border-bottom: 1px solid #000;border-right: 1px solid #000;">
                                        
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-bottom: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                     
                                    </td>
                                    <?php
                                        $adjustedPriceFinal=$compDetails->com_details['net_adjustment'];
                                    ?>
                                    <td style="color: #ff0000;border-left: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                        <table cellspacing="0" cellpadding="0" style="width: 100%;">
                                         	<tr>
                                         		<td style="width: 10%;">
                                         			$ 
                                         		</td>
                                         		<td style="width: 90%;text-align: right;padding-right: 5px;">
                                         		    <span>{{ money_format('%!n',$adjustedPriceFinal) }}</span> 
                                         		</td>
                                         	</tr>
                                        </table>   
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-bottom: 1px solid #000;padding-left: 2px;">
                                        <?php $Net_Adjustment_price=money_format('%!n',$compDetails->com_details['price_after_adjustment']); ?>
                                        $ {{  $Net_Adjustment_price }}
                                    </td>
                                    <td style="border-left: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;padding-left: 2px;padding-top: 2px;">
                                    
                                        <?php echo '$ <span class="special_price">'.money_format('%!n',$compDetails->com_details['sale_price_divided_sf']).'</span>'; ?>
                                    </td>
                                </tr>
                                <tr>
                              
                                 @if($counting < 1)
                                    <td colspan="2" style="border-bottom: 1px solid #000;background-color: #ccc;font-size: 9pt;font-weight: bold;padding-left: 2px;padding-top: 2px;">
                                    $ {{ money_format('%!n',$topComparableData['subject']->subjectOtherData->appeal_amount) }}
                                    </td>
                                 @else
                                    <td colspan="2" style="border-bottom: 1px solid #000;background-color: #ccc;font-size: 9pt;font-weight: bold;padding-left: 2px;padding-top: 2px;height: 14.8px;">
                                    </td>
                                 @endif  
                              
                          </tr> 
                          <tr>
                              <td colspan="2" style="height: 3px;border-bottom: 1px solid #000;">
                                  
                              </td>
                          </tr>

                               </table> 
                            </td>
                        </tr>
                    </table>
                    
                </td>

                <?php $counting++; ?>
                @endforeach
                @endif
               <!-- END LOOP COMPARABLES-->
              </tr>
            </table>
          </td>
        </tr>
        <tr>
            <td style="height: 10px;">
                
            </td>
        </tr>
        <tr>
            <td>
                <table cellspacing="0" cellpadding="0" style="font-size: 9pt;width: 100%;">
                    <tr>
                        <td style="width: 40%;font-weight: bold;border-bottom: 1px solid #000;">
                          Additional Owner Adjustments  
                        </td>
                        <td style="width: 10%;">
                            
                        </td>
                        <!--td style="width: 50%; font-weight: bold;border-bottom: 1px solid #000;">
                          Provide a brief description of these adjustments:  
                        </td-->
                    </tr>
                    <?php  $totalLookupsPrice=0; $alreadyExists=array();  ?>
                    @if(count($topComparableData['subject']->lookUpsDetail))
                        @foreach($topComparableData['subject']->lookUpsDetail as $lookupDetail)
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
                    <tr>
                        <td style="width: 40%;padding-top: 0px;">
                          - {{ ucfirst($FullLookupName) }} 
                        </td>
                        <?php 
                            $child_details = Helper::getChildLookupDetail($lookupDetail->lookup_id);
                            $additional_homeowner_question_lookup_id = (count($child_details)) ? $child_details->lookup_id : $lookupDetail->lookup_id; 
                            $maxRange = $lookupDetail->value;
                            $maxLimitValue = "$".$maxRange;
                            $minRange = $lookupDetail->value1;
                            $minimitValue = "$".$minRange;
                            $percentageValue = $lookupDetail->value2;
                            $concateVal = $maxRange."-".$minRange."-".$percentageValue;
                        ?>
                        <td style="width: 10%;color: #ff0000;padding-top: 0px;">
                            $ {{ money_format('%!n', ($lookupDetail->lookup_value)) }} 
                        </td>
                        <?php $totalLookupsPrice = $totalLookupsPrice + $lookupDetail->lookup_value; ?>
                        <!--td style="width: 50%;padding-top: 0px;">
                          <?php 
                                $str = str_replace('$maxLimitValue', $maxLimitValue, $lkDesc);
                                    
                               // echo ucfirst(str_replace('$minimitValue', $minimitValue, $str));
                            ?>
                        </td-->
                    </tr>
                    <tr>
                        <td colspan="3" style="height: 8px;">
                            
                        </td>
                    </tr>
                    <?php } ?>
                    @endforeach
                   @endif
                    <tr>
                        <td colspan="3">
                            <table cellspacing="0" cellpadding="0" style="font-size: 9pt;width: 100%;background-color: yellow;">
                                <tr>
                                 <td style="width: 40%;border-top: 1px solid #000;border-bottom: 1px solid #000;font-weight: bold;">
                                   FINAL OWNERSHIP ESTIMATE OF VALUE
                               </td>
                               <td style="width: 15%;padding-right: 10px;border-top: 1px solid #000;border-bottom: 1px solid #000;font-weight: bold;">
                                   $ {{ money_format('%!n',(($topComparableData['subject']->subjectOtherData->appeal_amount) - ($totalLookupsPrice))) }}
                               </td>
                               <td style="width: 45%;border-top: 1px solid #000;border-bottom: 1px solid #000;">

                               </td> 
                           </tr>
                       </table>
                   </td>
                        
                    </tr>
                    <tr>
                        <td colspan="3" style="height: 15px;">
                            
                        </td>
                    </tr>
                    <!--tr>
                        <td colspan="3" style="font-size: 8pt;">
                            Documents prepared by: Assessment Solutions, LLC
                        </td>
                    </tr-->
                </table>
            </td>
        </tr>






  </tbody>
</table>