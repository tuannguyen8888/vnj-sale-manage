<!-- First you need to extend the CB layout -->
@extends('crudbooster::admin_template')
@section('content')
	<!-- Your custom  HTML goes here -->
	<!-- Your html goes here -->
	<div>
		<p><a title="Return" href="{{CRUDBooster::mainpath()}}"><i class="fa fa-chevron-circle-left "></i> &nbsp; Quay lại danh sách Đơn Hàng</a></p>
		<div class='panel panel-default'>
			<div class='panel-heading'>
				<strong><i class="fa fa-diamond"></i> {{$mode=='new'?'Tạo mới Phiếu Chuyển Kho':(mode=='edit'?'Sửa Phiếu Chuyển Kho':'Chi tiết Phiếu Chuyển Kho')}}</strong>
			</div>

			<div class="panel-body" id="parent-form-area">

				<form method='post' action='{{CRUDBooster::mainpath('add-save')}}' id="form">
					<input type="hidden" name="id" id="id">
					<input type="hidden" name="saler_id" id="saler_id">
					<div class="col-sm-12">
						<div class="row">
							<label for="customer_code" class="control-label col-sm-1">Từ kho <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
							<div class="col-sm-5">
								<div class="input-group">
									<span class="input-group-btn">
										<button type="button" class="btn btn-primary btn-flat" onclick="showModalFromStockId()"><i class="fa fa-search"></i></button>
									</span>
									<input required type="text" name="from_stock_name" id="from_stock_name" readonly class="form-control" placeholder="Kho nguồn">
									<input required type="hidden" name="from_stock_id" id="from_stock_id">
								</div>
							</div>
							<label for="customer_code" class="control-label col-sm-1">Đến kho <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
							<div class="col-sm-5">
								<div class="input-group">
									<span class="input-group-btn">
										<button type="button" class="btn btn-primary btn-flat" onclick="showModalToStockId()"><i class="fa fa-search"></i></button>
									</span>
									<input required type="text" name="to_stock_name" id="to_stock_name" readonly class="form-control" placeholder="Kho đích">
									<input required type="hidden" name="to_stock_id" id="to_stock_id">
								</div>
							</div>
						</div>
						<div class="row">
							<label class="control-label col-sm-1">Phân loại <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
							<div class="col-sm-5">
								<select class="form-control" id="tranfer_type" name="tranfer_type">
									<option value="0"></option>
									<option value="1">Chuyển Kho</option>
									<option value="2">Trả hàng</option>
								</select>
							</div>
							<label class="control-label col-sm-1">Lý do <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
							<div class="col-sm-5">
								<select class="form-control" id="reason_id" name="reason_id" style="width: 100%;">
								</select>
							</div>
							<label class="control-label col-sm-1 other_reason" style="display: none;">Lý do khác<span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
							<div class="col-sm-5 other_reason" style="display: none;">
								<input type="text" name="other_reason" id="other_reason" class="form-control" placeholder="Nhập lý do">
							</div>
						</div>
						<div class="row">
							<label class="control-label col-sm-1">Mã vạch </label>
							<div class="col-sm-5">
								<div class="input-group">
									<input type="text" name="bar_code" id="bar_code" class="form-control"
										   placeholder="Quét mã vạch" autocomplete="off" onkeyup="findProduct(event)"
										   style="background-color: rgba(251,240,83,0.52)">
									<span class="input-group-btn">
										<button type="button" class="btn btn-danger btn-flat" onclick="$('#modal-datamodal-delete-barcode').modal('show')"><i class="fa fa-remove"></i></button>
									</span>
								</div>
							</div>
							<label class="control-label col-sm-2">Tổng cân thực tế </label>
							<div class="col-sm-4">
								<div class="input-group">
									<input type="text" name="actual_weight" id="actual_weight" class="form-control money" required>
									<span class="input-group-btn">
											<button type="button" class="btn btn-warning btn-flat" id="check_actual_weight" onclick="valid_actual_weight(true);"><i class="fa fa-question"></i></button>
										</span>
								</div>
							</div>
						</div>
					</div>

					<div class="col-sm-12">
						<div id="header1" data-collapsed="false" class="header-title form-divider">
							<h4>
								<strong><i class="fa fa-list-alt"></i> Danh sách hàng</strong>
								{{--<span class="pull-right icon"><i class="fa fa-minus-square-o"></i></span>--}}
							</h4>
						</div>
						<div class="form-group header-group-1">
							<table id="table_order_details" class='table table-bordered' height=50>
								<thead>
								<tr class="bg-success">
									<th class="action">#</th>
									<th class="sort_no">STT</th>
									<th class="bar_code">Mã vạch</th>
									<th class="product_type_name">Loại vàng</th>
									<th class="product_name">Tên hàng</th>
									<th class="product_code">Mã hàng</th>
									<th class="age">Tuổi</th>
									<th>Tổng TL</th>
									<th>TL đá</th>
									<th>TL vàng</th>
									<th>Quy 10</th>
									<th>Công</th>
									<th>CK công (%)</th>
									<th>Nhóm</th>
									<th>Kho</th>
								</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
								<tr class="bg-gray-active">
									<th colspan="7" class="total_label">Tổng cộng</th>
									<th id="total_order_total_weight" class="text-right">0</th>
									<th id="total_order_gem_weight" class="text-right">0</th>
									<th id="total_order_gold_weight" class="text-right">0</th>
									<th id="total_order_exchange_g10" class="text-right">0</th>
									<th id="total_order_wage" class="text-right">0</th>
									<th></th>
									<th></th>
									<th></th>
								</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</form>
			</div>
			<div class="box-footer" style="background: #F5F5F5">

				<div class="form-group">
					<label class="control-label col-sm-2"></label>
					<div class="col-sm-10">
						<a href="{{CRUDBooster::mainpath()}}" class="btn btn-default" id="goBack"><i class="fa fa-chevron-circle-left"></i> Quay về</a>
						@if($mode=='new' || $mode=='edit')
							<button id="save_button" class="btn btn-success" onclick="submit(true)"><i class="fa fa-save"></i> Lưu</button>
						@endif
						<a id="print_report_detail" style="display: none;cursor: pointer;" onclick="printReportDetail()" class="btn btn-primary"><i class="fa fa-print"></i> In Bảng kê chi tiết</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="modal-datamodal-stock_id" class="modal in" tabindex="-1" role="dialog" aria-hidden="false" style="display: none; padding-right: 7px;">
		<div class="modal-dialog modal-lg " role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title"><i class="fa fa-search"></i> Browse Data | Kho Hàng</h4>
				</div>
				<div class="modal-body">
					<iframe id="iframe-modal-stock_id" style="border:0;height: 430px;width: 100%"></iframe>
				</div>

			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div>
	<div id="modal-datamodal-delete-barcode" class="modal in" tabindex="-1" role="dialog" aria-hidden="false" style="display: none; padding-right: 7px;">
		<div class="modal-dialog modal-md " role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title"><i class="fa fa-search"></i> Delete Barcode | Hàng đang chuyển kho</h4>
				</div>
				<div class="modal-body">
					<form onsubmit="event.preventDefault();" id="form" style="min-height: 50px;">
						<input type="hidden" name="id" id="id">
						<input type="hidden" name="saler_id" id="saler_id">
						<div class="col-sm-12">
							<div class="row">
								<label class="control-label col-sm-2">Mã vạch </label>
								<div class="col-sm-10">
									<input type="text" name="bar_code_delete" id="bar_code_delete" class="form-control"
										   placeholder="Quét mã vạch để xóa" autocomplete="off" onkeyup="removeBarcode(event)"
										   style="background-color: rgba(251,17,24,0.23)">
								</div>
							</div>
						</div>
					</form>
				</div>

				<div class="modal-footer text-center">
					<button type="button" class="btn btn-info" data-dismiss="modal" aria-label="Close">Xong</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div>
	<div class="loading"></div>
@endsection

@push('bottom')
	<style>
		#table_order_details tbody {
			display:block;
			max-height:200px;
			overflow:auto;
		}
		#table_order_details thead, #table_order_details tfoot, #table_order_details  tbody tr {
			display:table;
			width:100%;
			table-layout:fixed;
		}
		#table_order_details thead, #table_order_details tfoot {
			width: 100%
		}
		#table_order_details table {
			width:100%;
		}
		#table_order_details .action{
			width: 30px;
		}
		#table_order_details .sort_no{
			width: 60px;
		}
		#table_order_details .bar_code{
			width: 130px;
		}
		#table_order_details .product_type_name{
			width: 80px;
		}
		#table_order_details .product_name{
			width: 80px;
		}
		#table_order_details .product_code{
			width: 100px;
		}
		#table_order_details .age{
			width: 70px;
		}
		#table_order_details .total_label{
			width: 550px;
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
		.hide_invalid_actual_weight{
			display: none;
		}
	</style>

	<script type="application/javascript">
        // table_order_details = null;
        stamp_weight = Number('{{CRUDBooster::getSetting('trong_luong_tem')}}')
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
        invalid_order = false;// đánh dấu đơn hàng vi phạm
        lastTimeScanBarCode = moment();
        autosave_add_new_detail = null;
        autosave_remove_detail = null;
        $(function(){
            sessionTimeout = Number('{{Config::get('session.lifetime') * 60}}');
			setTimeout(function () {
                swal(
                    	{
							title: "Thông báo",
							text: "Phiên đăng nhập của bạn đã hết hạn, vui lòng đăng nhập lại.",
							type: "danger"
						},
						function() {
                    	    window.location = '{{CRUDBooster::adminPath()}}/logout';
                    	}
					);
            }, sessionTimeout * 1000);
            //$('.money').autoNumeric('init', optionNumberInput);
            AutoNumeric.multiple('.money', optionNumberInput);
            let tranfer_type = getUrlParameter('tranfer_type');
            if(tranfer_type) {
                $('#tranfer_type').val(tranfer_type);
                $('#tranfer_type').prop('disabled', true);
			}else{
                $('#tranfer_type').prop('disabled', false);
            }
            $('#reason_id').select2({
                splaceholder: 'Chọn lý do',
                minimumInputLength: 0,
                ajax: {
                    url: '{{Route("AdminGoldStocksTransferReasonControllerPostSearchReason")}}',
                    type: 'POST',
                    dataType: 'json',
                    data: function (para) {
                        para.tranfer_type = $('#tranfer_type').val();
                        para.page_limit = 10;
                        para.page = para.page || 1;
                        return para;
                    },
                    //results: function (data, page) {
                    //    return { results: data };
                    //},
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        if(!data) {
                            data = [];
                        }
                        data.push({id: -1, text: "Lý do khác (tự nhập)"});
                        return {
                            results: data,
                            pagination: {
                                more: data.length >= 10
                            }
                        };
                    }
                },
            }).change(function () {
                if($('#reason_id').val() == -1) {
                    $('.other_reason').show();
                    $('.other_reason').prop("required", true);
				}else{
                    $('.other_reason').val('');
                    $('.other_reason').hide();
                    $('.other_reason').prop("required", false);
                }
            });
		});
        function getUrlParameter(sParam) {
            var sPageURL = decodeURIComponent(window.location.search.substring(1)),
                sURLVariables = sPageURL.split('&'),
                sParameterName,
                i;

            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');

                if (sParameterName[0] === sParam) {
                    return sParameterName[1] === undefined ? true : sParameterName[1];
                }
            }
        };

        function showModalFromStockId() {
            var stock_ids = '{{$FromStockId}}';
            var url = "{{action('AdminGoldStocksTransferController@getModalData')}}/gold_stocks_transfer/modal-data?table=gold_stocks&columns=id,name&name_column=FromStockId&where=deleted_at+is+null+and+id+in+(" + (stock_ids && stock_ids.trim() != '' ? stock_ids : '0') + ")&columns_name_alias=Tên kho";
            $('#iframe-modal-stock_id').attr('src',url);
            $('#modal-datamodal-stock_id').modal('show');
        }
        function showModalToStockId() {
            var url = "{{action('AdminGoldStocksTransferController@getModalData')}}/gold_stocks_transfer/modal-data?table=gold_stocks&columns=id,name&name_column=ToStockId&where=deleted_at+is+null&select_to=to_stock_id:id,name:name&columns_name_alias=Tên kho";
            $('#iframe-modal-stock_id').attr('src',url);
            $('#modal-datamodal-stock_id').modal('show');
        }
        function hideModalstock_id() {
            $('#modal-datamodal-stock_id').modal('hide');
        }
        function selectAdditionalDataFromStockId(select_to_json) {
            console.log('selectAdditionalDataFromStockId');
            if(select_to_json.datamodal_id){
                $('#from_stock_id').val(select_to_json.datamodal_id);
                $('#from_stock_name').val(select_to_json.datamodal_label);
            }
            hideModalstock_id();
        }
        function selectAdditionalDataToStockId(select_to_json) {
            console.log('selectAdditionalDataToStockId');
            if(select_to_json.datamodal_id){
                $('#to_stock_id').val(select_to_json.datamodal_id);
                $('#to_stock_name').val(select_to_json.datamodal_label);
            }
            hideModalstock_id();
        }
		// productFinding = false;
        function findProduct(event) {
            if(event == null || event.keyCode == 13) {
                if(moment().diff(lastTimeScanBarCode, 's') >= 1) // productFinding &&
                {
                    lastTimeScanBarCode = moment();
                    // productFinding = true;
                    let bar_code = $('#bar_code').val();
                    let added = false;
                    order_details.forEach(function (detail, index) {
                        if (detail.bar_code == bar_code) {
                            added = true;
                            $('#bar_code').val(null);
                            // swal("Thông báo", "Sản phẩm [" + bar_code + "] đã được thêm.", "info");
                        }
                    });
                    if (bar_code && !added) {
                        // $('.loading').show();
						setTimeout(function () {
                            $('#bar_code').val(null);
                        },200);
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
                                    } else {
                                        let tmp_added = false;
                                        order_details.forEach(function (detail, index) {
                                            if (detail.bar_code == data.product.bar_code) {
                                                tmp_added = true;
                                            }
                                        });
                                        if(!tmp_added) {
                                            data.product.no = order_details ? order_details.length + 1 : 1;
                                            switch (data.product.product_code.toUpperCase().trim().substr(0, 2)) {
                                                case 'V_': //hàng khác
                                                    data.product.age = $('#gold_age_3').val() ? Number($('#gold_age_3').val().replace(/,/g, '')) : 0;
                                                    break;
                                                case 'B_': //đồ bộng
                                                    data.product.age = $('#gold_age_2').val() ? Number($('#gold_age_2').val().replace(/,/g, '')) : 0;
                                                    break;
                                                default: //đồ đúc
                                                    data.product.age = $('#gold_age_1').val() ? Number($('#gold_age_1').val().replace(/,/g, '')) : 0;
                                            }
                                            data.product.exchange_g10 = data.product.gold_weight * data.product.age / 100; // data.product.qty *
                                            data.product.discount_machining_fee = $('#sampling_discount').val() ? Number($('#sampling_discount').val().replace(/,/g, '')) : 0;
                                            order_details.push(data.product);
                                            autosave_add_new_detail = data.product;
                                            addNewTransferDetail(data.product);
                                            $('#bar_code').val(null);
                                            // table_order_details.data = order_details;
                                            // console.log('order_details = ', order_details);
                                            // console.log('table_order_details.data = ', table_order_details.data);
                                            // console.log('table_order_details = ', table_order_details);
                                            // table_order_details.fnDraw();

                                            calcTotalOfSaleOrderDetails();
                                            calcTotalSaleOrder();
                                        }
                                    }
                                } else {
                                    $('#bar_code').val(null);
                                    swal("Thông báo", "Không tìm thấy mã " + bar_code, "warning");
                                }
                                // productFinding = false;
                                // $('.loading').hide();
                            },
                            error: function (request, status, error) {
                                // $('.loading').hide();
								console.log('Lỗi khi tìm sản phẩm ', [request, status, error]);
                                swal("Thông báo", "Có lỗi xãy ra khi tải dữ liệu, vui lòng thử lại.", "warning");
                                // productFinding = false;
                            }
                        });
                    } else {
                        // productFinding = false;
					}
                }
            }
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
                total_order.wage += detail.retail_machining_fee ? detail.retail_machining_fee : 0;
            });
            $('#total_order_total_weight').html(total_order.total_weight.toLocaleString('en-US'));
            $('#total_order_gem_weight').html(total_order.gem_weight.toLocaleString('en-US'));
            $('#total_order_gold_weight').html(total_order.gold_weight.toLocaleString('en-US'));
            $('#total_order_exchange_g10').html(total_order.exchange_g10.toLocaleString('en-US'));
            $('#total_order_wage').html(total_order.wage.toLocaleString('en-US'));
        }
        function addNewTransferDetail(dataRow) {
            if(readOnlyAll){
                swal("Thông báo", "Bạn không thể thêm sản phẩm sau khi đã lưu đơn hàng, hãy tạo đơn hàng mới.", "warning");
                return;
			}
            let html = `<tr id="order_detail_` + dataRow.id + `">` +
                `<th class="action text-center"><a style="cursor: pointer;" onclick="removeTransferDetail(` + dataRow.id + `)"><i class="fa fa-remove text-red"></i></a></th>` +
                `<th class="sort_no text-right" id="no_` + dataRow.id + `">${dataRow.no}</th>` +
                `<th class="bar_code">${dataRow.bar_code}</th>` +
                `<th class="product_type_name text-center">${dataRow.product_type_name}</th>` +
                `<th class="product_name text-center">${dataRow.product_name}</th>` +
                `<th class="product_code text-center">${dataRow.product_code}</th>` +
                `<th class="age text-right" id="age_${dataRow.id}">${dataRow.age.toLocaleString('en-US')}%</th>` +
                `<th class="text-right">${dataRow.total_weight.toLocaleString('en-US')}</th>` +
                `<th class="text-right">${dataRow.gem_weight.toLocaleString('en-US')}</th>` +
                `<th class="text-right">${dataRow.gold_weight.toLocaleString('en-US')}</th>` +
                `<th class="text-right" id="exchange_g10_${dataRow.id}">${dataRow.exchange_g10.toLocaleString('en-US')}</th>` +
                `<th class="text-right">${dataRow.retail_machining_fee.toLocaleString('en-US')}</th>` +
                `<th class="text-right" id="discount_machining_fee_${dataRow.id}">${dataRow.discount_machining_fee?dataRow.discount_machining_fee.toLocaleString('en-US')+'%':''}</th>` +
                // `<th class="no-padding" style="width: 100px;"><input id="discount_machining_fee_` + dataRow.id + `" type="text" class="form-control money" value="${dataRow.discount_machining_fee}" onchange="discount_machining_fee_change(` + dataRow.id + `)"></th>` +
                `<th>${dataRow.product_group_name}</th>` +
                `<th>${dataRow.stock_name}</th>` +
                `</tr>`;
			$('#table_order_details tbody').append(html);
			// let html_id = "#discount_machining_fee_" + dataRow.id;
			// setTimeout(function () {
            //     // $(html_id).autoNumeric('init', optionNumberInput);
            //     new AutoNumeric(html_id, optionNumberInput);
            // },100);
            valid_actual_weight();
            $('#table_order_details tbody').animate({scrollTop:9999999}, 'slow');
        }

        function removeBarcode(event){
            if(readOnlyAll){
                // swal("Thông báo", "Bạn không thể thêm sản phẩm sau khi đã lưu đơn hàng, hãy tạo đơn hàng mới.", "warning");
                return;
            }
            if (event == null || event.keyCode == 13) {
                let bar_code_delete = $('#bar_code_delete').val();
                if(order_details && order_details.length) {
                    for (let i = 0; i < order_details.length; i++) {
                        if(order_details[i].bar_code == bar_code_delete) {
                            removeTransferDetail(order_details[i].id);
                        }
                    }
                }else{
                    console.log('Chưa có sản phẩm được quét barcode, không thể xóa');
                }
            }
        }
        function removeTransferDetail(id) {
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
                    autosave_remove_detail = removeDetail;
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

        function calcTotalSaleOrder() {
            total_sale_exchange_g10 = 0;
            total_sale_wage = 0;
            let debt_exchange_g10 = $('#exchange_g10').val() ? Number($('#exchange_g10').val().replace(/,/g, '')) : 0;
            total_sale_exchange_g10 = debt_exchange_g10 + total_order.exchange_g10 - return_total.exchange_g10 - pay_total_exchange_g10;
            total_sale_exchange_g10 = Math.round(total_sale_exchange_g10 * 1000000) / 1000000;
            let debt_wage = $('#wage').val() ? Number($('#wage').val().replace(/,/g, '')) : 0;
            let pay_total_wage = $('#pay_total_wage').val() ? Number($('#pay_total_wage').val().replace(/,/g, '')) : 0;
            let sampling_discount = $('#sampling_discount').val() ? Number($('#sampling_discount').val().replace(/,/g, '')) : 0;
            let reduce  = $('#reduce').val() ? Number($('#reduce').val().replace(/,/g, '')) : 0;
            total_sale_wage = debt_wage + (total_order.wage - total_order.wage * sampling_discount / 100 - reduce) - return_total.wage -  pay_total_wage;
            total_sale_wage = Math.round(total_sale_wage * 1000000) / 1000000;
            $('#total_sale_exchange_g10').html(total_sale_exchange_g10.toLocaleString('en-US'));
            $('#total_sale_wage').html(total_sale_wage.toLocaleString('en-US'));
            let pay_total = pay_total_wage;
            order_pays.forEach(function (detail, index) {
                if(detail.pay_method == 3) {
                    pay_total += isNaN(detail.money)? 0 : detail.money;
				}
            });
            $('#pay_total').html(pay_total.toLocaleString('en-US'));
        }
        function validate() {
			let valid = true;
            $('#form input').removeClass('invalid');
            if(!$('#actual_weight').val()){
                valid = false;
                $('#actual_weight').addClass('invalid');
            }
            if(!$('#from_stock_id').val()){
                valid = false;
                $('#from_stock_name').addClass('invalid');
            }
            if(!$('#to_stock_id').val()){
                valid = false;
                $('#to_stock_name').addClass('invalid');
            }
            if(!$('#tranfer_type').val()){
                valid = false;
                $('#tranfer_type').addClass('invalid');
            }
            if(!$('#reason_id').val()){
                valid = false;
                $('#reason_id').addClass('invalid');
            }else if($('#reason_id').val() == -1 && !$('#other_reason').val()){
                valid = false;
                $('#other_reason').addClass('invalid');
            }
            if(!valid) {
                swal("Thông báo", "Dữ liệu chưa được nhập đầy đủ, vui lòng kiểm tra lại.", "warning");
            }else{
                invalid_order = false;
                valid = valid && valid_actual_weight(true);
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
                margin = 0.04;
            } else // if(order_details.length > 100)
            {
                margin = 0.05;
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
        function getTransferHeader(finish) {
            return {
                id: $('#id').val() ? Number($('#id').val()) : null,
                from_stock_id: $('#from_stock_id').val() ? Number($('#from_stock_id').val()) : null,
                to_stock_id: $('#to_stock_id').val() ? Number($('#to_stock_id').val()) : null,
                tranfer_type: Number($('#tranfer_type').val() ? $('#tranfer_type').val() : 0),
                reason_id: Number($('#reason_id').val() ? $('#reason_id').val() : 0),
                new_reason: $('#reason_id').val() == -1 ? null : $('#other_reason').val(),
                total_wage: Number($('#total_wage').val() ? $('#total_wage').val() : 0),
                notes: $('#notes').val()
            };
		}
        function submit(finish) {
			if(!finish || validate()){ // nếu finish == false thì không validate
			    if(finish) {
                    $('#save_button').hide();
                    $('.loading').show();
                    //$('#form input').attr('readonly', true);
                    calcTotalSaleOrder();
                }
                $.ajax({
                    method: "POST",
                    url: '{{CRUDBooster::mainpath('add-save')}}',
                    data: {
                        tranfer_header: getTransferHeader(finish),
						tranfer_details: order_details,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    async: true,
                    success: function (data) {
                        $('.loading').hide();
                        if (data) {
							$('#id').val(data.transfer_id);
                            swal("Thông báo", "Đã chuyển kho thành công.", "info");
                            $('#goBack').click();
                        }
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
        function printReportDetail() {
            if(order_id) {
                popupWindow("{{action('AdminGoldSaleOrdersController@getPrintReportDetail')}}?id=" + order_id,"print");
            }else{
                alert("Bạn không thể in bảng kê chi tiết nếu chưa lưu đơn hàng!");
            }
        }
	</script>
@endpush