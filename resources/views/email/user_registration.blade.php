<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="/assets/img/" />
    <title>Document</title>
  </head>
  <style>
    /* .img1 {
      max-width: 20%;
      height: auto;
      margin-right: 3%;
      margin-left: 17%;
    }
    .img2 {
      width: 15%;
      height: auto;
      margin-right: 5%;
    }
    .img3 {
      max-width: 10%;
      height: 110px;
    } */

    .font {
      font-size: 25px;
      margin-top: 3%;
    }
    .font2 {
      font-size: 19px;
      font-weight: bold;
    }

    .card-body {
      margin-top: 3%;
      margin-left: 15%;
      margin-right: 15%;
      border: 2px solid;
      margin-bottom: 5%;
      padding-left: 5%;
      padding-right: 5%;
    }

    .logo {
      margin-top: 5%;
    }

    /* table td , th{
    width: 300px;
} */

    .td1 {
      width: 300px;
    }
    .td2 {
      width: 100px;
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
      <div class="mt-5">
        <div class="font">
          <b><u>PENDAFTARAN AKAUN NATIONAL PROJECT INFORMATION SYSTEM</u></b>
        </div>
        <br /><br />
        <p class="font2">Selamat Sejahtera <b>{{ $user['name'] }} ,</b></p>
      </div>
      <br /><br />
      <div>
        <p class="font2">
          Adalah dimaklumkan bahawa pendaftaran akaun NPIS anda telah berjaya
          didaftarkan di dalam sistem NPIS
        </p>
        <p class="font2">
          Dibawah adalah maklumat dari permintaan pendaftaran akaun NPIS:
        </p>
      </div>
      <br /><br />
      <table>
        <tr>
          <td class="td1">
            <p class="font2">Nama Pengguna</p>
            <!-- <p class="font2">Kata Laluan</p> -->
            <p class="font2">Kementerian Pengguna</p>
            <p class="font2">Jabatan</p>
            <p class="font2">Bahagian</p>
            <p class="font2">Tarikh Pendaftaran</p>
          </td>
          <td class="td2">
            <p class="font2">:</p>
            <!-- <p class="font2">:</p> -->
            <p class="font2">:</p>
            <p class="font2">:</p>
            <p class="font2">:</p>
            <p class="font2">:</p>
          </td>
          <td>
            <p class="font2"><b>{{ $user['name'] }}</b></p>
            <!-- <p class="font2"><b>{{ $password }}</b></p> -->
            <p class="font2">
              <b>{{ $user['kementerian']['nama_kementerian'] }}</b>
            </p>
            <p class="font2"><b>{{ $user['jabatan']['nama_jabatan'] }}</b></p>
            <p class="font2"><b>{{ $user['bahagian']['nama_bahagian'] }}</b></p>
            <p class="font2"><b>{{ $user['dibuat_pada'] }}</b></p>
          </td>
        </tr>
      </table>
      <br /><br />
      <p class="font2">
        <b>
          Untuk makluman, pengguna perlu menunggu pengaktifan emel daripada
          pentadbir sistem NPIS untuk mengakses sistem NPIS</b
        >
      </p>
      <p class="font2"><a href="">https://npis.water.gov.my/</a></p>
      <br />
      <p class="font2">
        Sekian, terima kasih <br />
        Pentadbir Sistem NPIS
      </p>
      <br /><br />
      <p class="font2">
        <i>Emel ini dijana oleh sistem komputer. Sila jangan balas emel ini.</i>
      </p>
    </div>
  </body>
</html>
