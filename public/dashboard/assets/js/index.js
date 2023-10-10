let round = document.querySelector(".round");
let side_bar_Container = document.querySelector(".side_bar_Container");

let index = document.querySelector("#index");
let eye_icon = document.querySelector(".eye_icon");
let password_eye_field = document.querySelector(".password_eye_field");
let login = document.querySelector("#login");
let interface_tab_content = document.querySelectorAll(".interface_tab_content");
let r_input = document.querySelectorAll(".r_container input");
let input_file = document.querySelector("#Dokumen_Sokongan");

let input_file_btn = document.querySelector(".form_input_file .select_file");
let userlist_tab_btn = document.querySelectorAll(
  ".userlist_tab_btn_container button"
);
let userlist_tab_content = document.querySelectorAll(".userlist_tab_content ");
let Selenggara_portal = document.querySelector("#Selenggara_portal"); console.log(Selenggara_portal);
let overlay_sidebar = document.querySelector("body.overlay_sidebar"); console.log(overlay_sidebar)

// sidebar overlay

if (overlay_sidebar) {
  let toggler_img = document.querySelector(".toggler_menu .round");
  let toggler_round = document.querySelector(".toggler_menu img");

  let toggler = document.querySelectorAll(".toggler ,.toggler_menu");
  console.log(toggler);
  toggler.forEach((toggler) => {
    toggler.addEventListener("click", () => {
      console.log(toggler);
      side_bar_Container.classList.toggle("toggle_sidebar");
      toggler_img.classList.toggle("d-none");
      toggler_round.classList.toggle("d-none");
    });
  });
}

// ---------------------Selenggara_portal-------------------------------------------------
if (Selenggara_portal) {
  let pop_content_all = document.querySelectorAll(".pop_content");
  let pop_btn_all = document.querySelectorAll(".pop_btn");
  let upload_img_all = document.querySelectorAll(".upload_img ,.upload_logo");
  // all popup functionality
  pop_btn_all.forEach((pba, i) => {
    pba.addEventListener("mouseover", (e) => {
      e.preventDefault();
      pop_content_all[i].classList.toggle("d-none");
    });
    pba.addEventListener("mouseout", (e) => {
      e.preventDefault();
      pop_content_all[i].classList.toggle("d-none");
    });
  });
  // all upload image video functionality
  upload_img_all.forEach((upload_img) => {
    upload_img.addEventListener("click", () => {
      upload_img.previousElementSibling.click();
    });
  });
}
// ----------------------------------------user_profile-------------------------------------------
let user_profile = document.querySelector("#user_profile");
console.log(user_profile)
if (user_profile) {
  userlist_tab_btn.forEach((utb, i) => {
    utb.addEventListener("click", () => {
      userlist_tab_content.forEach((utc) => {
        utc.classList.add("d-none");
      });
      userlist_tab_btn.forEach((utb) => {
        utb.classList.remove("active");
      });
      utb.classList.add("active");
      userlist_tab_content[i].classList.remove("d-none");
    });
  });
  userlist_tab_btn[0].click();
}
// ----------------------------------------login-------------------------------------------

if (login) {
  let pop_content = document.querySelector(".pop_content");
  let pop_btn = document.querySelector(".pop_btn button");
  pop_btn.addEventListener("click", () => {
    pop_content.classList.toggle("d-none");
  });
  eye_icon.addEventListener("click", (e) => {
    console.log(password_eye_field);
    e.preventDefault();
    password_eye_field.type == "password"
      ? (password_eye_field.type = "text")
      : (password_eye_field.type = "password");
  });
  document.querySelector(".masuk_submit").addEventListener("click", (e) => {
    e.preventDefault();
  });
  r_input.forEach((inp, i) => {
    inp.addEventListener("click", () => {
      interface_tab_content.forEach((itc) => {
        itc.classList.add("d-none");
      });
      if (inp.checked == true) {
        interface_tab_content[i].classList.remove("d-none");
      }
    });
  });
  r_input[1].click();

  // input_file
  input_file_btn.addEventListener("click", (e) => {
    e.preventDefault();
    console.log(input_file);
    input_file.click();
  });

  // -------------------------
  input_file.addEventListener("change", () => {
    var files = input_file.files;
    if (FileReader && files && files.length) {
      var fr = new FileReader();
      // console.log(fr);

      fr.onload = function () {
        console.log(fr.result, input_file.files);
      };

      fr.readAsDataURL(files[0]);
    }
  });
  let form_btn = document.querySelectorAll(".form_btn_container button");
  form_btn.forEach((fb) => {
    fb.addEventListener("click", (e) => {
      e.preventDefault();
    });
  });
}
// ----------------------------------------index-------------------------------------------

if (index) {
  let accordial_all_list = document.querySelectorAll(
    ".accordian_single_list, .NPIS_logo_right_content"
  );
  // --------------------------------------------------------------------------------------------
  let Mainbody_conatiner = document.querySelector(".Mainbody_conatiner");
  round.addEventListener("click", () => {
    side_bar_Container.classList.remove("show");
    Mainbody_conatiner.classList.add("active");
    accordial_all_list.forEach((asl) => {
      asl.classList.add("active");
    });
  });
  // --------------------------------------------------------------------------------------------
  document.querySelector(".NPIS_logo").addEventListener("click", () => {
    side_bar_Container.classList.add("show");
    Mainbody_conatiner.classList.remove("active");
    accordial_all_list.forEach((asl) => {
      asl.classList.remove("active");
    });
  });
  // --------------------------------------------------------------------------------------------

  let accordian_single_list = document.querySelectorAll(
    ".accordian_single_list"
  );
  let d_arrow = document.querySelectorAll(".d_arrow");

  accordian_single_list.forEach((asl) => {
    asl.addEventListener("click", () => {
      d_arrow.forEach((darr) => {
        darr.classList.remove("active");
      });
      // let accordian_content = asl.closest(".accordian_content ");
      // console.log(accordian_content);
      let arrow = asl.querySelector(".d_arrow");
      let Accordian_link = asl.querySelector(".Accordian_link");
      if (Accordian_link.classList.contains("collapsed")) {
        arrow.classList.add("active");
      } else {
        arrow.classList.remove("active");
      }
    });
  });
  Highcharts.chart("bar_chart", {
    chart: {
      type: "column",
    },
    title: {
      text: "STATISTIK PELAWAT",
      align: "left",
      floating: true,
      verticalAlign: "top",
      style: {
        fontSize: "16px",
        fontFamily: "helvetica, arial, sans-serif",
        textShadow: false,
        fontWeight: "bold",
      },
    },
    subtitle: {
      text: "",
    },
    xAxis: {
      categories: ["Jan", "feb", "Mar", "Apr", "May", "Jun", "July"],
      crosshair: false,
    },
    yAxis: {
      min: 0,
      tickInterval: 20,
      title: {
        text: "",
      },
      lineWidth: 0,
      gridLineWidth: 0,
      // labels: {
      //   enabled: false,
      // },
    },

    plotOptions: {
      series: {
        animation: false,
      },
      column: {
        pointPadding: 0.2,
        borderWidth: 0,
      },
    },
    credits: {
      enabled: false,
    },

    legend: {
      // margin: 30,
      align: "right",
      verticalAlign: "top",
      symbolHeight: 10,
      symbolWidth: 10,
      symbolRadius: 50,
    },
    dataLabels: {
      style: {
        fontSize: "12px",
        fontfamily: "Poppins_regular",
        textShadow: false,
        fontWeight: "bold",
      },
    },
    series: [
      {
        name: "Agensi",
        data: [30, 25, 30, 46, 67, 28, 30],
        color: "#b5a8ff",
      },
      {
        name: "JPS",
        data: [20, 14, 60, 30, 25, 40, 28],
        color: "#78f1aa",
      },
    ],
  });
}

