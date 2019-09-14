<!-- First you need to extend the CB layout -->
@extends('crudbooster::admin_template')
@section('content')
	<!-- Your custom  HTML goes here -->
	<!-- Your html goes here -->
	<div>
		<p><a title="Return" href="{{CRUDBooster::mainpath()}}"><i class="fa fa-chevron-circle-left "></i> &nbsp; Quay lại danh sách Đơn Hàng</a></p>
		<div class='panel panel-default'>
			<div class='panel-heading'>
				<strong><i class="fa fa-flash"></i> {{$mode=='new'?'Tạo Đơn Hàng Nhanh':(mode=='edit'?'Sửa Đơn Hàng Nhanh':'Chi tiết Đơn Hàng Nhanh')}}</strong>
			</div>

			<div class="panel-body" id="parent-form-area">

				<form method='post' action='{{CRUDBooster::mainpath('add-save')}}' id="form">
					<input type="hidden" name="id" id="id">
					<input type="hidden" name="saler_id" id="saler_id">
					<div class="col-sm-12">
						<div class="row">
							<label for="customer_code" class="control-label col-sm-2">Khách hàng <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
							<div class="col-sm-10">
								<div class="input-group">
									<span class="input-group-btn">
										<button type="button" class="btn btn-primary btn-flat" onclick="showModalcustomer_id()"><i class="fa fa-search"></i></button>
									</span>
									<input required type="text" name="customer_code" id="customer_code" onchange="searchCustomer();" class="form-control" placeholder="Mã KH">

								</div>
							</div>
							<div class="col-sm-12">
								<input type="hidden" name="customer_id" id="customer_id">
								<input type="text" name="customer_name" id="customer_name" class="form-control" placeholder="Tên KH">
								<input type="text" name="customer_phone" id="customer_phone" class="form-control" placeholder="Số ĐT">
								<input type="text" name="customer_address" id="customer_address" class="form-control" placeholder="Địa chỉ">
							</div>
						</div>
						<div class="row" style="display: none;">
							<label class="control-label col-sm-1">Công nợ</label>
							<label class="control-label col-sm-1 text-right">Ngày </label>
							<div class="col-sm-2">
								<input type="text" name="debt_date" id="debt_date" class="form-control" placeholder="Ngày công nợ" readonly disabled>
							</div>
							<label class="control-label col-sm-1 text-right">Số ngày </label>
							<div class="col-sm-1">
								<input type="text" name="days_diff" id="days_diff" class="form-control money" readonly disabled>
							</div>
							<label class="control-label col-sm-1 text-right">Q10 </label>
							<div class="col-sm-2">
								<input type="text" name="exchange_g10" id="exchange_g10" class="form-control money" readonly disabled>
							</div>
							<label class="control-label col-sm-1 text-right">Công </label>
							<div class="col-sm-2">
								<input type="text" name="wage" id="wage" class="form-control money" readonly disabled>
							</div>
						</div>
					</div>

					<div class="col-sm-12">
						<div id="header1" data-collapsed="false" class="header-title form-divider">
							<h4>
								<strong><i class="fa fa-list-alt"></i> Thông tin đơn hàng</strong>
								{{--<span class="pull-right icon"><i class="fa fa-minus-square-o"></i></span>--}}
							</h4>
						</div>
						<div class="form-group header-group-1">
							<div class="row">
								<label class="control-label col-sm-2">Ngày </label>
								<div class="col-sm-2">
									<div class="input-group" >
										<input id="order_date" readonly type="text" class="form-control bg-white" placeholder="Ngày đơn hàng" required>
										<div class="input-group-addon bg-gray">
											<i class="fa fa-calendar"></i>
										</div>
									</div>
								</div>
								<label class="control-label col-sm-2 text-right">CK chung (%) </label>
								<div class="col-sm-2">
									<input type="text" name="sampling_discount" id="sampling_discount" onchange="saleOrderHeadChange();" class="form-control money" placeholder="Chiết khấu chung">
								</div>
							</div>
							<div class="row">
								<label class="control-label col-sm-2">Tuổi đồ đúc </label>
								<div class="col-sm-2">
									<input type="text" name="gold_age_1" id="gold_age_1" onchange="saleOrderHeadChange();" class="form-control money" value="{{CRUDBooster::getSetting('do_duc')}}" placeholder="Tuổi đồ đúc">
								</div>
								<label class="control-label col-sm-2">Tuổi đồ bộng </label>
								<div class="col-sm-2">
									<input type="text" name="gold_age_2" id="gold_age_2" onchange="saleOrderHeadChange();" class="form-control money" value="{{CRUDBooster::getSetting('do_bong')}}" placeholder="Tuổi đồ bộng">
								</div>
								<label class="control-label col-sm-2 text-right">Tuổi hàng khác</label>
								<div class="col-sm-2">
									<input type="text" name="gold_age_3" id="gold_age_3" onchange="saleOrderHeadChange();" class="form-control money" value="{{CRUDBooster::getSetting('tuoi_hang_khac')}}" placeholder="Tuổi hàng khác">
								</div>
							</div>
							<div class="row">
								<label class="control-label col-sm-2">Mã vạch </label>
								<div class="col-sm-2">
									<input type="text" name="bar_code" id="bar_code" class="form-control"
										   placeholder="Quét mã vạch" autocomplete="off" onkeyup="findProduct(event)" onchange="barCodeChange()"
										   style="background-color: rgba(251,240,83,0.52)">
								</div>
							</div>
						</div>
						<div class="form-group header-group-1">
							<table id="table_order_details" class='table table-striped table-bordered'>
								<thead>
								<tr class="bg-success">
									<th>Danh sách hàng bán</th>
								</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
								<tr class="bg-gray-active">
									<th><label class="col-md-12 col-xs-12 no-padding text-center" style="color: #fff;">Tổng cộng</label>
										<label class="col-md-5 col-xs-5 no-padding">Tổng TL: </label><label id="total_order_total_weight" class="col-md-7 col-xs-7 text-right">0</label>
										<label class="col-md-5 col-xs-5 no-padding">TL đá: </label><label id="total_order_gem_weight" class="col-md-7 col-xs-7 text-right">0</label>
										<label class="col-md-5 col-xs-5 no-padding">TL vàng: </label><label id="total_order_gold_weight" class="col-md-7 col-xs-7 text-right">0</label>
										<label class="col-md-5 col-xs-5 no-padding">Vàng Q10: </label><label id="total_order_exchange_g10" class="col-md-7 col-xs-7 text-right">0</label>
										<label class="col-md-5 col-xs-5 no-padding">Tiền công: </label><label id="total_order_wage" class="col-md-7 col-xs-7 text-right">0</label>
									</th>
								</tr>
								</tfoot>
							</table>
						</div>

						<div class="form-group header-group-1">
							<div class="row">
								<label class="control-label col-sm-2">Tổng cân thực tế </label>
								<div class="col-sm-2">
									<input type="text" name="actual_weight" id="actual_weight" class="form-control money" required>
								</div>
								<label class="control-label col-sm-2">Giảm trừ (đ)</label>
								<div class="col-sm-2">
									<input type="text" name="reduce" id="reduce" class="form-control money" required>
								</div>

								<label class="control-label col-sm-2 text-right">Số đơn hàng </label>
								<div class="col-sm-2">
									<input type="text" name="order_no" id="order_no" class="form-control" placeholder="Phát sinh tự động khi lưu" readonly disabled>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-12" style="display: none;">
						<div id="header2" data-collapsed="false" class="header-title form-divider">
							<h4>
								<strong><i class="fa fa-reply-all"></i> Hàng trả lại</strong>
								{{--<span class="pull-right icon"><i class="fa fa-minus-square-o"></i></span>--}}
							</h4>
						</div>
					</div>
					<div class="col-sm-10" style="display: none;">
						<div class="form-group header-group-2">
							<table id="table_returns" class='table table-bordered'>
								<thead>
								<tr class="bg-danger">
									<th style="width: 120px;">Mục</th>
									<th>Số lượng (món)</th>
									<th>Tổng cân (chỉ)</th>
									<th>Tổng đá (chỉ)</th>
									<th>Tổng vàng 18 (chỉ)</th>
									<th>Tuổi (%)</th>
									<th>Vàng quy 10 (chỉ)</th>
									<th>Tiền công (đ)</th>
								</tr>
								</thead>
								<tbody>
								<tr>
									<th>Đồ đúc</th>
									<th class="no-padding"><input id="return1_qty" name="return1_qty" onchange="returns_chage(1);" type="text" class="form-control money"></th>
									<th class="no-padding"><input id="return1_total_weight" name="return1_total_weight" onchange="returns_chage(1);" type="text" class="form-control money"></th>
									<th class="no-padding"><input id="return1_gem_weight" name="return1_gem_weight" onchange="returns_chage(1);" type="text" class="form-control money"></th>
									<th class="text-right" id="return1_gold_weight"></th>
									<th class="no-padding"><input id="return1_gold_age" name="return1_gold_age" onchange="returns_chage(1);" type="text" class="form-control money"></th>
									<th class="text-right" id="return1_exchange_g10"></th>
									<th class="no-padding"><input id="return1_wage" name="return1_wage" onchange="returns_chage(1);" type="text" class="form-control money"></th>
								</tr>
								<tr>
									<th>Đồ bộng</th>
									<th class="no-padding"><input id="return2_qty" name="return2_qty" onchange="returns_chage(2);" type="text" class="form-control money"></th>
									<th class="no-padding"><input id="return2_total_weight" name="return2_total_weight" onchange="returns_chage(2);" type="text" class="form-control money"></th>
									<th class="no-padding"><input id="return2_gem_weight" name="return2_gem_weight" onchange="returns_chage(2);" type="text" class="form-control money"></th>
									<th class="text-right" id="return2_gold_weight"></th>
									<th class="no-padding"><input id="return2_gold_age" name="return2_gold_age" onchange="returns_chage(2);" type="text" class="form-control money"></th>
									<th class="text-right" id="return2_exchange_g10"></th>
									<th class="no-padding"><input id="return2_wage" name="return2_wage" onchange="returns_chage(2);" type="text" class="form-control money"></th>
								</tr>
								<tr>
									<th>Hàng khác</th>
									<th class="no-padding"><input id="return3_qty" name="return3_qty" onchange="returns_chage(3);" type="text" class="form-control money"></th>
									<th class="no-padding"><input id="return3_total_weight" name="return3_total_weight" onchange="returns_chage(3);" type="text" class="form-control money"></th>
									<th class="no-padding"><input id="return3_gem_weight" name="return3_gem_weight" onchange="returns_chage(3);" type="text" class="form-control money"></th>
									<th class="text-right" id="return3_gold_weight"></th>
									<th class="no-padding"><input id="return3_gold_age" name="return3_gold_age" onchange="returns_chage(3);" type="text" class="form-control money"></th>
									<th class="text-right" id="return3_exchange_g10"></th>
									<th class="no-padding"><input id="return3_wage" name="return3_wage" onchange="returns_chage(3);" type="text" class="form-control money"></th>
								</tr>
								</tbody>
								<tfoot>
								<tr class="bg-gray-active">
									<th>Tổng cộng</th>
									<th class="text-right" id="return0_qty">0</th>
									<th class="text-right" id="return0_total_weight">0</th>
									<th class="text-right" id="return0_gem_weight">0</th>
									<th class="text-right" id="return0_gold_weight">0</th>
									<th class="text-right"></th>
									<th class="text-right" id="return0_exchange_g10">0</th>
									<th class="text-right" id="return0_wage">0</th>
								</tr>
								</tfoot>
							</table>
						</div>
					</div>
					<div class="col-sm-12" style="display: none;">
						<div id="header3" data-collapsed="false" class="header-title form-divider">
							<h4>
								<strong><i class="fa fa-dollar"></i> Thanh toán (tạm tính trước khi có kết quả phổ)</strong>
								{{--<span class="pull-right icon"><i class="fa fa-minus-square-o"></i></span>--}}
							</h4>
						</div>
					</div>
					<div class="col-sm-12" style="display: none;">
						<div class="form-group header-group-2">
							<table id="table_pays" class='table table-bordered'>
								<thead>
								<tr class="bg-success">
									<th style="width: 20px;">#</th>
									<th style="width: 120px;">Hình thức thu</th>
									<th style="width: 200px;">Nội dung</th>
									<th>Số lượng<br>(món)</th>
									<th>Tổng cân<br>(chỉ)</th>
									<th>Tổng đá<br>(chỉ)</th>
									<th>Tổng vàng (chỉ)<br>Tiền mặt (đồng)</th>
									<th>Tuổi (%)<br>Giá quy đổi (đồng)</th>
									<th>Vàng quy 10<br>(chỉ)</th>
									<th>Tiền công<br>(đồng)</th>
								</tr>
								</thead>
								<tbody>

								</tbody>
								<tfoot>
								<tr>
									<th class="text-center"><a onclick="addNewPayDetail()" class="text-blue" style="cursor: pointer;"><i class="fa fa-plus"></i></a></th>
									<th colspan="9"></th>
								</tr>
								<tr class="bg-gray-active">
									<th colspan="2" class="text-center">Tổng cộng</th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th class="text-right" id="pay_total_exchange_g10">0</th>
									<th class="no-padding"><input id="pay_total_wage" type="text" onchange="calcTotalSaleOrder()" class="form-control money" placeholder="Tổng tiền công"></th>
								</tr>
								</tfoot>
							</table>
						</div>
					</div>
					<div class="col-sm-12" style="display: none;">
						<h4>
							<strong><i class="fa fa-desktop"></i> Thông tin hành chính</strong>
							{{--<span class="pull-right icon"><i class="fa fa-minus-square-o"></i></span>--}}
						</h4>
					</div>
					<div class="col-sm-8" style="display: none;">
						<b>Công nợ cuối (Tạm tính trước khi có kết quả phổ)</b>
						<div class="form-group header-group-2">
							<table id="table_info" class='table table-bordered'>
								<tbody>
								<tr class="bg-info">
									<th rowspan="2" class="text-center" style="vertical-align: middle;width: 300px;">
										Công nợ này là tạm tính trước khi có kết quả phổ từ công ty Việt Ngọc
									</th>
									<th class="text-center" style="vertical-align: middle;">Ngày thu</th>
									<th class="text-center">Vàng quy 10<br>(chỉ)</th>
									<th class="text-center">Tiền công<br>(đồng)</th>
								</tr>
								<tr>
									<th class="no-padding">
										<div class="input-group" >
											<input id="pay_date" type="text" class="form-control bg-white" placeholder="Ngày thu" required>
											<div class="input-group-addon bg-gray">
												<i class="fa fa-calendar"></i>
											</div>
										</div>
									</th>
									<th class="text-right" id="total_sale_exchange_g10">0</th>
									<th class="text-right" id="total_sale_wage">0</th>
								</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="col-sm-4" style="display: none;"></div>
					<div class="col-sm-12" style="display: none;"></div>
					<div class="col-sm-8" style="display: none;">
						<b style="text-decoration:underline;">GHI CHÚ:</b> Giá trên chưa bao gồm thuế GTGT.<br>
						<label class="control-label">Lý do chưa thu hết công nợ cũ/tiền công:</label>
						<textarea name="reason_pay_not_enough" id="reason_pay_not_enough" class="form-control"></textarea>
						<label class="control-label">Khách đặt:</label>
						<textarea name="other_orders" id="other_orders" class="form-control"></textarea>
					</div>
					<div class="col-sm-4"></div>
				</form>
			</div>
			<div class="box-footer" style="background: #F5F5F5">

				<div class="form-group">
					<label class="control-label col-sm-2"></label>
					<div class="col-sm-10">
						<a href="{{CRUDBooster::mainpath()}}" class="btn btn-default"><i class="fa fa-chevron-circle-left"></i> Quay về</a>
						@if($mode=='new' || $mode=='edit')
							<button id="save_button" class="btn btn-success" onclick="submit()"><i class="fa fa-save"></i> Lưu</button>
						@endif
						<a id="print_invoice" style="display: none;cursor: pointer;" onclick="printInvoice()" class="btn btn-info"><i class="fa fa-print"></i> In hóa đơn</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="modal-datamodal-customer_id" class="modal in" tabindex="-1" role="dialog" aria-hidden="false" style="display: none; padding-right: 7px;">
		<div class="modal-dialog modal-lg " role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title"><i class="fa fa-search"></i> Browse Data | Khách hàng</h4>
				</div>
				<div class="modal-body">
					<iframe id="iframe-modal-customer_id" style="border:0;height: 430px;width: 100%"></iframe>
				</div>

			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div>

	<div class="loading"></div>
@endsection

@push('bottom')
	<style>
		.header-title {
			cursor: pointer;
		}
		.form-divider {
			/*padding: 10px 0px 10px 0px;*/
			margin-bottom: 10px;
			border-bottom: 1px solid #dddddd;
		}
		.row{
			margin-bottom: 5px;
		}
		.table thead tr th{
			text-align: center;
			vertical-align: middle;
		}
		select.form-control{
			border-radius: 0 !important;
		}
		.invalid{
			border-color: red !important;
		}
		.loading{
			display: none;
		}
		.money{
			text-align: right;
		}
	</style>

	<script type="application/javascript">
        // table_order_details = null;
        stamp_weight = Number('{{CRUDBooster::getSetting('trong_luong_tem')}}');
        owner_stock_ids = '{{$stock_ids}}'.split(',');
        order_id: null; // sẽ có khi lưu thành công
        readOnlyAll = false;
        order_details = [];
        total_order = {
            total_weight: 0,
            gem_weight: 0,
            gold_weight: 0,
            exchange_g10: 0,
            wage: 0
        };
        order_returns = [
            {
                id: null,
                order_id: null,
                sort_no: 1,
                type: 1, // đồ đúc
                qty: 0,
                total_weight: 0,
                gem_weight: 0,
                gold_weight: 0,
                gold_age: 0,
                exchange_g10: 0,
                wage: 0
            },
            {
                id: null,
                order_id: null,
                sort_no: 2,
                type: 2, // đồ bộng
                qty: 0,
                total_weight: 0,
                gem_weight: 0,
                gold_weight: 0,
                gold_age: 0,
                exchange_g10: 0,
                wage: 0
            },
            {
                id: null,
                order_id: null,
                sort_no: 3,
                type: 3, // hàng khác
                qty: 0,
                total_weight: 0,
                gem_weight: 0,
                gold_weight: 0,
                gold_age: 0,
                exchange_g10: 0,
                wage: 0
            }
        ];
        return_total = {
            qty: 0,
            total_weight: 0,
            gem_weight: 0,
            gold_weight: 0,
            exchange_g10: 0,
            wage: 0
        };
        order_pays = [];
        pay_total_exchange_g10 = 0;
        optionNumberInput = {
            allowDecimalPadding: false,
            decimalPlaces: 6,
            decimalPlacesRawValue: 6,
            leadingZero: "allow",
            modifyValueOnWheel: false,
            negativeSignCharacter: "−",
            outputFormat: "number"
        };
        total_sale_exchange_g10 = 0;
        total_sale_wage = 0;
        lastTimeScanBarCode = moment();
        $(function(){
            // $(document).scannerDetection({
            //     timeBeforeScanTest: 200, // wait for the next character for upto 200ms
            //     avgTimeByChar: 40, // it's not a barcode if a character takes longer than 100ms
            //     preventDefault: false,
            //     endChar: [13],
            //     onComplete: function(barcode, qty) {
            //         $('#bar_code').val(barcode);
            //         findProduct(null);
            //     },
            //     onError: function( string, qty) {}
            // });
            let todayStr = moment().format('DD/MM/YYYY');
            if($('#order_date').val() == ''){
                $('#order_date').val(moment().format('DD/MM/YYYY HH:mm:ss'));
            }
            if($('#pay_date').val() == ''){
                $('#pay_date').val(todayStr);
            }
            $('#order_date').datetimepicker({
                format:'d/m/Y H:i:s',
                autoclose:true,
                todayHighlight:true,
                showOnFocus:false
            });
            $('#pay_date').datepicker({
                format:'dd/mm/yyyy',
                autoclose:true,
                todayHighlight:true,
                showOnFocus:false
            });
            //$('.money').autoNumeric('init', optionNumberInput);
            AutoNumeric.multiple('.money', optionNumberInput);
            // table_order_details = $('#table_order_details').dataTable({
            //     language: {
            //         processing: '<div class="loading"></div>'
            //     },
            //     paging: false,
            //     searching: false,
            //     ordering: false,
            //     autoWidth: true,
            //     scrollX: true,
            //     stateSave: true,
            //     scrollY: '20vh',
            //     scrollCollapse: true,
            //     processing: true,
            //     data: order_details,
            //     columns: [
            //         { data: 'id' },
            //         { data: 'no' },
            //         { data: 'bar_code' },
            //         { data: 'product_type_id' },
            //         { data: 'product_name' },
            //         { data: 'product_code' },
            //         { data: 'age' },
            //         { data: 'total_weight' },
            //         { data: 'gem_weight' },
            //         { data: 'gold_weight' },
            //         { data: 'exchange_g10' },
            //         { data: 'retail_machining_fee' },
            //         { data: 'discount_machining_fee' },
            //         { data: 'product_group_id' },
            //         { data: 'stock_id' }
            //     ],
            // });
        });
        function searchCustomer() {
            let customer_code = $('#customer_code').val();
            if(customer_code && customer_code.trim() != ''){
                //$('.loading').show();
                $.ajax({
                    method: "GET",
                    url: '{{Route("AdminGoldCustomersControllerGetSearchCustomer")}}',
                    data: {
                        customer_code: customer_code,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    async: true,
                    success: function (data) {
                        if (data){
                            if(data.customer){
                                $('#customer_id').val(data.customer.id);
                                $('#customer_name').val(data.customer.name);
                                $('#customer_address').val(data.customer.address);
                                $('#customer_phone').val(data.customer.phone);
                                $('#customer_name').attr('readonly', true);
                                $('#customer_address').attr('readonly', true);
                                $('#customer_phone').attr('readonly', true);
                                if(data.debt){
                                    if(data.debt.date) {
                                        let date = moment(data.debt.date);
                                        $('#debt_date').val(date.format('DD/MM/YYYY'));
                                        $('#days_diff').val(moment().diff(date, 'd'));
                                    } else {
                                        $('#debt_date').val(null);
                                        $('#days_diff').val(null);
                                    }
                                    $('#exchange_g10').val(Math.round(1000 * (data.debt.exchange_g10_credit - data.debt.exchange_g10_debit))/1000);
                                    $('#wage').val(data.debt.wage_credit - data.debt.wage_debit);
                                    calcTotalSaleOrder();
                                }
                                setTimeout(function () {
                                    $('#bar_code').focus();
                                },100);
                            }else{
                                $('#customer_name').attr('readonly', false);
                                $('#customer_address').attr('readonly', false);
                                $('#customer_phone').attr('readonly', false);
                                $('#debt_date').val(null);
                                $('#exchange_g10').val(null);
                                $('#days_diff').val(null);
                                $('#wage').val(null);
                                calcTotalSaleOrder();
                            }
                        }else{
                            $('#customer_name').attr('readonly', false);
                            $('#customer_address').attr('readonly', false);
                            $('#customer_phone').attr('readonly', false);
                            $('#debt_date').val(date.format('DD/MM/YYYY'));
                            $('#exchange_g10').val(null);
                            $('#days_diff').val(null);
                            $('#wage').val(null);
                            calcTotalSaleOrder();
                        }
                        //$('#loading').hide();
                    },
                    error: function (request, status, error) {
                        //$('.loading').hide();
                        swal("Thông báo","Có lỗi xãy ra khi tải dữ liệu, vui lòng thử lại.","warning");
                    }
                });
            } else {
                $('#customer_id').val(null);
                $('#customer_name').val(null);
                $('#customer_address').val(null);
                $('#customer_phone').val(null);
                $('#debt_date').val(null);
                $('#exchange_g10').val(null);
                $('#days_diff').val(null);
                $('#wage').val(null);
                calcTotalSaleOrder();
            }
        }

        function showModalcustomer_id() {
            var url_customer_id = "{{action('AdminGoldSaleOrdersController@getModalData')}}/gold_sale_orders/modal-data?table=gold_customers&columns=id,code,tmp_code,name,address,phone&name_column=customer_id&where=deleted_at+is+null+and+saler_id={{CRUDBooster::myId()}}&select_to=code:code,tmp_code:tmp_code&columns_name_alias=Mã khách hàng,Mã tạm,Tên khách hàng,Địa chỉ,Số điện thoại";
            $('#iframe-modal-customer_id').attr('src',url_customer_id);
            $('#modal-datamodal-customer_id').modal('show');
        }
        function hideModalcustomer_id() {
            $('#modal-datamodal-customer_id').modal('hide');
        }
        function selectAdditionalDatacustomer_id(select_to_json) {
            if(select_to_json.code){
                $('#customer_code').val(select_to_json.code).trigger('change');
                $('#customer_code').trigger('change');
            } else {
                $('#customer_code').val(select_to_json.tmp_code).trigger('change');
                $('#customer_code').trigger('change');
            }
            hideModalcustomer_id();
        }
		function barCodeChange() {
            findProduct(null);
		}
        function findProduct(event) {
            if(event == null || event.keyCode == 13) {
                if(moment().diff(lastTimeScanBarCode, 's') >= 1) {
                    lastTimeScanBarCode = moment();
                    let bar_code = $('#bar_code').val();
                    let added = false;
                    order_details.forEach(function (detail, index) {
                        if (detail.bar_code == bar_code) {
                            added = true;
                            $('#bar_code').val(null);
                            swal("Thông báo", "Sản phẩm [" + bar_code + "] đã được thêm.", "info");
                        }
                    });
                    if (bar_code && !added) {
                        $.ajax({
                            method: "GET",
                            url: '{{Route("AdminGoldProductsControllerGetSearchProduct")}}',
                            data: {
                                bar_code: bar_code,
                                _token: '{{ csrf_token() }}'
                            },
                            dataType: "json",
                            async: true,
                            success: function (data) {
                                if (data && data.product) {
                                    if (data.product.qty == 0) {
                                        $('#bar_code').val(null);
                                        swal("Thông báo", "Sản phẩm [" + bar_code + "] đã bán.", "warning");
                                    } else if (owner_stock_ids.indexOf(data.product.stock_id + '') < 0) {
                                        swal("Thông báo", "Sản phẩm [" + bar_code + "] nằm trong " + data.product.stock_name + ", không thuộc quyền quản lý của bạn, hãy kiểm tra lại.", "error");
                                    } else {
                                        data.product.no = order_details.length + 1;
                                        switch (data.product.product_code.substr(0,2).toUpperCase()) {
                                            case 'V_': //hàng khác
                                                data.product.age = Number($('#gold_age_3').val()?$('#gold_age_3').val().replace(/,/g, ''):0);
                                                break;
                                            case 'B_': //đồ bộng
                                                data.product.age = Number($('#gold_age_2').val()?$('#gold_age_2').val().replace(/,/g, ''):0);
                                                break;
                                            default: //đồ đúc
                                                data.product.age = Number($('#gold_age_1').val()?$('#gold_age_1').val().replace(/,/g, ''):0);
                                        }
                                        data.product.exchange_g10 = data.product.qty * data.product.gold_weight * data.product.age / 100;
                                        data.product.discount_machining_fee = $('#sampling_discount').val()?Number($('#sampling_discount').val().replace(/,/g, '')):0;
                                        order_details.push(data.product);
                                        addNewSaleOrderDetail(data.product);
                                        $('#bar_code').val(null);
                                        // table_order_details.data = order_details;
                                        // console.log('order_details = ', order_details);
                                        // console.log('table_order_details.data = ', table_order_details.data);
                                        // console.log('table_order_details = ', table_order_details);
                                        // table_order_details.fnDraw();

                                        calcTotalOfSaleOrderDetails();
                                        calcTotalSaleOrder();
                                    }
                                } else {
                                    $('#bar_code').val(null);
                                    swal("Thông báo", "Không tìm thấy mã " + bar_code, "warning");
                                }
                                //$('#loading').hide();
                            },
                            error: function (request, status, error) {
                                //$('.loading').hide();
                                swal("Thông báo", "Có lỗi xãy ra khi tải dữ liệu, vui lòng thử lại.", "warning");
                            }
                        });
                    }
                }
            }
        }

        function saleOrderHeadChange() {
            let sampling_discount = Number($('#sampling_discount').val()?$('#sampling_discount').val().replace(/,/g, ''):0);
            let gold_age_1 = Number($('#gold_age_1').val()?$('#gold_age_1').val().replace(/,/g, ''):0);
            let gold_age_2 = Number($('#gold_age_2').val()?$('#gold_age_2').val().replace(/,/g, ''):0);
            let gold_age_3 = Number($('#gold_age_3').val()?$('#gold_age_3').val().replace(/,/g, ''):0);
            order_details.forEach(function (detail, index) {
                detail.discount_machining_fee = sampling_discount;
                $(`#discount_machining_fee_${detail.id}`).html(detail.discount_machining_fee?detail.discount_machining_fee.toLocaleString('en-US') + ' %':'');
                switch (detail.product_code.substr(0,2).toUpperCase()) {
                    case 'V_': //hàng khác
                        detail.age = gold_age_3;
                        detail.exchange_g10 = detail.qty * detail.gold_weight * detail.age / 100;
                        break;
                    case 'B_': //đồ bộng
                        detail.age = gold_age_2;
                        detail.exchange_g10 = detail.qty * detail.gold_weight * detail.age / 100;
                        break;
                    default: //đồ đúc
                        detail.age = gold_age_1;
                        detail.exchange_g10 = detail.qty * detail.gold_weight * detail.age / 100;
                }
                $(`#age_${detail.id}`).html(detail.age?detail.age.toLocaleString('en-US') + ' %':'');
                $(`#exchange_g10_${detail.id}`).html(detail.exchange_g10?detail.exchange_g10.toLocaleString('en-US'):'');
            })
            valid_actual_weight();
            calcTotalOfSaleOrderDetails();
            calcTotalSaleOrder();
        }
        function calcTotalOfSaleOrderDetails() {
            total_order = {
                total_weight: 0,
                gem_weight: 0,
                gold_weight: 0,
                exchange_g10: 0,
                wage: 0
            };
            order_details.forEach(function (detail, index) {
                total_order.total_weight += detail.total_weight ? detail.total_weight : 0;
                total_order.gem_weight += detail.gem_weight ? detail.gem_weight : 0;
                total_order.gold_weight += detail.gold_weight ? detail.gold_weight : 0;
                total_order.exchange_g10 += detail.exchange_g10 ? detail.exchange_g10 : 0;
                // Ở phần xuất đơn hàng nhanh. Ở phần tổng cộng a cho trừ chiết khấu luôn
				let wage = (detail.retail_machining_fee ? detail.retail_machining_fee : 0);
                total_order.wage += wage - wage * (detail.discount_machining_fee?detail.discount_machining_fee:0) / 100;
            });
            $('#total_order_total_weight').html(total_order.total_weight.toLocaleString('en-US'));
            $('#total_order_gem_weight').html(total_order.gem_weight.toLocaleString('en-US'));
            $('#total_order_gold_weight').html(total_order.gold_weight.toLocaleString('en-US'));
            $('#total_order_exchange_g10').html(total_order.exchange_g10.toLocaleString('en-US'));
            $('#total_order_wage').html(total_order.wage.toLocaleString('en-US'));
        }
        function addNewSaleOrderDetail(dataRow) {
            if(readOnlyAll){
                swal("Thông báo", "Bạn không thể thêm sản phẩm sau khi đã lưu đơn hàng, hãy tạo đơn hàng mới.", "warning");
                return;
            }
            let html = `<tr id="order_detail_${dataRow.id}">` +
				`<th>` +
					`<label class="col-md-5 col-xs-5 no-padding">STT: </label><label class="col-md-5 col-xs-5" id="no_${dataRow.id}">${dataRow.no}</label>` +
                	`<label class="col-md-1 col-xs-1 text-right"><a style="cursor: pointer;font-size: xx-large;position: absolute;top: -10px;" onclick="removeSaleOrderDetail(${dataRow.id})"><i class="fa fa-remove text-red"></i></a></label>` +
					`<hr><label class="col-md-5 col-xs-5 no-padding">Mã vạch: </label><label class="col-md-7 col-xs-7">${dataRow.bar_code}</label>` +
					`<label class="col-md-5 col-xs-5 no-padding">Loại vàng: </label><label class="col-md-7 col-xs-7">${dataRow.product_type_name}</label>` +
					`<hr><label class="col-md-5 col-xs-5 no-padding">Tên hàng: </label><label class="col-md-7 col-xs-7">${dataRow.product_name}</label>` +
					`<label class="col-md-5 col-xs-5 no-padding">Mã hàng: </label><label class="col-md-7 col-xs-7">${dataRow.product_code}</label>` +
					`<hr><label class="col-md-5 col-xs-5 no-padding">Tuổi vàng: </label><label class="col-md-7 col-xs-7" id="age_${dataRow.id}">${dataRow.age.toLocaleString('en-US')}%</label>` +
					`<label class="col-md-5 col-xs-5 no-padding">Tổng TL: </label><label class="col-md-7 col-xs-7">${dataRow.total_weight.toLocaleString('en-US')}</label>` +
					`<hr><label class="col-md-5 col-xs-5 no-padding">TL đá: </label><label class="col-md-7 col-xs-7">${dataRow.gem_weight.toLocaleString('en-US')}</label>` +
					`<label class="col-md-5 col-xs-5 no-padding">TL vàng: </label><label class="col-md-7 col-xs-7">${dataRow.gold_weight.toLocaleString('en-US')}</label>` +
					`<hr><label class="col-md-5 col-xs-5 no-padding">Vàng Q10: </label><label class="col-md-7 col-xs-7" id="exchange_g10_${dataRow.id}">${dataRow.exchange_g10.toLocaleString('en-US')}</label>` +
					`<label class="col-md-5 col-xs-5 no-padding">Công: </label><label class="col-md-7 col-xs-7">${dataRow.retail_machining_fee.toLocaleString('en-US')}</label>` +
					`<hr><label class="col-md-5 col-xs-5 no-padding">CK công(%): </label><label class="col-md-7 col-xs-7" id="discount_machining_fee_${dataRow.id}">${dataRow.discount_machining_fee?dataRow.discount_machining_fee.toLocaleString('en-US')+'%':'0%'}</label>` +
					`<label class="col-md-5 col-xs-5 no-padding">Nhóm hàng: </label><label class="col-md-7 col-xs-7">${dataRow.product_group_name}</label>` +
					`<hr><label class="col-md-5 col-xs-5 no-padding">Kho: </label><label class="col-md-7 col-xs-7">${dataRow.stock_name}</label>` +
                `</th>` +
                `</tr>`;
            $('#table_order_details tbody').append(html);
            // let html_id = "#discount_machining_fee_" + dataRow.id;
            // setTimeout(function () {
            //     // $(html_id).autoNumeric('init', optionNumberInput);
            //     new AutoNumeric(html_id, optionNumberInput);
            // },100);
            valid_actual_weight();
        }
        function removeSaleOrderDetail(id) {
            if(readOnlyAll){
                // swal("Thông báo", "Bạn không thể thêm sản phẩm sau khi đã lưu đơn hàng, hãy tạo đơn hàng mới.", "warning");
                return;
            }
            let removeDetail = null;
            let removeIndex = -1;
            order_details.forEach(function (detail, index) {
                if(detail.id == id) {
                    removeDetail = detail;
                    removeIndex = index;
                }
                // giảm số thứ tự
                if(removeIndex != -1 && index > removeIndex) {
                    detail.no -= 1;
                    $('#no_'+id).html(detail.no);
                }
            });
            if(removeDetail){
                $('#order_detail_'+id).remove();
                order_details.splice(removeIndex, 1);
                calcTotalOfSaleOrderDetails();
                calcTotalSaleOrder();
                valid_actual_weight();
            }
        }
        function discount_machining_fee_change(id) {
            order_details.forEach(function (detail, index) {
                if(detail.id == id) {
                    detail.discount_machining_fee = Number($('#discount_machining_fee_'+id).val()?$('#discount_machining_fee_'+id).val().replace(/,/g, ''):0);
                    console.log('change detail = ', detail);
                }
            });

            calcTotalSaleOrder();
        }
        function returns_chage(type) // type = 1: đồ đúc, type = 2: đồ bộng
        {
            let returnDetail = order_returns[type-1];
            returnDetail.qty = Number($('#return' + type + '_qty').val()?$('#return' + type + '_qty').val().replace(/,/g, ''):0);
            returnDetail.total_weight = Number($('#return' + type + '_total_weight').val()?$('#return' + type + '_total_weight').val().replace(/,/g, ''):0);
            returnDetail.gem_weight = Number($('#return' + type + '_gem_weight').val()?$('#return' + type + '_gem_weight').val().replace(/,/g, ''):0);
            returnDetail.gold_weight = returnDetail.total_weight - returnDetail.gem_weight;
            returnDetail.gold_weight = Math.round(returnDetail.gold_weight * 1000000) / 1000000;
            $('#return' + type + '_gold_weight').html(returnDetail.gold_weight.toLocaleString('en-US'));
            returnDetail.gold_age = Number($('#return' + type + '_gold_age').val()?$('#return' + type + '_gold_age').val().replace(/,/g, ''):0);
            returnDetail.exchange_g10 = returnDetail.gold_weight * returnDetail.gold_age / 100;
            returnDetail.exchange_g10 = Math.round(returnDetail.exchange_g10 * 1000000) / 1000000;

            $('#return' + type + '_exchange_g10').html(returnDetail.exchange_g10.toLocaleString('en-US'));
            returnDetail.wage = Number($('#return' + type + '_wage').val()?$('#return' + type + '_wage').val().replace(/,/g, ''):0);

            return_total.qty = 0;
            return_total.total_weight = 0;
            return_total.gem_weight = 0;
            return_total.gold_weight = 0;
            return_total.exchange_g10 = 0;
            return_total.wage = 0;
            order_returns.forEach(function (detail, index) {
                return_total.qty += detail.qty;
                return_total.total_weight += detail.total_weight;
                return_total.gem_weight += detail.gem_weight;
                return_total.gold_weight += detail.gold_weight;
                return_total.exchange_g10 += detail.exchange_g10;
                return_total.wage += detail.wage;
            });
            $('#return0_qty').html(return_total.qty.toLocaleString('en-US'));
            $('#return0_total_weight').html(return_total.total_weight.toLocaleString('en-US'));
            $('#return0_gem_weight').html(return_total.gem_weight.toLocaleString('en-US'));
            $('#return0_gold_weight').html(return_total.gold_weight.toLocaleString('en-US'));
            $('#return0_exchange_g10').html(return_total.exchange_g10.toLocaleString('en-US'));
            $('#return0_wage').html(return_total.wage.toLocaleString('en-US'));

            calcTotalSaleOrder();
        }

        function addNewPayDetail() {
            if(readOnlyAll){
                // swal("Thông báo", "Bạn không thể thêm sản phẩm sau khi đã lưu đơn hàng, hãy tạo đơn hàng mới.", "warning");
                return;
            }
            let index = order_pays.length;
            order_pays.push({});
            let html = `<tr id="order_pay_index_${index}">
							<th class="text-center"><a onclick="removePayDetail(${index})" class="text-red" style="cursor: pointer;"><i class="fa fa-remove"></i></a></th>
							<th class="no-padding">
								<select id="pay${index}_pay_method" class="form-control" onchange="pay_method_change(${index})">
									<option value=""></option>
									<option value="1">Thảo</option>
									<option value="2">Dẻ</option>
									<option value="3">Tiền mặt</option>
									<option value="4">Đúc</option>
									<option value="5">Bộng</option>
								</select>
							</th>
							<th class="no-padding"><input id="pay${index}_description" onchange="pays_change(${index})" type="text" class="form-control"></th>
							<th class="no-padding"><input id="pay${index}_qty" type="text" onchange="pays_change(${index})" class="form-control money"></th>
							<th class="no-padding"><input id="pay${index}_total_weight" onchange="pays_change(${index})" type="text" class="form-control money"></th>
							<th class="no-padding"><input id="pay${index}_gem_weight" onchange="pays_change(${index})" type="text" class="form-control money"></th>
							<th class="no-padding">
								<input id="pay${index}_gold_weight" type="text" onchange="pays_change(${index})" class="form-control money" placeholder="Tổng vàng" style="display: none;">
								<input id="pay${index}_money" type="text" onchange="pays_change(${index})" class="form-control money" placeholder="Tiền mặt" style="display: none;">
							</th>
							<th class="no-padding">
								<input id="pay${index}_gold_age" type="text" onchange="pays_change(${index})" class="form-control money" placeholder="Tuổi vàng" style="display: none;">
								<input id="pay${index}_converted_price" type="text" onchange="pays_change(${index})" class="form-control money" placeholder="Giá quy đổi" style="display: none;">
							</th>
							<th class="text-right" id="pay${index}_exchange_g10"></th>
							<th></th>
						</tr>`;
            $('#table_pays tbody').append(html);
            let html_id = "#order_pay_index_" + index + " .money";
            setTimeout(function () {
                // $(html_id).autoNumeric('init', optionNumberInput);
                AutoNumeric.multiple(html_id, optionNumberInput);
            },100);
        }
        function removePayDetail(removeIndex) {
            if(readOnlyAll){
                // swal("Thông báo", "Bạn không thể thêm sản phẩm sau khi đã lưu đơn hàng, hãy tạo đơn hàng mới.", "warning");
                return;
            }
            $('#order_pay_index_'+removeIndex).remove();
            order_pays.splice(removeIndex, 1);
            calcTotalOfPays();
            calcTotalSaleOrder();
        }
        function pay_method_change(index) {
            let pay_method = $(`#pay${index}_pay_method`).val();
            switch (pay_method) {
                case '1':
                    $(`#pay${index}_qty`).hide();
                    $(`#pay${index}_qty`).val(null);
                    $(`#pay${index}_total_weight`).hide();
                    $(`#pay${index}_total_weight`).val(null);
                    $(`#pay${index}_gem_weight`).hide();
                    $(`#pay${index}_gem_weight`).val(null);
                    $(`#pay${index}_gold_weight`).show();
                    $(`#pay${index}_gold_weight`).attr('readonly', false);
                    $(`#pay${index}_gold_weight`).attr('disabled', false);
                    $(`#pay${index}_money`).hide();
                    $(`#pay${index}_money`).val(null);
                    $(`#pay${index}_gold_age`).show();
                    $(`#pay${index}_converted_price`).hide();
                    $(`#pay${index}_converted_price`).val(null);
                    break;
                case '2':
                    $(`#pay${index}_qty`).hide();
                    $(`#pay${index}_qty`).val(null);
                    $(`#pay${index}_total_weight`).hide();
                    $(`#pay${index}_total_weight`).val(null);
                    $(`#pay${index}_gem_weight`).hide();
                    $(`#pay${index}_gem_weight`).val(null);
                    $(`#pay${index}_gold_weight`).show();
                    $(`#pay${index}_gold_weight`).attr('readonly', false);
                    $(`#pay${index}_gold_weight`).attr('disabled', false);
                    $(`#pay${index}_money`).hide();
                    $(`#pay${index}_money`).val(null);
                    $(`#pay${index}_gold_age`).show();
                    $(`#pay${index}_converted_price`).hide();
                    $(`#pay${index}_converted_price`).val(null);
                    break;
                case '3':
                    $(`#pay${index}_qty`).hide();
                    $(`#pay${index}_qty`).val(null);
                    $(`#pay${index}_total_weight`).hide();
                    $(`#pay${index}_total_weight`).val(null);
                    $(`#pay${index}_gem_weight`).hide();
                    $(`#pay${index}_gem_weight`).val(null);
                    $(`#pay${index}_gold_weight`).hide();
                    $(`#pay${index}_gold_weight`).val(null);
                    $(`#pay${index}_gold_weight`).attr('readonly', false);
                    $(`#pay${index}_gold_weight`).attr('disabled', false);
                    $(`#pay${index}_money`).show();
                    $(`#pay${index}_gold_age`).hide();
                    $(`#pay${index}_gold_age`).val(null);
                    $(`#pay${index}_converted_price`).show();
                    break;
                case '4':
                    $(`#pay${index}_qty`).show();
                    $(`#pay${index}_total_weight`).show();
                    $(`#pay${index}_gem_weight`).show();
                    $(`#pay${index}_gold_weight`).show();
                    $(`#pay${index}_gold_weight`).attr('readonly', true);
                    $(`#pay${index}_gold_weight`).attr('disabled', true);
                    $(`#pay${index}_money`).hide();
                    $(`#pay${index}_money`).val(null);
                    $(`#pay${index}_gold_age`).show();
                    $(`#pay${index}_converted_price`).hide();
                    $(`#pay${index}_converted_price`).val(null);
                    break;
                case '5':
                    $(`#pay${index}_qty`).show();
                    $(`#pay${index}_total_weight`).show();
                    $(`#pay${index}_gem_weight`).show();
                    $(`#pay${index}_gold_weight`).show();
                    $(`#pay${index}_gold_weight`).attr('readonly', true);
                    $(`#pay${index}_gold_weight`).attr('disabled', true);
                    $(`#pay${index}_money`).hide();
                    $(`#pay${index}_money`).val(null);
                    $(`#pay${index}_gold_age`).show();
                    $(`#pay${index}_converted_price`).hide();
                    $(`#pay${index}_converted_price`).val(null);
                    break;
            }
            pays_change(index);
        }
        function pays_change(index) {
            let payDetail = order_pays[index];
            payDetail.qty = Number($(`#pay${index}_qty`).val()?$(`#pay${index}_qty`).val().replace(/,/g, ''):0);
            payDetail.description = $(`#pay${index}_description`).val();
            payDetail.total_weight = Number($(`#pay${index}_total_weight`).val()?$(`#pay${index}_total_weight`).val().replace(/,/g, ''):0);
            payDetail.gem_weight = Number($(`#pay${index}_gem_weight`).val()?$(`#pay${index}_gem_weight`).val().replace(/,/g, ''):0);
            // payDetail.gold_weight = Number($(`#pay${index}_gold_weight`).val()?$(`#pay${index}_gold_weight`).val().replace(/,/g, ''):0);
            payDetail.money = Number($(`#pay${index}_money`).val()?$(`#pay${index}_money`).val().replace(/,/g, ''):0);
            payDetail.gold_age = Number($(`#pay${index}_gold_age`).val()?$(`#pay${index}_gold_age`).val().replace(/,/g, ''):0);
            payDetail.converted_price = Number($(`#pay${index}_converted_price`).val()?$(`#pay${index}_converted_price`).val().replace(/,/g, ''):0);
            let pay_method = $(`#pay${index}_pay_method`).val();
            payDetail.pay_method = pay_method != '' ? Number(pay_method) : 0;
            payDetail.exchange_g10 = 0;
            payDetail.gold_weight = 0;
            switch (pay_method) {
                case '1':
                    payDetail.gold_weight = Number($(`#pay${index}_gold_weight`).val()?$(`#pay${index}_gold_weight`).val().replace(/,/g, ''):0);
                    payDetail.exchange_g10 = payDetail.gold_weight * payDetail.gold_age / 100;
                    payDetail.exchange_g10 = Math.round(payDetail.exchange_g10 * 1000000) / 1000000;
                    $(`#pay${index}_exchange_g10`).html(payDetail.exchange_g10.toLocaleString('en-US'));
                    break;
                case '2':
                    payDetail.gold_weight = Number($(`#pay${index}_gold_weight`).val()?$(`#pay${index}_gold_weight`).val().replace(/,/g, ''):0);
                    payDetail.exchange_g10 = payDetail.gold_weight * payDetail.gold_age / 100;
                    payDetail.exchange_g10 = Math.round(payDetail.exchange_g10 * 1000000) / 1000000;
                    $(`#pay${index}_exchange_g10`).html(payDetail.exchange_g10.toLocaleString('en-US'));
                    break;
                case '3':
                    payDetail.exchange_g10 = payDetail.converted_price == 0 ? 0 : (payDetail.money / payDetail.converted_price);
                    payDetail.exchange_g10 = Math.round(payDetail.exchange_g10 * 1000000) / 1000000;
                    $(`#pay${index}_exchange_g10`).html(payDetail.exchange_g10.toLocaleString('en-US'));
                    break;
                case '4':
                    payDetail.gold_weight = payDetail.total_weight - payDetail.gem_weight;
                    payDetail.gold_weight = Math.round(payDetail.gold_weight * 1000000) / 1000000;
                    $(`#pay${index}_gold_weight`).val(payDetail.gold_weight);
                    payDetail.exchange_g10 = payDetail.gold_weight * payDetail.gold_age / 100;
                    payDetail.exchange_g10 = Math.round(payDetail.exchange_g10 * 1000000) / 1000000;
                    $(`#pay${index}_exchange_g10`).html(payDetail.exchange_g10.toLocaleString('en-US'));
                    break;
                case '5':
                    payDetail.gold_weight = payDetail.total_weight - payDetail.gem_weight;
                    payDetail.gold_weight = Math.round(payDetail.gold_weight * 1000000) / 1000000;
                    $(`#pay${index}_gold_weight`).val(payDetail.gold_weight);
                    payDetail.exchange_g10 = payDetail.gold_weight * payDetail.gold_age / 100;
                    payDetail.exchange_g10 = Math.round(payDetail.exchange_g10 * 1000000) / 1000000;
                    $(`#pay${index}_exchange_g10`).html(payDetail.exchange_g10.toLocaleString('en-US'));
                    break;
            }
            calcTotalOfPays();
            calcTotalSaleOrder();
        }
        function calcTotalOfPays() {
            pay_total_exchange_g10 = 0;
            order_pays.forEach(function (detail, index) {
                pay_total_exchange_g10 += detail.exchange_g10;
            });
            pay_total_exchange_g10 = Math.round(pay_total_exchange_g10 * 1000000) / 1000000;
            $('#pay_total_exchange_g10').html(pay_total_exchange_g10.toLocaleString('en-US'));
        }
        function calcTotalSaleOrder() {
            total_sale_exchange_g10 = 0;
            total_sale_wage = 0;
            let debt_exchange_g10 = $('#exchange_g10').val() ? Number($('#exchange_g10').val().replace(/,/g, '')) : 0;
            total_sale_exchange_g10 = debt_exchange_g10 + total_order.exchange_g10 - return_total.exchange_g10 - pay_total_exchange_g10;
            total_sale_exchange_g10 = Math.round(total_sale_exchange_g10 * 1000000) / 1000000;
            let debt_wage = $('#wage').val() ? Number($('#wage').val().replace(/,/g, '')) : 0;
            let pay_total_wage = $('#pay_total_wage').val() ? Number($('#pay_total_wage').val().replace(/,/g, '')) : 0;
            total_sale_wage = debt_wage + total_order.wage - return_total.wage -  pay_total_wage;
            total_sale_wage = Math.round(total_sale_wage * 1000000) / 1000000;
            $('#total_sale_exchange_g10').html(total_sale_exchange_g10.toLocaleString('en-US'));
            $('#total_sale_wage').html(total_sale_wage.toLocaleString('en-US'));
        }
        function validate() {
            console.log('validate');
            let valid = true;
            $('#form input').removeClass('invalid');
            if($('#customer_id').val()) {
                if(!$('#customer_code').val()){
                    valid = false;
                    $('#customer_code').addClass('invalid');
                }
                if(!$('#customer_name').val()){
                    valid = false;
                    $('#customer_name').addClass('invalid');
                }
            } else {
                if(!$('#customer_code').val()){
                    valid = false;
                    $('#customer_code').addClass('invalid');
                }
                if(!$('#customer_name').val()){
                    valid = false;
                    $('#customer_name').addClass('invalid');
                }
                if(!$('#customer_address').val()){
                    valid = false;
                    $('#customer_address').addClass('invalid');
                }
                if(!$('#customer_phone').val()){
                    valid = false;
                    $('#customer_phone').addClass('invalid');
                }
            }
            if(!$('#order_date').val()){
                valid = false;
                $('#order_date').addClass('invalid');
            }
            if(!$('#actual_weight').val()){
                valid = false;
                $('#actual_weight').addClass('invalid');
            }
            // if(order_pays.length > 0 && !$('#pay_total_wage').val()){
            //     valid = false;
            //     $('#pay_total_wage').addClass('invalid');
            // } validate ở dưới
            if(!$('#pay_date').val()){
                valid = false;
                $('#pay_date').addClass('invalid');
            }
            order_returns.forEach(function (detail, index) {
                if(    detail.qty > 0
                    || detail.total_weight > 0
                    || detail.gem_weight > 0
                    || detail.gold_weight > 0
                    || detail.gold_age > 0
                    || detail.wage > 0 ){
                    if(detail.qty == 0){
                        valid =  false;
                        $(`#return${detail.type}_qty`).addClass('invalid');
                    }
                    if(detail.total_weight == 0){
                        valid =  false;
                        $(`#return${detail.type}_total_weight`).addClass('invalid');
                    }
                    // gem_weight có thể không nhập, vì có món hàng không gắn đá
                    // if(detail.gem_weight == 0){
                    //     valid =  false;
                    //     $(`#return${detail.type}_gem_weight`).addClass('invalid');
                    // }
                    if(detail.gold_weight == 0){
                        valid =  false;
                        $(`#return${detail.type}_gold_weight`).addClass('invalid');
                    }
                    if(detail.gold_age == 0){
                        valid =  false;
                        $(`#return${detail.type}_gold_age`).addClass('invalid');
                    }
                    if(detail.wage == 0){
                        valid =  false;
                        $(`#return${detail.type}_wage`).addClass('invalid');
                    }
                }
            });
            order_pays.forEach(function (detail, index) {
                switch (detail.pay_method) {
                    case 1:
                    case 2:
                        if(detail.gold_weight == 0){
                            valid =  false;
                            $(`#pay${index}_gold_weight`).addClass('invalid');
                        }
                        if(detail.gold_age == 0){
                            valid =  false;
                            $(`#pay${index}_gold_age`).addClass('invalid');
                        }
                        break;
                    case 3:
                        if(detail.money == 0){
                            valid =  false;
                            $(`#pay${index}_money`).addClass('invalid');
                        }
                        if(detail.converted_price == 0){
                            valid =  false;
                            $(`#pay${index}_converted_price`).addClass('invalid');
                        }
                        break;
                    case 4:
                    case 5:
                        if(detail.total_weight == 0){
                            valid =  false;
                            $(`#pay${index}_total_weight`).addClass('invalid');
                        }
                        // gem_weight có thể không nhập, vì có món hàng không gắn đá
                        // if(detail.gem_weight == 0){
                        //     valid =  false;
                        //     $(`#pay${index}_gem_weight`).addClass('invalid');
                        // }
                        if(detail.gold_age == 0){
                            valid =  false;
                            $(`#pay${index}_gold_age`).addClass('invalid');
                        }
                        break;
                    default:
                        valid = false;
                        $(`#pay${index}_pay_method`).addClass('invalid');
                        break;
                }
            });
            if(!valid) {
                swal("Thông báo", "Dữ liệu chưa được nhập đầy đủ, vui lòng kiểm tra lại.", "warning");
            }else{
                // let total_wage_need_pay = 0;
                // let reduce = $('#reduce').val() ? Number($('#reduce').val().replace(/,/g, '')) : 0;
                // let pay_total_wage = $('#pay_total_wage').val() ? Number($('#pay_total_wage').val().replace(/,/g, '')) : 0;
                // order_details.forEach(function (detail, index) {
                //     total_wage_need_pay += (detail.retail_machining_fee?detail.retail_machining_fee:0)
                //         - (detail.retail_machining_fee?detail.retail_machining_fee:0)
                //         * (detail.discount_machining_fee?detail.discount_machining_fee:0)/100;
                // });
                // total_wage_need_pay = total_wage_need_pay - reduce - return_total_wage;
                // if(pay_total_wage < total_wage_need_pay){
                //     valid = false;
                //     $('#pay_total_wage').addClass('invalid');
                //     swal("Thông báo", "Chưa thu tiền công.\nBạn cần thu đủ " + total_wage_need_pay.toLocaleString('en-US') +"(đ) tiền công, hoặc nhập Lý do chưa thu hết công nợ cũ/tiền công.", "warning");
                // }else {
                    valid = valid && valid_actual_weight(true);
                // }
            }
            return valid;
        }
        function valid_actual_weight(show_alert) {
            let valid = true;
            let margin = 0;
            let total_weight_calc = 0;
            order_details.forEach(function (detail, index) {
                total_weight_calc += detail.total_weight + stamp_weight;
            });
            let actual_weight = $('#actual_weight').val() ? Number($('#actual_weight').val().replace(/,/g, '')) : 0;
            if(order_details.length <= 50){
                margin = 0.02;
            } else if(order_details.length <= 100)
            {
                margin = 0.03;
            } else // if(order_details.length > 100)
            {
                margin = 0.04;
            }
            $(`#actual_weight`).removeClass('invalid');
            if (actual_weight < (total_weight_calc - margin)) {
                valid = false;
                if(show_alert) {
                    $(`#actual_weight`).addClass('invalid');
                    swal("Thông báo", "Tổng TL cao hơn Tổng cân thực tế", "warning");
                }
            } else if (actual_weight > (total_weight_calc + margin)) {
                valid = false;
                if(show_alert) {
                    $(`#actual_weight`).addClass('invalid');
                    swal("Thông báo", "Tổng TL thấp hơn Tổng cân thực tế", "warning");
                }
            }
            $('#check_actual_weight').removeClass('btn-warning');
            $('#check_actual_weight').removeClass('btn-success');
            if(valid){
                $('#check_actual_weight').addClass('btn-success');
                $('#check_actual_weight').html('<i class="fa fa-check"></i>');
                $('.hide_invalid_actual_weight').show();
            } else {
                $('#check_actual_weight').addClass('btn-warning');
                $('#check_actual_weight').html('<i class="fa fa-question"></i>');
                $('.hide_invalid_actual_weight').hide();
            }
            return valid;
        }
        function submit() {
            $('#save_button').hide();
            if(validate()){
                $('.loading').show();
                //$('#form input').attr('readonly', true);
                calcTotalSaleOrder();
                $.ajax({
                    method: "POST",
                    url: '{{CRUDBooster::mainpath('add-save')}}',
                    data: {
                        order: {
                            id: null,
							order_type: 1, // đơn hàng nhanh
                            customer_id: $('#customer_id').val() ? Number($('#customer_id').val()) : null,
                            debt_date: $('#debt_date').val() ? moment($('#debt_date').val(), 'DD/MM/YYYY').format('YYYY-MM-DD') : null,
                            days_diff: Number($('#days_diff').val() ? $('#days_diff').val() : 0),
                            wage: Number($('#wage').val() ? $('#wage').val() : 0), // tiền công trong bảng công nợ
                            exchange_g10: Number($('#exchange_g10').val() ? $('#exchange_g10').val().replace(/,/g, '') : 0), // Q10 trong bảng công nợ
                            saler_id: Number('{{CRUDBooster::myId()}}'),
                            order_date: moment($('#order_date').val(), 'DD/MM/YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss'),
                            order_no: null,
                            gold_age_1: Number($('#gold_age_1').val() ? $('#gold_age_1').val().replace(/,/g, '') : 0),
                            gold_age_2: Number($('#gold_age_2').val() ? $('#gold_age_2').val().replace(/,/g, '') : 0),
                            gold_age_3: Number($('#gold_age_3').val() ? $('#gold_age_3').val().replace(/,/g, '') : 0),
                            sampling_discount: Number($('#sampling_discount').val() ? $('#sampling_discount').val().replace(/,/g, '') : 0),
                            pay_date: moment($('#pay_date').val(), 'DD/MM/YYYY').format('YYYY-MM-DD'),
                            pay_total_wage: Number($('#pay_total_wage').val() ? $('#pay_total_wage').val().replace(/,/g, '') : 0),
							actual_weight: Number($('#actual_weight').val() ? $('#actual_weight').val().replace(/,/g, '') : 0),
                            reduce: Number($('#reduce').val() ? $('#reduce').val().replace(/,/g, '') : 0),
                            total_exchange_g10: total_sale_exchange_g10, // tổng vàng quy 10
                            total_wage: total_sale_wage, // tổng tiền công
                            reason_pay_not_enough: $('#reason_pay_not_enough').val(),
                            other_orders: $('#other_orders').val()
                        },
                        customer: {
                            tmp_code: $('#customer_code').val(),
                            name: $('#customer_name').val(),
                            phone: $('#customer_phone').val(),
                            address: $('#customer_address').val(),
                            import: 0,
                        },
                        order_details: order_details,
                        order_returns: order_returns,
                        order_pays: order_pays,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    async: true,
                    success: function (data) {
                        if (data) {
                            readOnlyAll = true;
                            $('#order_no').val(data.order_no);
                            $('#print_invoice').show();
                            $('#save_button').hide();
                            $('#form input').attr('readonly', true);
                            $('#form textarea').attr('readonly', true);
                            $('#form select').attr('disabled', true);
                            // $('#print_invoice').attr('href', '{{CRUDBooster::mainpath()}}/' + data.id);
                            order_id = data.id;
                        } else {
                            $('#bar_code').val(null);
                            swal("Thông báo", "Không tìm thấy mã " + bar_code, "warning");
                        }
                        $('.loading').hide();
                    },
                    error: function (request, status, error) {
                        $('.loading').hide();
                        swal("Thông báo", "Có lỗi xãy ra khi lưu dữ liệu, vui lòng thử lại.", "error");
                        $('#save_button').show();
                    }
                });
            } else {
                $('#save_button').show();
			}
        }
        function popupWindow(url,windowName) {
            window.open(url,windowName,'height=500,width=600');
            return false;
        }
        function printInvoice() {
            if(order_id) {
                popupWindow("{{action('AdminGoldSaleOrdersController@getPrintInvoice')}}?id=" + order_id,"print");
            }else{
                alert("Bạn không thể in hóa đơn nếu chưa lưu đơn hàng!");
            }
        }
	</script>
@endpush