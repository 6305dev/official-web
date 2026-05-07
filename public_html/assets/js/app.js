$(document).ready(function () {
  $(".owl-carousel").owlCarousel({
    items: 3,
    loop: true,
    center: true,
    nav: true,
    dots: true,
    autoplay: true,
    autoplayTimeout: 4000,
    autoplayHoverPause: true,
    smartSpeed: 800,
    navText: [
      '<i class="bi caret-left"></i>',
      '<i class="bi caret-right"></i>',
    ],
    responsive: {
      0: {
        items: 1,
      },
      768: {
        items: 2,
      },
      992: {
        items: 3,
      },
    },
    animateOut: "fadeOut",
    animateIn: "fadeIn",
  });

  // Swal.fire({
  //   title: "Pemberitahuan!",
  //   text: "Selamat datang di website Dinas Kependudukan dan Pencatatan Sipil Kabupaten Tapin. Mohon maaf saat ini kami sedang melakukan pembaharuan fitur, sehingga ada beberapa halaman tidak bisa di akses untuk sementara waktu. Terima kasih atas perhatiannya.",
  //   icon: "info",
  //   confirmButtonText: "OK",
  // });
});
