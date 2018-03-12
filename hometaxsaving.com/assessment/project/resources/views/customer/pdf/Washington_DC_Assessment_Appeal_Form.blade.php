<table cellpadding="20" cellspacing="0" style="width:100%; margin:0 auto;font-weight: normal;font-family: Arial, sans-serif;color:#000000;border:0;line-height: 1.2;font-size: 11pt;max-width: 650px; border:1px solid #000;">
    <tbody>
        <tr>
            <td style="padding-bottom: 0;">
                <table cellpadding="0" cellspacing="0" style="width: 100%; font-size: 12pt;font-weight: bold;text-align: center;">
                    <tr>
                        <td>
                            Government of the District of Columbia
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Office of Tax and Revenue
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Real Property Tax Administration
                        </td>
                    </tr>
                    <tr>
                        <td>
                             <img style="width: 60px;" src="{{ public_path('/project/resources/assets/customer/images/flag.png') }}">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Real Property Assessment Division
                        </td>
                    </tr>
                    <tr>
                        <td>
                            First Level Administrative Review Application
                        </td>
                    </tr>
                    <tr>
                        <td style="height: 10px;">
                            
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding-bottom: 0; padding-top: 0;">
                <table cellpadding="0" cellspacing="0" style="width: 100%; font-size: 9pt;">
                    <tr>
                        <td style="text-align: justify;">
                            The Real Property Tax Administration (RPTA) strives to assess property at 100% of estimated market value. Estimated market value is defined as the most probable price that a buyer would pay a willing seller on the open market. As the property owner, you are given the opportunity to dispute the assessment of your real property through a formal appeal process. The process involves three levels of appeal, beginning with the First Level Administrative Review. Subsequent steps include appealing before the Real Property Tax Appeals Commission and DC Superior Court. You must, however, start at the First Level before proceeding to the next levels of appeal. Please complete the following information in order to file the First Level appeal of your property.
                        </td>
                    </tr>
                    <tr>
                        <td style="height: 4px;">
                            
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center; color: #ff0000;font-weight: bold;font-size: 11pt;">
                            YOU MUST FILE YOUR APPEAL ON OR BEFORE <u style="vertical-align: top;">{{ $appeal_deadline_date }}</u>
                        </td>
                    </tr>
                    <tr>
                        <td style="height: 12px;">
                            
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding-bottom: 0;padding-top: 0;">
                <table cellspacing="0" cellpadding="0" style="width: 100%; font-size: 10pt;">
                    <tr>
                        <td style="width: 55%;">
                            *Owner’s Name: <b style="border-bottom: 1px solid #000; display: inline-block;width: 70.5%; height: 14px;">{{ ucwords($owner_name) }}</b>
                        </td>
                        <td style="width: 18%;">
                            *Square: <b style="border-bottom: 1px solid #000; display: inline-block;width: 44%; height: 14px;"></b>
                        </td>
                        <td style="width: 15%;">
                            *Suffix: <b style="border-bottom: 1px solid #000; display: inline-block;width: 39%; height: 14px;"></b>
                        </td>
                        <td style="width: 12%;">
                            *Lot:<b style="border-bottom: 1px solid #000; display: inline-block;width: 44%; height: 14px;"></b>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="height: 12px;">
                            
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <table cellpadding="0" cellspacing="0" style="width: 100%; font-size: 9pt;">
                                <tr>
                                    <td style="width: 50%;">
                                        *Property Address: <b style="border-bottom: 1px solid #000; display: inline-block;width: 68%; height: 12px;">{{ $street_address }}</b>
                                    </td>
                                    <td style="width: 24%">
                                        *City: <b style="border-bottom: 1px solid #000; display: inline-block;width: 68%; height: 12px;">{{ $city }}</b>
                                    </td>
                                    <td style="width: 14%">
                                        *State: <b style="border-bottom: 1px solid #000; display: inline-block;width:47%; height: 12px;">{{ $state }}</b>
                                    </td>
                                    <td style="width: 12%">
                                        *Zip: <b style="border-bottom: 1px solid #000; display: inline-block;width: 42%; height: 12px;">{{ $zip_code }}</b>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="height: 12px;">
                            
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <table cellpadding="0" cellspacing="0" style="width: 100%; font-size: 9pt;">
                                <tr>
                                    <td style="width: 45%;"> 
                                        *Contact Phone Numbers: <b style="border-bottom: 1px solid #000; display: inline-block;width: 46%; height: 12px;">{{ $phone_number }}</b>
                                    </td>
                                    <td style="width: 15%;">
                                        <b style="border-bottom: 1px solid #000; display: inline-block;width: 80%; height: 12px;"></b>
                                    </td>
                                    <td style="width: 40%;">
                                        E-mail: <b style="border-bottom: 1px solid #000; display: inline-block;width: 78%; height: 12px;">{{ $email }}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="font-size: 8pt;">
                                        * Required information
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        Please indicate the basis for your appeal (examples of supporting documentation are shown below):
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding-bottom: 0;padding-top: 0;">
                <table cellpadding="2" cellspacing="0" style="width: 100%; font-size: 10pt;border:1px solid #000;font-style: 9pt;">
                    <tr>
                        <td style="width: 25%; border-bottom: 1px solid #000; text-align: center;font-weight: bold;padding-bottom: 10px;">
                            [<img style="width: 10px;padding-top: 4px;" src="{{ public_path('/project/resources/assets/customer/images/tick-tax.png') }}" >] Estimated Market Value
                        </td>
                        <td style="border-left: 1px solid #000; width: 10%;border-bottom: 1px solid #000;vertical-align: top;padding-left: 8px;font-size: 9pt;">
                            Examples:
                        </td>
                        <td colspan="2" style="width: 75%;border-bottom: 1px solid #000;padding-top: 0;vertical-align: top;">
                            <table cellpadding="2" cellspacing="0" style="width: 100%; font-size: 9pt;">
                                <tr>
                                    <td style="vertical-align: top;">
                                        •recent written appraisal •recent settlement statement<br>
                                        •property insurance documents 
                                    </td>

                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #000;font-weight: bold;padding:10px;vertical-align: top;">
                            [<img style="width: 10px;padding-top: 4px;" src="{{ public_path('/project/resources/assets/customer/images/tick-tax.png') }}" >] Equalization
                        </td>
                        <td colspan="3" style="border-left: 1px solid #000;border-bottom: 1px solid #000;padding:8px;padding-top: 0;font-size:9pt;">
                            Example: a listing of properties that you consider to be comparable to your property.
                        </td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #000;font-weight: bold;padding:8px;vertical-align: top;">
                            [ ] Classification
                        </td>
                        <td colspan="3" style="border-left: 1px solid #000;border-bottom: 1px solid #000;padding:8px;">
                            <table  cellpadding="2" cellspacing="0" style="width: 100%; font-size: 9pt;">
                                <tr>
                                    <td colspan="3" style="font-size:9pt;">
                                        Indicate current use of the property, and date the use started: Date:______________
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="padding: 0;">
                                        <table cellpadding="0" cellspacing="0" style="width: 100%; font-size: 9pt;">
                                            <tr>
                                                <td style="width: 20%;">
                                                    [ ] Residential
                                                </td>
                                                <td style="width: 20%;">
                                                    [ ] Commercial
                                                </td>
                                                <td style="width: 60%;">
                                                    [ ] Mixed Use
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3"  style="text-decoration: underline; font-weight: bold;font-style: 9pt;">
                                        <i>Note:</i> If the appeal is based on Class 3 or Class 4 classification, do NOT use this form, call (202) 442-4332 for appeal information.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center;font-weight: bold;padding-bottom: : 10px;"> 
                            [ ] Property Damage or Condition
                        </td>
                        <td colspan="3" style="border-left: 1px solid #000;border-bottom: 1px solid #000;vertical-align: top;padding-left: 8px;font-size: 9pt;">
                            Examples: •cost estimates •damage claims.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding-top: 0;">
                <table cellpadding="2" cellspacing="0" style="width: 100%; font-size: 9pt;font-weight: bold;">
                    <tr>
                        <td style="width: 40%;color: #ff0000; border-left: 1px solid #000; border-bottom: 1px solid #000;"> 
                            TY {{ date('Y') }} TOTAL VALUE: $ {{ $total_assessment_value }}
                        </td>
                        <td style="width: 60%;color: #ff0000;border-left: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;">
                            REQUESTED TY {{ date('Y') }} TOTAL VALUE: $ {{ $final_Value }}   
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding-top: 0; text-align: justify;font-size: 9pt;color: #ff0000;">
                The DC Code 47-825.01a(d)(1) allows an owner to petition for an administrative review of the proposed assessment on or before {{ date('F d',strtotime($appeal_deadline_date)) }}<sup>st</sup>. We conduct both telephone and in-person interviews as well as written petitions. Telephone and in-person interviews are conducted by appointment only. If you fail to appear and have not notified us twenty-four (24) hours in advance of the appointed time, your review will be converted to a written review and only the information furnished with your original petition will be considered in the review.
            </td>
        </tr>
        <tr>
            <td style="padding-top: 0;font-size: 9pt;padding-bottom: 0;">
                <b>New Homeowner – </b> Do not use this form – New Owner Forms may be obtained from our Web site or from RPTA at the address and/or telephone number below.
            </td>
        </tr>
        <tr>
        	<td style="height: 10px;padding-top: 0;padding-bottom: 0;">
        		
        	</td>
        </tr>
        <tr>
            <td style="padding-top: 0;padding-bottom: 0;">
                <table cellpadding="0" cellspacing="0" style="width: 100%; font-size: 9pt;font-style: italic;font-weight: bold;">
                    <tr>
                        <td style="width:41%;">
                            Please select your preferred hearing method:
                        </td>
                        <td style="width: 11%;">
                            [  ] Written 
                        </td>
                        <td style="width: 35%;">
                            [  ] Telephone <b style="border-bottom: 1px solid #000; display: inline-block;width: 58%; height: 12px;"></b>
                        </td>
                        <td style="width: 13%;"> 
                            [  ] In-Person
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td style="padding-top: 0;padding-bottom: 0;">
                <table cellpadding="0" cellspacing="0" style="width: 100%; font-size: 9pt;font-style: italic;font-weight: bold;">
                    <tr>
                        <td style="width: 64%;">

                        </td>
                        <td >
                            (Contact Phone Number)
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td style="padding-top: 0; font-size: 9pt;padding-bottom: 0;">
                Will you be appealing any other properties? [ ] Yes [ ] No, If Yes, please complete an appeal application for each.
            </td>
        </tr>
         <tr>
        	<td style="height: 10px;padding-top: 0;padding-bottom: 0;">
        		
        	</td>
        </tr>
        <tr>
            <td style="padding-top: 0;">
                <table cellpadding="0" cellspacing="0" style="width: 100%; font-size: 9pt;">
                    <tr>
                        <td style="width: 45%; font-style:italic;">
                            Return completed form to:
                        </td>
                        <td style="width: 55%;">
                            Print Name: <b style="border-bottom: 1px solid #000; display: inline-block;width: 79%; height: 12px;"></b>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Office of Tax and Revenue
                        </td>
                        <td>
                            Owner/Agent* Signature: <b style="border-bottom: 1px solid #000; display: inline-block;width: 60%; height: 12px;"></b>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Real Property Tax Admin. - Appeals Section
                        </td>
                        <td>
                            Date (mm/dd/yyyy): <b style="border-bottom: 1px solid #000; display: inline-block;width: 68%; height: 12px;"></b>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            P.O. Box 71440
                        </td>
                        <td>
                            Daytime Phone: <b style="border-bottom: 1px solid #000; display: inline-block;width: 73%; height: 12px;"></b>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Washington, D.C. 20024
                        </td>
                        <td>
                            Evening Phone: <b style="border-bottom: 1px solid #000; display: inline-block;width: 73%; height: 12px;"></b>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            
                        </td>
                        <td style="font-size: 8pt;">
                            *If not the owner, a Letter of Agent Authorization must be attached.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding-top: 0;padding-bottom: 0;font-size: 9pt;">
                Assessment information about your property and comparable properties may be found on our Web site located at,
            </td>
        </tr>
        <tr>
            <td style="padding-top: 0;padding-bottom: 0;font-size: 9pt;">
                <a style="color: blue;text-decoration: underline;">http://www.cfo.dc.gov/otr/</a>, or you may call (202) 727-4TAX (4829) for assistance.
            </td>
        </tr>
        <tr>
        	<td style="height: 4px;padding-bottom: 0;padding-top: 0;">
        		
        	</td>
        </tr>
        <tr>
            <td style="padding-top: 0;padding-bottom: 0;font-size: 7pt;">
                Rev.{{ $date_of_value }}
            </td>
        </tr>
        <tr>
            <td style="padding-top: 0;padding-bottom: 0; font-size: 9pt;text-align: center;height: 2px;">
                <b style="border-top: 2px solid #000; display: inline-block;width: 100%;padding-bottom: 2px;"></b>
                1101 4<sup style="font-size: 8pt;">th</sup> Street, SW, Second Floor, Customer Service Center, Washington, D.C. 20024
            </td>
        </tr>
    </tbody>
</table>