<footer class="main-footer">
  <div class="pull-right hidden-xs">
    Arise, awake, and stop not till the goal is reached - Swami Vivekananda
  </div>
  <strong>Copyright &copy; <?php echo date("Y"); ?> <a href="#">Pazhamuthir POS</a>.</strong> All rights reserved.
</footer>
</div>

<!-- jQuery UI 1.11.4 -->
<!-- <script src="assets/bower_components/jquery-ui/jquery-ui.min.js"></script> -->
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  // $.widget.bridge('uibutton', $.ui.button);
</script>

<!-- jQuery 3 -->
<script src="assets/bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="assets/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- DataTables Buttons-->
<script src="assets/bower_components/datatables.button/dataTables.buttons.min.js"></script>
<script src="assets/bower_components/datatables.button/jszip.min.js"></script>
<script src="assets/bower_components/datatables.button/pdfmake.min.js"></script>
<script src="assets/bower_components/datatables.button/vfs_fonts.js"></script>
<script src="assets/bower_components/datatables.button/buttons.html5.min.js"></script>
<script src="assets/bower_components/datatables.button/buttons.print.min.js"></script>
<script src="assets/bower_components/datepicker/datepicker.min.js"></script>
<!-- Slimscroll -->
<script src="assets/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="assets/bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="assets/dist/js/adminlte.min.js"></script>
<!-- Select2 -->
<script src="assets/bower_components/select2/dist/js/select2.full.min.js"></script>

<!-- sweet-alert -->
<script src="assets/plugins/sweet-alert/js/sweet-alert.min.js"></script>
<!-- toastr -->
<script src="assets/plugins/toastr/toastr.min.js"></script>

<script language="javascript" type="text/javascript" src="includes/common/valid.js"></script>
<script language="javascript" type="text/javascript" src="includes/common/mydeveloper.js"></script>

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script> -->


<script>
var url = window.location;
// for sidebar menu entirely but not cover treeview
$('ul.sidebar-menu a').filter(function() {
    return this.href == url;
}).parent().addClass('active');

// for treeview
$('ul.treeview-menu a').filter(function() {
    return this.href == url;
}).parentsUntil(".sidebar-menu > .treeview-menu").addClass('active');

$(function() {
    $('.select2').select2({
    placeholder: 'Select the Options',
    allowClear: true
  })
});

$('.UPPERCASE').keyup(function(){
    return this.value = this.value.toUpperCase();
});

$('.NUMBER_ONLY').keyup(function(){
    this.value = this.value.replace(/[^0-9\.]/g,'');
    // this.value == 
});
var today = new Date();
$('.date_picker').datepicker({
      format: 'dd-mm-yyyy',
      autoclose: true,
      todayHighlight: true,
      endDate: "today",
      maxDate: today
});

</script>

<script>
$(function () {
    <?php 
    if(isset($_SESSION['msg']) && $_SESSION['msg']!=""){
      echo "toastr.success('". $_SESSION['msg'] ."');";
      $_SESSION['msg'] = ""; 
    }
    if(isset($_SESSION['msg_err']) && $_SESSION['msg_err']!=""){
      echo "toastr.error('Error: ". $_SESSION['msg_err'] ."');";
      $_SESSION['msg_err'] = "";
    }
    ?>

    toastr.options = {
      "closeButton": true,
      "newestOnTop": false,
      "progressBar": true,
      "positionClass": "toast-top-right",
      "preventDuplicates": false,
      "onclick": null,
      "showDuration": "300",
      "hideDuration": "1000",
      "timeOut": "2000",
      "extendedTimeOut": "1000",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut"
    }
});
</script>
</body>

</html>