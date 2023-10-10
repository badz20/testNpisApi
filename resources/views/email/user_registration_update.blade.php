<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>KEMASKINI AKAUN NATIONAL PROJECT INFORMATION SYSTEM</title>
</head>
<style>
    .img1{
    max-width: 20% !important;
    height: auto  !important;
    margin-right: 5%  !important;
    margin-left: -10%  !important;  
}
.img2{
    width: 15%  !important;
    height: auto  !important;
    margin-right: 8%  !important;
}
.img3{
    max-width: 10%  !important;
    height: auto  !important;
}
.font{
    font-size:25px  !important;
}
/* .font2{
    font-size:19px  !important;
    font-weight: bold  !important;
} */
.card-body{
    margin-top: 3%  !important;
    margin-left: 15%  !important;
    margin-right: 15%  !important;
    border: 2px solid  !important;
    margin-bottom: 5%  !important;
    padding-left: 5%  !important;
    padding-right: 5%  !important;
}
.logo{
    margin-top:5%  !important;
}
.btn_text{
    color:#ffffff;
    text-decoration:auto;
}
img.CToWUd.a6T {
        max-width: 10%;
        height: auto;
        margin-right: 2%;
    }
img.CToWUd {
    max-width: 8%;
    height: auto;
    margin-right: 18px;
}

</style>
<body>
    <div class="card-body">
        <div class="text-center logo" style="margin-left: 15%; margin-bottom:10px;">
            <img
              src="https://mcusercontent.com/5f9d4660d0bb1f2608f2b4e3e/images/fd7390a5-4fe2-e32d-8bf1-28990478a5b2.png"
              alt=""
              class="img1"
              style="width: 130px; margin-right: 4%"
            />
            <img
              src="https://mcusercontent.com/5f9d4660d0bb1f2608f2b4e3e/images/6e4d3fd1-82f5-ffff-36ce-8fb7f4370225.png"
              alt=""
              class="img2"
              style="width: 90px; margin-right: 6%"
            />
            <img
              src="https://mcusercontent.com/5f9d4660d0bb1f2608f2b4e3e/images/9f977a36-3317-3fd1-86bd-64e48a34b930.png"
              alt=""
              class="img3"
              style="width: 70px"
            />
          </div>
        <div class="mt-3">
            <div class="font"><b><u>KEMASKINI AKAUN NATIONAL PROJECT INFORMATION SYSTEM</u></b></div>
            <br><br>
            <p class="font2">Selamat Sejahtera <b> {{ $user['name'] }} ,</b></p>
        </div>
        <br><br>
        <div>
            <p class="font2">Perhatian! Adalah dimaklumkan bahawa akaun anda perlu dikemaskini. Sila kemaskini profil melalui pautan dibawah.</p>
            <p class="font2">Komen Pentadbir : {{ $user['comment'] }}</p>
        </div>
        <br>
        <div class="text-center">
            <button type="button" class="btn btn-dark"><a class="btn_text" href="{{ $user['Url'] }}">KEMASKINI BUTIRAN PENDAFTARAN</a></button>
        </div>
        <br><br>
        <p class="font2">atau jika anda mengalami masalah menekan butang di atas, boleh masukkan URL dibawah di web browser untuk pengemaskinian.</p>
        <p class="font2" style="word-wrap:break-word;"><a href=""> {{ $user['Url'] }}</a></p>
        <br>
        <p class="font2">Sekian, terima kasih <br> Pentadbir Sistem NPIS</p>
        <br><br>
        <p class="font2"><i>Emel ini dijana oleh sistem komputer. Sila jangan balas emel ini.</i></p>
    </div>
    
</body>
</html>