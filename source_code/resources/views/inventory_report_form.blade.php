<!-- First you need to extend the CB layout -->
@extends('crudbooster::admin_template')
@section('content')
	<!-- Your custom  HTML goes here -->
	<!-- Your html goes here -->
	<div>
		<div class='panel panel-default'>
			<div class='panel-heading'>
				<strong><i class="fa fa-bar-chart-o"></i> Báo cáo tồn kho</strong>
			</div>
			<div class="panel-body" id="parent-form-area">

				<form  id="form">
					<div class="col-sm-12">
						<div class="row">
							<label for="customer_code" class="control-label col-sm-2">Kho: </label>
							<div class="col-sm-10">
								<span style="font-weight: bold;">{{$stock_names}}</span>
							</div>
						</div>
						<div class="row">
							<label class="control-label col-sm-2">Từ </label>
							<div class="col-sm-4">
								<div class="input-group" >
									<input id="from_date" readonly type="text" class="form-control bg-white" required>
									<div class="input-group-addon bg-gray">
										<i class="fa fa-calendar"></i>
									</div>
								</div>
							</div>
							<label class="control-label col-sm-2">Đến </label>
							<div class="col-sm-4">
								<div class="input-group" >
									<input id="to_date" readonly type="text" class="form-control" readonly>
									<div class="input-group-addon bg-gray">
										<i class="fa fa-calendar"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="box-footer" style="background: #F5F5F5">

				<div class="form-group">
					<label class="control-label col-sm-2"></label>
					<div class="col-sm-10">
						<a id="print_report_detail" style="cursor: pointer;" onclick="printReport()" class="btn btn-primary"><i class="fa fa-print"></i> In báo cáo</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="loading"></div>
@endsection

@push('bottom')
	<style>
		.content-header{
			display: none;
		}
		.loading{
			display: none;
		}
	</style>

	<script type="application/javascript">
        $(function(){
            $('#from_date').datepicker({
                format:'dd/mm/yyyy',
                autoclose:true,
                todayHighlight:true,
                showOnFocus:false
            });
            $('#to_date').val(moment().format('DD/MM/YYYY'))
		});

        function popupWindow(url,windowName) {
            window.open(url,windowName,'height=500,width=600');
            return false;
        }
        function printReport() {
            if($('#from_date').val()) {
                var from_date = moment($('#from_date').val(),'DD/MM/YYYY').format('YYYY-MM-DD');
                popupWindow("{{action('AdminGoldStocksTransferController@getPrintInventoryReport')}}?from_date=" + from_date,"print");
            }else{
                alert("Bạn phải chọn ngày!");
            }
        }
	</script>
@endpush