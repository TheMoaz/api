<div style="background-color:#f8f8f8;color:#666;padding:30px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:large;">
    <div style="min-width:320px;max-width:80%;margin:0 auto;">
        <div style="margin-bottom:50px">
            <img src="http://skillbazaar.co/images/logo.png">
        </div>
        <p>Hi {{$name or 'Mr XYZ'}},</p>
        <p>We have reset your account password on SkillBazaar. Please use the following code to sign in.</p>
        <pre style="display:block;border-radius:4px;text-decoration:none;text-align:center;color:#fff;background-color:#d9534f;border-color:#d43f3a;padding:15px;width:25%;min-width:100px;margin:0 auto;font-size:21px;">{{$code or '123456'}}</pre>
        <p>Thanks,</p>
        <p>The SkillBazaar Team</p>
        <div style="text-align:center;color:#999;margin-top:50px;">
            <p>Sent with &#9829; from SkillBazaar HQ | <a href="https://www.facebook.com/forimazdoori" target="_blank"><img src="http://skillbazaar.co/images/facebook.png" style="vertical-align:middle;width:24px;"></a><a href="https://twitter.com/forimazdoori" target="_blank"><img src="http://skillbazaar.co/images/twitter.png" style="vertical-align:middle;width:24px;"></a></p>
        </div>
    </div>
</div>