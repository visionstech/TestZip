<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Welcome to Home Assessment wizard</title>
    </head>
    <body>
        <table width="650" style="font-family: Arial, Helvetica, sans-serif; font-size:14px;  border:5px solid #1570C3;" cellspacing="0" cellpadding="0" align="center" >
        <tr>
            <td height="20">&nbsp;</td>
        </tr>
        <tr>
            <td>
                @include('emails.header')

                @yield('content')  

                @include('emails.footer')
            </td>
        </tr>
        <tr>
            <td height="20">&nbsp;</td>
        </tr>
        </table>
    </body>
</html>
