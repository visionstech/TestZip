<table cellpadding="2" cellspacing="0" style="width:100%; margin:0 auto;font-weight: normal;font-family: arial;color:#000000;border:0;line-height: 1.2;font-size: 10pt;border: 1px solid #000000;">
	<tbody>
		<tr><td style="height: 15px;"></td></tr>
		<tr>
			<td style="text-align: center;font-size: 12pt;font-weight: bold;line-height: 1.2">Government of the District of Columbia<br> Office of Tax and Revenue<br> Real Property Tax Administration</td>
		</tr>
		<tr>
			<td style="text-align: center;"><img style="max-width: 50px;" src="{{ public_path('/project/resources/assets/customer/images/flag.png') }}"> </td>
		</tr>
		<tr>
			<td style="text-align: center;font-size: 12pt;font-weight: bold;line-height: 1.2;vertical-align: top;">Real Property Assessment Division<br> First Level Administrative Review Application</td>
		</tr>
		<tr>
			<td>
				<table style="width: 100%;" cellspacing="0" cellpadding="0">
					<tr>
						<td style="width: 3.5%"></td>
						<td style="width: 93%">
							<table style="width: 100%;" cellspacing="0" cellpadding="0">
								<tr>
									<td style="height: 10px;"></td>
								</tr>
								<tr>
									<td>The Real Property Tax Administration (RPTA) strives to assess property at 100% of estimated market value. Estimated market value is defined as the most probable price that a buyer would pay a willing seller on the open market. As the property owner, you are given the opportunity to dispute the assessment of your real property through a formal appeal process. The process involves three levels of appeal, beginning with the First Level Administrative Review. Subsequent steps include appealing before the Real Property Tax Appeals Commission and DC Superior Court. You must, however, start at the First Level before proceeding to the next levels of appeal. Please complete the following information in order to file the First Level appeal of your property.</td>
								</tr>
								<tr>
									<td style="height:5px;"></td>
								</tr>
								<tr>
									<td style="color:#ff0000;font-size: 10pt;font-weight: bold;text-align: center;line-height: 1;vertical-align: top;word-spacing: 5px;height: 20px; ">YOU MUST FILE YOUR APPEAL ON OR BEFORE <u style="word-spacing: normal;">{{ $appeal_deadline_date }}</u></td>
								</tr>
								
								<tr>
									<td style="vertical-align: bottom;">
										<table style="width: 100%;" cellspacing="0" cellpadding="0">
											<tr>
												<td style="width:15%;vertical-align: bottom;height: 25px; ">*Owner's Name:</td>
												<td style="width:38%"><span style="display: inline-block;width:97%;border-bottom: 1px solid #000;height: 20px;">{{ ucwords($owner_name) }}</span></td>
												<td style="width:7%;vertical-align: bottom; ">*Square:</td>
												<td style="width:10% "><b style="display: inline-block;width:95%;border-bottom: 1px solid #000;height: 20px;"></b></td>
												<td style="width:6%;vertical-align: bottom; ">*Suffix:</td>
												<td style="width: 10%;"><b style="display: inline-block;width:90%;border-bottom: 1px solid #000;height: 20px;"></b></td>
												<td style="width:4%;vertical-align: bottom;">*Lot:</td>
												<td style="width: 10%;"><b style="display: inline-block;width:100%;border-bottom: 1px solid #000;height: 20px;"></b></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td style="vertical-align: bottom;">
										<table style="width: 100%;" cellspacing="0" cellpadding="0">
											<tr>
												<td style="width:17%;vertical-align: bottom;height: 25px; ">*Property Address:</td>
												<td style="width:32%"><span style="display: inline-block;width:97%;border-bottom: 1px solid #000;height: 20px;">{{ $street_address }}</span></td>
												<td style="width:5%;vertical-align: bottom; ">*City:</td>
												<td style="width:16% "><span style="display: inline-block;width:95%;border-bottom: 1px solid #000;height: 20px;">{{ $city }}</span></td>
												<td style="width:6%;vertical-align: bottom; ">*State:</td>
												<td style="width: 10%;"><span style="display: inline-block;width:90%;border-bottom: 1px solid #000;height: 20px;">{{ $state }}</span></td>
												<td style="width:4%;vertical-align: bottom;">*Zip:</td>
												<td style="width: 10%;"><span style="display: inline-block;width:100%;border-bottom: 1px solid #000;height: 20px;">{{ $zip_code }}</span></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td style="vertical-align: bottom;">
										<table style="width: 100%;" cellspacing="0" cellpadding="0">
											<tr>
												<td style="width:23%;vertical-align: bottom;height: 25px; ">*Contact Phone Numbers:</td>
												<td style="width:18%"><span style="display: inline-block;width:85%;border-bottom: 1px solid #000;height: 20px;">{{ $phone_number }}</span></td>
												<td style="width:14% "><span style="display: inline-block;width:95%;border-bottom: 1px solid #000;height: 20px;"></span></td>
												<td style="width:7%;vertical-align: bottom; ">E-mail:</td>
												<td style="width: 38%;"><span style="display: inline-block;width:100%;border-bottom: 1px solid #000;height: 20px;">{{ $email }}</span></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td style="vertical-align: top;font-size: 8pt;">* Required information</td>
								</tr>
								<tr>
									<td style="vertical-align: bottom;">Please indicate the basis for your appeal (examples of supporting documentation are shown below):</td>
								</tr>
								<tr>
									<td style="vertical-align: bottom;">
										<table style="width: 100%;border:1px solid #000;" cellspacing="0" cellpadding="0">
											<tr>
												<td style="width:25%;font-size: 11pt; font-weight: bold;border-right: 1px solid #000000;line-height: 1.2;height: 45px;vertical-align: top;"> &nbsp;  [ ] Estimated  &nbsp; Market <br> &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp; Value</td>
												<td style="width:75%;vertical-align: top;"> &nbsp; Examples: <span style="display: inline-block;width: 2px;text-align: center;"></span><img style="vertical-align: middle;display: inline-block;max-width: 33%;border-top: 7px solid #ffffff;" src="{{ public_path('project/resources/assets/customer/images/doc-bulit.png') }}" /><span style="display: inline-block;width: 2px;text-align: center;"></span> recent written appraisal <span style="display: inline-block;width: 2px;text-align: center;"></span><img style="vertical-align: middle;display: inline-block;max-width: 33%;border-top: 7px solid #ffffff;" src="{{ public_path('project/resources/assets/customer/images/doc-bulit.png') }}" /><span style="display: inline-block;width: 2px;text-align: center;"></span>  recent settlement statement <br> &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  <span style="display: inline-block;width: 2px;text-align: center;"></span><img style="vertical-align: middle;display: inline-block;max-width: 33%;border-top: 7px solid #ffffff;" src="{{ public_path('project/resources/assets/customer/images/doc-bulit.png') }}" /><span style="display: inline-block;width: 2px;text-align: center;"></span>  property insurance documents</td>
											</tr>
											<tr>
												<td style="width:25%;font-size: 11pt;font-weight: bold;border-right: 1px solid #000000;border-top: 1px solid #000000;border-bottom: 1px solid #000000;line-height: 1.2;height: 30px;"> &nbsp; [ ] Equalization</td>
												<td style="width:75%;border-top: 1px solid #000000;border-bottom: 1px solid #000000;"> &nbsp; Examples: &nbsp; a listing of properties that you consider to be comparable to your property.</td>
											</tr>
											<tr>
												<td style="width:25%;font-size: 11pt;font-weight: bold;border-right: 1px solid #000000;line-height: 1.2;height: 80px;vertical-align: top;"> &nbsp; <span style="line-height: 1.5;">[ ] Classification</span></td>
												<td style="width:75%;">
													<table style="width: 100%;" cellspacing="0">
														<tr>
															<td > &nbsp; Indicate current use of the property, and date the use started: Date: <b style="display: inline-block;width:20%;border-bottom: 1px solid #000;height: 20px;"></b><br> &nbsp; [ ] Residential &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp;[ ] Commercial  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;[ ] Mixed Use<br> &nbsp; <u><b style="font-size: 10pt;"><i>Note:</i> If the appeal is based on Class 3 or Class 4 classification, do NOT use this</u></b> <br> &nbsp; <u><b>form, call (202) 442-4332 for appeal information.</b></u></td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td style=" width:25%;font-size: 11pt;font-weight: bold;border-right: 1px solid #000000;border-top: 1px solid #000000;line-height: 1.2;height: 40px;vertical-align: bottom;"> &nbsp; [ ] Property Damage or<br> &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp; &nbsp; &nbsp;Condition</td>
												<td style="width:75%;border-top: 1px solid #000000;"> &nbsp; Examples: &nbsp; <img style="vertical-align: middle;display: inline-block;max-width: 33%;border-top: 7px solid #ffffff;" src="{{ public_path('project/resources/assets/customer/images/doc-bulit.png') }}" /> cost estimates &nbsp; <img style="vertical-align: middle;display: inline-block;max-width: 33%;border-top: 7px solid #ffffff;" src="{{ public_path('project/resources/assets/customer/images/doc-bulit.png') }}" /> damage claims.</td>
											</tr>
											<tr>
												<td colspan="2">
													<table style="width: 100%;border-top:1px solid #000;" cellspacing="0" cellpadding="0">
														<tr>
															<td style="width: 40%;font-size: 11pt;color: #ff0000;height: 30px;border-right: 1px solid #000;font-weight:bold;"> &nbsp; TY 2017 TOTAL VALUE: $ {{ $total_assessment_value }}</td>
															<td style="width: 60%;font-size: 11pt;color: #ff0000;height: 30px;font-weight:bold;"> &nbsp; REQUESTED TY 2017 TOTAL VALUE: $</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
										
									</td>
								</tr>
								<tr><td style="height: 10px;"></td></tr>
								<tr>
									<td style="color: #ff0000;">The DC Code 47-825.01a(d)(1) allows an owner to petition for an administrative review of the proposed assessment on or before April 1<sup>st</sup>.&nbsp; We conduct both telephone and in-person interviews as well as written petitions. Telephone and in-person interviews are conducted by appointment only. If you fail to appear and have not notified us twenty-four (24) hours in advance of the appointed time, your review will be converted to a written review and only the information furnished with your original petition will be considered in the review.</td>
								</tr>
								<tr><td style="height: 10px;"></td></tr>
								<tr>
									<td style="line-height: 1.2;"><b>New Homeowner -</b> Do not use this form - New Owner Forms may be obtained from our Web site or from RPTA at the address and/or telephone number below.</td>
								</tr>
								<tr>
									<td style="height: 5px;"></td>
								</tr>
								<tr>
									<td style="font-size: 10pt;line-height: 1.2;font-weight: bold;font-style: italic;">Please select your preferred hearing method: [ ] Written [ ] Telephone <b style="vertical-align: bottom;display: inline-block;width:25%;border-bottom: 1px solid #000;height: 20px;"></b> [ ] In-Person</td>
								</tr>
								<tr>
									<td>
										<table style="width: 100%;" cellspacing="0" >
											<tr>
												<td style="width: 65%"></td>
											 	<td style="width: 35%;font-weight: bold;vertical-align: top;line-height: 1;font-size: 8pt;font-style: italic;">(Contact Phone Number)</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td>Will you be appealing any other properties? [ ] Yes [ ] No, If Yes, please complete an appeal application for each.</td>
								</tr>
								<tr>
									<td style="height: 5px;"></td>
								</tr>
								<tr>
									<td>
										<table style="width: 100%;line-height: 1" cellspacing="0" cellpadding="0">
											<tr>
												<td style="width: 42%;vertical-align: bottom;font-style: italic;">Return completed form to:</td>
												<td style="width: 58%;vertical-align: bottom;">Print Name: <b style="display: inline-block;width:77%;border-bottom: 1px solid #000;height: 15px;"></b></td>
											</tr>
											<tr>
												<td style="width: 42%;vertical-align: bottom;">Office of Tax and Revenue</td>
												<td style="width: 58%;vertical-align: bottom;">Owner/Agent* Signature: <b style="display: inline-block;width:58%;border-bottom: 1px solid #000;height: 15px;"></b></td>
											</tr>
											<tr>
												<td style="width: 42%;vertical-align: bottom;">Real Property Tax Admin. - Appeals Section</td>
												<td style="width: 58%;vertical-align: bottom;">Date <span style="font-size: 8pt;">(mm/dd/yyyy)</span> : <b style="display: inline-block;width:69%;border-bottom: 1px solid #000;height: 15px;"></b></td>
											</tr>
											<tr>
												<td style="width: 42%;vertical-align: bottom;">P.O. Box 71440</td>
												<td style="width: 58%;vertical-align: bottom;">Daytime Phone:<b style="display: inline-block;width:72.5%;border-bottom: 1px solid #000;height: 15px;"></b></td>
											</tr>
											<tr>
												<td style="width: 42%;vertical-align: bottom;">Washington, D.C. 20024</td>
												<td style="width: 58%;vertical-align: bottom;">Evening Phone:<b style="display: inline-block;width:73%;border-bottom: 1px solid #000;height: 15px;"></b></td>
											</tr>
											<tr>
												<td style="width: 42%;vertical-align: top;"></td>
												<td style="width: 58%;vertical-align: top;font-size:8pt;line-height: 1.1; ">*If not the owner, a <span style="font-style: italic;">Letter of Agent Authorization</span> must be attached.</td>
											</tr>
											
										</table>
									</td>
								</tr>
								<tr>
									<td style="height: 10px;"></td>
								</tr>
								<tr>
									<td>Assessment information about your property and comparable properties may be found on our Web site located at, <a style="color: #0000ff;font-weight: bold;" href="">http://www.cfo.dc.gov/otr/</a>, or you may call (202) 727-4TAX (4829) for assistance.</td>
								</tr>
								<tr>
									<td style="border-bottom: 2px solid #000;vertical-align: bottom;font-size: 7pt;line-height: 1;height: 15px;">Rev.2/2016</td>
								</tr>
								<tr>
									<td style="text-align: center;height: 25px;vertical-align: top;">1101 4<sup>th</sup> Street, SW, Second Floor, Customer Service Center, Washington, D.C. 20024</td>
								</tr>
							</table>
						</td>
						<td style="width: 3.5%"></td>
					</tr>
				</table>
			</td>
		</tr>
	</tbody>
</table>