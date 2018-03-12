<table cellpadding="0" cellspacing="0" style="width:100%; margin:0 auto;font-weight: normal;font-family: Arial, sans-serif;color:#000000;border:0;line-height: 1.2;font-size: 11pt;max-width: 650px;">
	<tbody>
	<?php //echo "<pre>";print_r($params);exit; ?>
		<tr>
			<td>
				<table cellpadding="0" cellspacing="0" style="width: 100%;font-size: 9pt;">
					<tr>
						<td style="height: 30px;">
							
						</td>
					</tr>
					<tr>
						<td>
							{{ date('F d, Y') }}
						</td>
					</tr>
					<tr>
						<td style="height: 18px;">
							
						</td>
					</tr>
					<tr>
					@if($params['county_name'] == 'Montgomery')
						<td>
							Montgomery County Assessments<br>
                            30 W Gude Drive, Suite 400<br>
                            Rockville, Maryland 20850<br>
						</td>
					@elseif($params['county_name'] == 'Baltimore')
						<td>
							Baltimore County Assessments<br>
                            Hampton Plaza<br>
                            300 E Joppa Rd, Suite 602<br>
                            Towson, Maryland 21286<br>
						</td>
					@elseif($params['county_name'] == 'Howard')
						<td>
							Howard County Assessments<br>
                            District Court Multi-Service Center<br>
                            3451 Court House Drive<br>
                            Ellicott City, Maryland 21043<br>
						</td>
				    @elseif($params['county_name'] == 'Prince George\'s')
				    	<td>
							Prince George’s County Assessments<br>
                            14735 Main St, Suite 354B<br>
                            Upper Marlboro, Maryland 20772<br>
						</td>
					@else 
						<td>
							City of Baltimore Assessments<br>
                            William Donald Schaefer Tower<br>
                            6 Saint Paul St, 11<sup>th</sup> Floor<br>
                            Baltimore, Maryland 21202-1608<br>
						</td>
					@endif
					</tr>
					<tr>
						<td style="height: 20px;">
							
						</td>
					</tr>
					<tr>
						<td style="font-weight: bold;">
							Re:&nbsp; &nbsp; &nbsp; {{ date('Y') }} In-Cycle Tax Appeal<br>
							<span style="color:#ff2e00;">(Note – The year will be the current year)</span>
						</td>
					</tr>
					<tr>
						<td style="height: 30px;">
							
						</td>
					</tr>
					<tr>
						<td>
							To whom it may concern:
						</td>
					</tr>
					<tr>
						<td style="height: 15px;">
							
						</td>
					</tr>
					<tr>
						<td>
						<?php $SearchAddress = $params['search_address'][0]->address_line_1.", ".$params['search_address'][0]->address_line_2.", ".$params['search_address'][0]->city.", ".$params['state_name'].', '.$params['county_name'].", ".$params['search_address'][0]->postal_code; ?>
							Enclosed is my first level appeal for {{ $SearchAddress }}.  The parcel number is {{ $params['parcel_number'] }}.
						</td>
					</tr>
					<tr>
						<td style="height: 15px;">
							
						</td>
					</tr>
					<tr>
						<td>
							Please contact me if you need any additional information.
						</td>
					</tr>
					<tr>
						<td style="height: 30px;">
							
						</td>
					</tr>
					<tr>
						<td>
							Regards,
						</td>
					</tr>
					<tr>
						<td style="height: 40px;">
							
						</td>
					</tr>
					<tr>
						<td>
							{{ $params['owner_name'] }}<br>
                            [OWNER PHONE NUMBER]<br>
                            [OWNER EMAIL ADDRESS]
						</td>
					</tr>
					
				</table>
			</td>
		</tr>
	</tbody>
</table>