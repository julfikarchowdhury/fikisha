<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ @settings()->title }}</title>
    <style>
            body{
                background-color: aliceblue;
                color:#8094ae;
            }
            a{
                color:#7e0095;
            }
            ul {
                display: inline-block;
                text-align: center;
                overflow: hidden;
                padding-left: 0px;
                margin-bottom: 0px!important;
            }
            ul li{
                float: left;
                list-style: none;
                padding:5px;
            }
            ul li a{
                cursor: pointer;
                display: block;
            }
    </style>
</head>
    <body style="margin: 10;">
        <table style="width:100%;height:50px;max-width:650px;margin: auto;">
                <tr>
                    <td style="text-align: center;padding:30px 10px">
                        <a href="{{ url('/') }}"><img src="{{ static_asset(@settings()->rxlogo->original) }}" style="height: 50px;"/></a>
                    </td>
                </tr>
        </table>
        <table style="width:100%;height:50px;max-width:650px;margin: auto;background-color: white;">
                <tr>
                    <td style="padding:30px;line-height: 1.5;" colspan="2">
                        <p>Hi <b style="font-style: italic;">{{ @$data['merchant_user']->name }}</b>,</p>
                        <p>Thank you for your interest in becoming an merchant with {{ @settings()->name }}.</p>
                        <p>Your login is <b style="font-style: italic;" >{{ @$data['merchant_user']->email }}</b></p>
                        <div style="text-align: center;">
                            Your OTP Code: <b>{{ @$data['otp'] }}</b><br><br> 
                         </div>
                        <div style="text-align: center;">
                            Please verify your email with bellow link:<br><br>
                            <a href="{{ route('email.verify', $data['merchant_user']->email_verify_token) }}" style="background-color: #4f2b55;color: white; margin-left:10px;padding: 7px 15px; border: none;text-decoration: none;border-radius: 3px;">Email Address Verify</a>
                         </div>
                        <p>
                            Please <a target="_blank" href="{{ url('login') }}">login</a>
                        </p>
                        <p>Hope you'll enjoy the experience, we're here if you have any questions, drop us a line at <a href="mailto:{{ @settings()->email }}" >{{ @settings()->email }}</a> or {{ @settings()->phone }} anytime.</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <p style="text-align: center;text-transform:uppercase">
                            Get download our android or i application
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%; text-align: right;padding: 10px 10px 30px 10px;">
                       <a href="#"> <img src="{{ static_asset('backend/images/social-media/play_butttom.png') }}" style="width:200px;"/></a>
                    </td>
                    <td style="width: 50%;text-align: left;padding: 10px 10px 30px 10px;">
                        <a href="#"><img src="{{ static_asset('backend/images/social-media/istore.png') }}" style="width:200px" /></a>
                    </td>
                </tr>
        </table>
        <table style="width:100%;height:50px;max-width:650px;margin: auto; ">
                <tr>
                    <td style="text-align: center;">
                        <ul>
                            <li><a><img src="{{ static_asset('backend/images/social-media/brand-b.png') }}" style="width: 30px;" /></a></li>
                            <li><a><img src="{{ static_asset('backend/images/social-media/brand-c.png') }}" style="width: 30px;" /></a></li>
                            <li><a><img src="{{ static_asset('backend/images/social-media/brand-d.png') }}" style="width: 30px;" /></a></li>
                            <li><a><img src="{{ static_asset('backend/images/social-media/brand-e.png') }}" style="width: 30px;" /></a></li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0px 30px;text-align: center;">
                        <p style="font-size: 13px;">
                            {{ @settings()->copyright }}
                        </p>
                    </td>
                </tr>
        </table>
    </body>
</html>
