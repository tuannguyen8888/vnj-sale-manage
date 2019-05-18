@extends('crudbooster::admin_template')
@section('content')

<!--bỏ $index_button ||-->
            @if($button_show_data || $button_reload_data || $button_new_data || $button_delete_data ||  $columns)
            <div id='box-actionmenu' class='box'>
              <div class='box-body'>
                 @include("crudbooster::default.actionmenu")
              </div>
            </div>
            @endif


            @if(Request::get('file') && Request::get('import'))

            <ul class='nav nav-tabs'>
                    <li style="background:#eeeeee"><a style="color:#111" onclick="if(confirm('Are you sure want to leave ?')) location.href='{{ CRUDBooster::mainpath("import-data") }}'" href='javascript:;'><i class='fa fa-download'></i> Upload a File &raquo;</a></li>
                    <li style="background:#eeeeee" ><a style="color:#111" href='#'><i class='fa fa-cogs'></i> Adjustment &raquo;</a></li>
                    <li style="background:#ffffff"  class='active'><a style="color:#111" href='#'><i class='fa fa-cloud-download'></i> Importing &raquo;</a></li>
            </ul>

            <!-- Box -->
            <div id='box_main' class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Importing</h3>
                    <div class="box-tools">                      
                    </div>
                </div>
                    
                <div class="box-body">
                    
                    <p style='font-weight: bold' id='status-import'><i class='fa fa-spin fa-spinner'></i> Vui lòng chờ trong khi xữ lý dữ liệu ...</p>
                    <div class="progress">
                      <div id='progress-import' class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="40" 
                      aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                        <span class="sr-only">40% Complete (success)</span>
                      </div>
                    </div>                    

                    @push('bottom')
                    <script type="text/javascript">
                      $(function() {
                        var total = {{ intval(Session::get('total_data_import')) }};
                        
                        var int_prog = setInterval(function() {

                          $.post("{{ CRUDBooster::mainpath('do-import-chunk?file='.Request::get('file')) }}",{resume:1},function(resp) {                                       
                              console.log(resp.progress);
                              $('#progress-import').css('width',resp.progress+'%');
                              $('#status-import').html("<i class='fa fa-spin fa-spinner'></i> Please wait importing... ("+resp.progress+"%)");
                              $('#progress-import').attr('aria-valuenow',resp.progress);
                              if(resp.progress >= 100) {
                                $('#status-import').addClass('text-success').html("<i class='fa fa-check-square-o'></i> Import dữ liệu thành công !");
                                clearInterval(int_prog);
                              }
                          })
                          
                          
                        },2500);

                        $.post("{{ CRUDBooster::mainpath('do-import-chunk').'?file='.Request::get('file') }}",function(resp) {
                            if(resp.status==true) {
                              $('#progress-import').css('width','100%');
                              $('#progress-import').attr('aria-valuenow',100);
                              $('#status-import').addClass('text-success').html("<i class='fa fa-check-square-o'></i> Import dữ liệu thành công !");
                              clearInterval(int_prog);
                              $('#upload-footer').show();
                              console.log('Import Success');
                            } else { 
                                $('#progress-import').css('width','100%');
                                $('#progress-import').attr('aria-valuenow',100);
                                //$('#status-import').addClass('text-success').html("<i class='fa fa-check-square-o'></i>".resp.last_error);
                                clearInterval(int_prog);
                                $("#status-import").html(resp.last_error);
                                $('#upload-footer').show();
                                swal("Thông Báo", resp.last_error, "error");
                                return false;
                            }
                        })

                      })

                    </script>
                    @endpush

                </div><!-- /.box-body -->
        
                <div class="box-footer" id='upload-footer' style="display:none">  
                  <div class='pull-right'>                            
                      <a href='{{ CRUDBooster::mainpath("import-data") }}' class='btn btn-default'><i class='fa fa-upload'></i> Upload Other File</a> 
<!--                      Viet add new-->
                    <?php 
                        $return_url = Session::get('return_url') ? Session::get('return_url') : CRUDBooster::mainpath();
                    ?>
                      <a href='{{$return_url}}' class='btn btn-success'>Kết thúc</a>                                
                  </div>
                </div><!-- /.box-footer-->
                
            </div><!-- /.box -->
            @endif

            @if(Request::get('file') && !Request::get('import'))

            <ul class='nav nav-tabs'>
                    <li style="background:#eeeeee"><a style="color:#111" onclick="if(confirm('Are you sure want to leave ?')) location.href='{{ CRUDBooster::mainpath("import-data") }}'" href='javascript:;'><i class='fa fa-download'></i> Upload a File &raquo;</a></li>
                    <li style="background:#ffffff"  class='active'><a style="color:#111" href='#'><i class='fa fa-cogs'></i> Adjustment &raquo;</a></li>
                    <li style="background:#eeeeee"><a style="color:#111" href='#'><i class='fa fa-cloud-download'></i> Importing &raquo;</a></li>
            </ul>

            <!-- Box -->
            <div id='box_main' class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Adjustment</h3>
                    <div class="box-tools">

                    </div>
                </div>
        
                  <?php           
                    if($data_sub_module) {
                      $action_path = Route($data_sub_module->controller."GetIndex");
                    }else{
                      $action_path = CRUDBooster::mainpath();
                    }            

                    $action = $action_path."/done-import?file=".Request::get('file').'&import=1';
                  ?>

                <form method='post' id="form" enctype="multipart/form-data" action='{{$action}}'>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="box-body table-responsive no-padding">
                              <div class='callout callout-info'>
                                  Vui lòng kiểm tra lại dữ liệu trước khi import.
                              </div>
                              <table id="review-data" class='table table-striped table-bordered table-hover'>
                                  <thead>
                                      <tr class='success'>
                                          @foreach($data_import_column as $k=>$column)
                                            <?php
                                            $help = ''; 
                                            if($column == 'id' || $column == 'created_at' || $column == 'updated_at' || $column == 'deleted_at') continue;
                                            if(substr($column,0,3) == 'id_') {
                                              $relational_table = substr($column, 3);
                                              $help = "<a href='#' title='This is foreign key, so the System will be inserting new data to table `$relational_table` if doesn`t exists'><strong>(?)</strong></a>";
                                            }
                                            ?>
                                            <th data-no-column='{{$k}}'>{{ $column }} {!! $help !!}</th>
                                          @endforeach
                                      </tr>                                      
                                  </thead>
                                  <tbody>
                                       @foreach($table_rows as $i=>$row)
                                        <tr>
                                        @foreach($table_columns as $k=>$column)
                                            <?php if($column == 'id' || $column == 'created_at' || $column == 'updated_at' || $column == 'deleted_at') continue;?>
                                            <td data-no-column='{{$k}}'>
                                                {{$row->$column}}
                                            </td>
                                        @endforeach
                                        </tr>
                                        @endforeach
                                  </tbody>
                              </table>


                        </div><!-- /.box-body -->

                        @push('bottom')

                        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">
                        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.2.7/css/select.dataTables.min.css">

                        <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
                        <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
                        <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
                        <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
                        <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>
                        <script type="text/javascript">
                          $(function(){    
//                              var total_selected_column = 0;                      
//                              setInterval(function() {
//                                  total_selected_column = 0;
//                                  $('.select_column').each(function() {
//                                      var n = $(this).val();
//                                      if(n) total_selected_column = total_selected_column + 1;
//                                  })
//                              },200);
                              $('#review-data').dataTable({
                                  iDisplayLength: 50,
                                  // pageLength: 50,
                                  lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, 'All'] ],
                                  language: {
                                      processing: '<div class="loading"></div>'
                                  },
                                  paging: true,
                                  lengthChange: true,
                                  searching: false,
                                  ordering: false,
                                  info: false,
                                  autoWidth: true,
                                  scrollX: true,
                                  processing: true,
                                  serverSide: true,
                                  stateSave: true,
                                  scrollY: '50vh',
                                  scrollCollapse: true,
                                  columns: [
                                      { "data": "ma_khach_hang", "orderable": false },
                                      { "data": "ten_khach_hang", "orderable": false },
                                      { "data": "ten_nguoi_ban", "orderable": false },
                                      { "data": "ngay_cong_no", "orderable": false },
                                      { "data": "tuoi_ban", "orderable": false },
                                      { "data": "q10_ghi_co", "orderable": false },
                                      { "data": "tien_cong_ghi_co", "orderable": false },
                                      { "data": "q10_ghi_no", "orderable": false },
                                      { "data": "tien_cong_ghi_no", "orderable": false }
                                  ],
                                  buttons: [ 'pageLength' ],
                                  // order: [[ 5, "desc" ]],
                                  ajax: {
                                      type: "POST",
                                      contentType: "application/json",
                                      url: '{{CRUDBooster::mainpath('load-data-import')}}',
                                      data: function (data) {
                                          console.log('datadatadatadata = ', data);
                                          return JSON.stringify({
                                              dataTableParameters: data
                                          });
                                      }
                                  },
                                  dom: 'Bfrtip',
                              });
                          })
                          function check_selected_column() {
                               return true;
//                              var total_selected_column = 0;
//                              $('.select_column').each(function() {
//                                  var n = $(this).val();
//                                  if(n) total_selected_column = total_selected_column + 1;
//                              })
//                              if(total_selected_column == 0) {
//                                swal("Oops...", "Please at least 1 column that should adjusted...", "error");
//                                return false;
//                              }else{
//                                return true;
//                              }
                          }
                        </script>
                        @endpush
                
                        <div class="box-footer">  
                          <div class='pull-right'>             
                              <?php 
                                $return_url = Session::get('return_url') ? Session::get('return_url') : CRUDBooster::mainpath("import-data");
                                ?>
                              <a onclick="if(confirm('Are you sure want to leave ?')) location.href='{{ $return_url }}'" href='javascript:;' class='btn btn-default'>Cancel</a>  
                              <input type='submit' class='btn btn-primary' name='submit' onclick='return check_selected_column()' value='Import Data'/>   
                          </div>
                        </div><!-- /.box-footer-->
                </form>
            </div><!-- /.box -->


            @endif

            @if(!Request::get('file'))
            <ul class='nav nav-tabs'>
                    <li style="background:#ffffff" class='active'><a style="color:#111" onclick="if(confirm('Are you sure want to leave ?')) location.href='{{ CRUDBooster::mainpath("import-data") }}'" href='javascript:;'><i class='fa fa-download'></i> Upload a File &raquo;</a></li>
                    <li style="background:#eeeeee"><a style="color:#111" href='#'><i class='fa fa-cogs'></i> Adjustment &raquo;</a></li>
                    <li style="background:#eeeeee"><a style="color:#111" href='#'><i class='fa fa-cloud-download'></i> Importing &raquo;</a></li>
            </ul>

            <!-- Box -->
            <div id='box_main' class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Upload a File</h3>
                    <div class="box-tools">
                       <a class="btn btn-danger" target="_blank" href="{{asset('uploads/templates/Import_cong_no_template.xls')}}"><i class="fa fa-download" aria-hidden="true"></i> Download template import công nợ</a>
                    </div>
                </div>
        
                  <?php           
                    if($data_sub_module) {
                      $action_path = Route($data_sub_module->controller."GetIndex");
                    }else{
                      $action_path = CRUDBooster::mainpath();
                    }            

                    $action = $action_path."/do-upload-import-data";
                  ?>

                <form method='post' id="form" enctype="multipart/form-data" action='{{$action}}'>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">             
                        <div class="box-body">

<!--                            <div class='callout callout-success'>
                                  <h4>Welcome to Data Importer Tool</h4>
                                  Before doing upload a file, its better to read this bellow instructions : <br/>
                                  * File format should be : xls or xlsx or csv<br/>
                                  * If you have a big file data, we can't guarantee. So, please split those files into some parts of file (at least max 5 MB).<br/>
                                  * This tool is generate data automatically so, be carefull about your table xls structure. Please make sure correctly the table structure.<br/>
                                  * Table structure : Line 1 is heading column , and next is the data.  (For example, you can export any module you wish to XLS format)                                                                
                              </div>-->
                            <div class='callout callout-success'>
                              <h4>Vui lòng chọn file Upload</h4>
                                * File phải ở định dạng : xls,xlsx<br/>
                                * Dòng đầu tiên phải là tên các cột trong bảng.<br/>
                                * Để đảm bảo dữ liệu được import đầy đủ: Dung lượng File tối đa là 4MB, nếu file lớn hơn 4MB, hãy chia nhỏ nó.<br/>
                            </div>
                            <div class='form-group'>
                                <label>File import</label>
                                <input type='file' name='userfile' id='userfile' class='form-control' accept=".xls,.xlsx" required />
                                <div class='help-block'>File type supported only : XLS, XLSX</div>
                            </div>
                        </div><!-- /.box-body -->

                        @push('bottom')
                            <style>
                                .load .loading{
                                    display: none;
                                }
                            </style>
                            <script type="text/javascript">
                                function showLoading() {
                                    if($('#userfile').val()) {
                                        $('.load .loading').show();
                                        $('#submit').addClass('disabled');
                                    }
                                    return true;
                                }
                            </script>
                        @endpush
                        <div class="box-footer">  
                          <div class='pull-right'>                            
                              <?php 
                                $return_url = Session::get('return_url') ? Session::get('return_url') : CRUDBooster::mainpath();
                               ?>
                              <a href='{{ $return_url }}' class='btn btn-default'>Cancel</a>  
                              <input type='submit' class='btn btn-primary' name='submit' id='submit' value='Upload'/>
                          </div>
                        </div><!-- /.box-footer-->
                </form>
            </div><!-- /.box -->

            <div class="load">
                <div class="loading"></div>
            </div>

             @endif
        </div><!-- /.col -->


    </div><!-- /.row -->

@endsection