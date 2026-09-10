<?php
session_start();
include_once "../language.php";

if (!isset($_SESSION["id"])) {
    header("Location: ../../");
    exit();
} else {
    include_once "partials/header.php"; ?>
<!--begin::Title-->
<title><?php echo $title_congrat ?> | CFAO Mobility Academy</title>
<!--end::Title-->

<!--begin::Body-->
<div class="content fs-6 d-flex flex-column flex-column-fluid" id="kt_content" data-select2-id="select2-data-kt_content">
  <!--begin::Post-->
  <div class="post fs-6 d-flex flex-column-fluid" id="kt_post" data-select2-id="select2-data-kt_post">
    <!--begin::Container-->
    <div class=" container-xxl " data-select2-id="select2-data-194-27hh">
      <!--begin::Main-->
      <div class="d-flex flex-column flex-root">
        <!--begin::Authentication - Password confirmation -->
        <div class="d-flex flex-column flex-column-fluid">
          <!--begin::Content-->
          <div class="d-flex flex-row-fluid flex-column flex-column-fluid text-center p-10 py-lg-20">
          <!--begin::Illustration-->
          <div class="d-flex flex-row-auto bgi-no-repeat bgi-position-x-center bgi-size-contain bgi-position-y-bottom min-h-150px min-h-lg-350px" style="background-image: url(../../public/images/success.png)">
          </div>
          <!--end::Illustration-->

            <!--begin::Logo-->
            <h1 class="fw-bold fs-2qx text-gray-800 mb-7" style="margin-top:30px"><?php echo $title_congrat ?></h1>
            <!--end::Logo-->

            <!--begin::Message-->
            <div class="fw-semibold fs-3 text-muted mb-15">
             <?php echo $congrat_text ?>.
            </div>
            <!--end::Message-->

            <!--begin::Action-->
            <div class="text-center">
              <a href="./dashboard" class="btn btn-primary btn-lg fw-bold"><?php echo $suivant ?></a>
            </div>
            <!--end::Action-->

            <!--begin::Action-->
            <!-- <div class="text-gray-700 fw-semibold fs-4 pt-7">Did’t receive an email?
              <a href="/craft/authentication/sign-in/password-reset.html" class="text-primary fw-bold">Try Again</a>
            </div> -->
            <!--end::Action-->

          </div>
          <!--end::Content-->

        </div>
        <!--end::Authentication - Password confirmation-->
      
      <!--end::Main-->
    </div>
  </div>
  <!--end::Container-->
</div>
<!--end::Post-->
</div>
<!--end::Body-->
<?php include_once "partials/footer.php"; ?>
<?php
} ?>
