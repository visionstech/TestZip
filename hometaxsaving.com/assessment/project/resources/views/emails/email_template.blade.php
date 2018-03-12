@extends('emails.template') 
@section('content')

    <tr>
        <td style="border-top:2px solid #d8d8d8;"  height="10">&nbsp;</td>
    </tr>
    <tr>
        <td style="font-size:16px; color:#333;" height="25">Hello {{ $user_name }},<br>&nbsp;</td>
    </tr>
    <tr>
        <td>
            <strong style="color:#333; font-size:16px;">Username: {{ $username }}</strong>
        </td> 
    </tr>
    <tr>
        <td height="15">&nbsp;</td>
    </tr>
    <tr>
        <td style="color:#333; font-size:16px;">
            <?php echo htmlspecialchars_decode($content); ?>
        </td>
    </tr>
    <tr>
        <td height="15">&nbsp;</td>
    </tr>
    <tr>
        <td style="font-size:16px; font-family:Arial, Helvetica, sans-serif; color:#333;" valign="middle" >
            Regards<br />
            Tax Assessment Team 
        </td>
    </tr>
    <tr>
        <td height="35">&nbsp;</td>
    </tr>
    <tr>
        <td style="border-top:2px solid #d8d8d8;"  height="10">&nbsp;</td>
    </tr>

@stop
