@extends('layout')
@section('content')


@push('head')
<style>
.fixed {
  width:100%;
  table-layout: fixed;
  border-collapse: collapse;
  border: 1px solid #dddddd;
  background: #fafafa;
}
.fixed th {
  text-decoration: underline;
}
.fixed th,
.fixed td {
  padding: 5px;
  text-align: left;
  min-width: 10px;
}


.fixed thead {
  background-color: red;
  color: #fdfdfd;
}
.fixed thead tr {
  display: block;
  position: relative;
}
.fixed tbody {
  display: block;
  overflow: auto;
  width: 100%;
  height: 340px;
  overflow-y: scroll;
    overflow-x: hidden;
}
.fixed tbody tr:nth-child(odd) td {
	background: #FFCF79;
}
.fixed tbody tr:nth-child(even) td {
	background: #ffedce;
}

</style>
@endpush



<div class="row">
	
	<div class="col-lg-5">
		
		<div class="form-group">
			<input type="text" class="form-control input-lg" autofocus onkeyup="scanBarcode()" placeholder="Barcode Scanner Here..." id="input_barcode_scanner" style="text-transform: uppercase;" />
		</div>
		<!-- /.form-group -->
		<form method='post' id="form_pos">
		{!! csrf_field() !!}
		<table id='table_pos' class="fixed">
			<thead>
				<tr bgcolor="#00A65A" style="color: #fff">
					<th width="50px">X</th>
					<th width="270px">PRODUCT</th>
					<th width="100px">QTY</th>
					<th width="100px">PRICE</th>
				</tr>
			</thead>
			<tbody>
				

			</tbody>
		</table>
		<!-- /.table table-striped -->

		<table class="table" style="color: #92CD00">
			<tbody>
				<tr bgcolor="#2C6700">
					<th>Total Items</th><td id="tr_total_items">0</td>
					<th>Total</th><td id="tr_total">0</td>
				</tr>
				<tr bgcolor="#2C6700">
					<th>Discount (%)</th><td><a href="#modal-discount" id="btn-discount" data-toggle='modal' class="btn btn-primary">0</a></td>
					<th>Tax (%)</th><td><a href="#modal-tax" id="btn-tax" data-toggle='modal' class="btn btn-primary">10</a></td>
				</tr>
				<tr bgcolor="#E5E4D7" style="color: red;border: 2px dashed red">
					<th valign="center" style="vertical-align: middle;text-align: center;text-decoration: underline;">
						Total Payable
					</th>
					<th colspan="3" id="tr_total_pay" style="vertical-align: middle;font-size: 30px;text-align: center;">
						0
					</th>
				</tr>
			</tbody>
		</table>
		<!-- /.table -->

		<div class="row">
			<div class="col-lg-6">
				<a href="javascript:;" onclick="clearCart()" class="btn btn-lg btn-block btn-danger">CANCEL</a>
			</div>
			<!-- /.col-lg-6 -->
			<div class="col-lg-6">
				<input type="hidden" name="sub_total" />
				<input type="hidden" name="discount" />
				<input type="hidden" name="tax" />
				<input type="hidden" name="grand_total" />
				<input type="hidden" name="customer_id" />
				<input type="hidden" name="customer_type" />
				<a href="javascript:;" onclick="showDone()" class="btn btn-lg btn-block btn-success">DONE</a>
			</div>
			<!-- /.col-lg-6 -->
		</div>
		<!-- /.row -->
		</form>

	</div>
	<!-- /.col-lg-4 -->
	<div class="col-lg-7">
		
		<div style="border: 1px solid #dddddd;background: #fafafa;padding: 5px;height: 645px;overflow: auto;overflow-x: hidden;">
		<div class="categories" style="border-bottom: 1px solid #dddddd;padding-bottom: 10px;height: 65px;overflow-x: scroll;
    overflow-y: hidden;white-space:nowrap">
    		<a href="javascript:;" onclick="showProductsCategories({{$c->id}})" class="btn btn-lg btn-primary"><i class="glyphicon glyphicon-glass"></i> All</a> 
			<?php 
				$categories = DB::table('categories')->get();
			?>
			@foreach($categories as $c)
			<a href="javascript:;" onclick="showProductsCategories({{$c->id}})" class="btn btn-lg btn-warning"><i class="glyphicon glyphicon-glass"></i> {{$c->name}}</a> 
			@endforeach
		</div>
		<!-- /.category -->
		<div style="margin:5px 0px 5px 0px">
			<input type="text" onkeyup="findProducts()" id="product_keyword" class="form-control input-lg" placeholder="Search product" />
		</div>

		<div id="products">
			<?php 
				$products = DB::table('products')->orderby('name','asc')->get();
			?>
			<div class="row">						
				@foreach($products as $p)
					<div class="col-lg-3 padding-0">									
					<a href="javascript:;" 
					onclick='addToCart({{$p->id}},"{{$p->code}}","{{$p->name}}","{{$p->sell_price}}")' 
					data-id="{{$p->id}}" data-code="{{$p->code}}" data-name="{{$p->name}}" data-price="{{$p->sell_price}}" 
					class="btn-product category{{$p->categories_id}}" 
					title="{{$p->code}} - {{$p->name}}">
						<strong style="color: yellow">{{$p->name}}</strong><br/>
						{{$p->code}}<br/>
						{{ number_format($p->sell_price) }}
					</a>
					</div>
					<!-- /.col-lg-3 -->
				@endforeach
			</div>
			<!-- /.row -->			
		</div>
		<!-- /#products -->
		</div>
	</div>
	<!-- /.col-lg-8 -->
</div>
<!-- /.row -->

@push('bottom')

<div class="modal" id="modal-done">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><i class="glyphicon glyphicon-euro"></i> Done</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label for="">Customer Type</label>
					<p>
					<label class="radio-inline">
					  <input type="radio" name="customer_type" checked value="Walk In"> Walk In
					</label>
					<!-- /.inline-radio -->
					<label class="radio-inline">
					  <input type="radio" name="customer_type" value="Member"> Member
					</label>
					<!-- /.inline-radio -->
					</p>
					<p>
						<select id="modal-done-customer-id" disabled class="form-control select2" style="width: 100%">
							<option value="">Customer List</option>
							<?php 
								$customers = DB::table('customers')->orderby('name','asc')->get();
							?>
							@foreach($customers as $cust)
								<option value="{{$cust->id}}">{{$cust->code}} - {{$cust->name}}</option>
							@endforeach
						</select>
					</p>
				</div>
				<!-- /.form-group -->

				<div class="form-group">
					<label for="">Total Payable</label>
					<div id="modal-done-total-payable" style="
						color: red;
					    font-size: 30px;
					    font-weight: bold;
					    padding: 15px;
					    border: 3px dotted;
					    text-align: center;
					    background: #ffe3e0;">
						0
					</div>
				</div>
				<!-- /.form-group -->

				<div class="form-group">
					<label for="">Customer Pay Amount</label>
					<input type="number" class="form-control input-lg" id="customer_pay_amount" />
				</div>
				<!-- /.form-group -->

				<div class="form-group">
					<label for="">Return Amount</label>
					<div id="modal-done-return-amount" style="
						color: green;
					    font-size: 30px;
					    font-weight: bold;
					    padding: 15px;
					    border: 3px dotted;
					    text-align: center;
					    background: #e8ffe8;">
						0
					</div>
				</div>
				<!-- /.form-group -->
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" onClick="submitFormPos()" class="btn btn-lg btn-success"><i class="glyphicon glyphicon-print"></i> Done</button>
			</div>
		</div>
	</div>
</div>

<div class="modal" id="modal-discount">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Input Discount (%)</h4>
			</div>
			<div class="modal-body">
				<input type="number" autofocus min="0" max="100" class="form-control input-lg" id="modal-discount-input" />
			</div>
			<div class="modal-footer">				
				<button type="button" id="modal-discount-submit" class="btn btn-block btn-lg btn-primary">Submit</button>
			</div>
		</div>
	</div>
</div>
<div class="modal" id="modal-tax">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Input Tax (%)</h4>
			</div>
			<div class="modal-body">
				<input type="number" autofocus min="0" max="100" value="10" class="form-control input-lg" id="modal-tax-input" />
			</div>
			<div class="modal-footer">				
				<button type="button" id="modal-tax-submit" class="btn btn-block btn-lg btn-primary">Submit</button>
			</div>
		</div>
	</div>
</div>
<div class="modal" id="modal-print">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Print Invoice</h4>
			</div>
			<div class="modal-body">			
				<div align="center">
					<a href="javascript:;" onclick="printInvoice()" class="btn btn-lg btn-success"><i class="fa fa-download"></i> Print Invoice</a>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	let cartTr = null;
	let totalItems = 0;
	let total = 0;
	let discount = 0;
	let discountPrice = 0;
	let tax = 10;
	let taxPrice = 0;
	let totalPay = 0;
	Number.prototype.formatMoney = function(c, d, t){
	var n = this, 
	    c = isNaN(c = Math.abs(c)) ? 2 : c, 
	    d = d == undefined ? "." : d, 
	    t = t == undefined ? "," : t, 
	    s = n < 0 ? "-" : "", 
	    i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))), 
	    j = (j = i.length) > 3 ? j % 3 : 0;
	   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
	 };

	 $(function() {
	 	$.notify("Press F11 to make full screen mode","info");

	 	$('input[name=customer_type]').click(function() {
	 		console.log($(this).val());
	 		if( $(this).val() == 'Member') {
	 			$('#modal-done-customer-id').prop('disabled',false);
	 		}else{
	 			$('#modal-done-customer-id').prop('disabled',true);
	 		}
	 	})

	 	$('#modal-discount').on('shown.bs.modal',function() {
	 		$(this).find('#modal-discount-input').focus();
	 	})
	 	$('#modal-discount-input').keyup(function(e) {
	 		if(e.which == 13) {
	 			discount = $('#modal-discount-input').val();
	 			if(parseInt(discount)>100) {
	 				alert("Please enter bellow than 100");
	 				return false;
	 			}
	 			$('#btn-discount').text(discount);
	 			$('#modal-discount').modal('hide');
	 			calculateTotal();
	 		}
	 	})
	 	$('#modal-discount-submit').click(function() {
	 		discount = $('#modal-discount-input').val();
 			if(parseInt(discount)>100) {
 				alert("Please enter bellow than 100");
 				return false;
 			}
	 		$('#btn-discount').text(discount);
	 		$('#modal-discount').modal('hide');
	 		calculateTotal();
	 	})


	 	$('#modal-tax').on('shown.bs.modal',function() {
	 		$(this).find('#modal-tax-input').focus();
	 	})
	 	$('#modal-tax-input').keyup(function(e) {
	 		if(e.which == 13) {
	 			tax = $('#modal-tax-input').val();
	 			if(parseInt(tax)>100) {
	 				alert("Please enter bellow than 100");
	 				return false;
	 			}
	 			$('#btn-tax').text(tax);
	 			$('#modal-tax').modal('hide');
	 			calculateTotal();
	 		}
	 	})
	 	$('#modal-tax-submit').click(function() {
	 		tax = $('#modal-tax-input').val();
 			if(parseInt(tax)>100) {
 				alert("Please enter bellow than 100");
 				return false;
 			}
	 		$('#btn-tax').text(tax);
	 		$('#modal-tax').modal('hide');
	 		calculateTotal();
	 	})
	 	$('#customer_pay_amount').on('input',function() {
	 		var pay = parseInt($(this).val());
	 		if(!pay) {
	 			returnAmount = 0;
	 		}

	 		var returnAmount = totalPay-pay;
	 		console.log('return'+returnAmount);
	 		if(returnAmount<0) {
	 			returnAmount = Math.abs(returnAmount);
	 			$('#modal-done-return-amount').text(returnAmount.formatMoney(2));
	 		}else{
	 			$('#modal-done-return-amount').text(0.00);
	 		}
	 		
	 		
	 	})
	 })

	 var last_trans_id = null;

	function printInvoice() {
		if(last_trans_id) {			
			popitup("{{action('PosController@getPrintOrder')}}/"+last_trans_id,"print");
		}else{
			alert("Sorry there is no invoice yet!");
		}
	}

	function submitFormPos() {
		console.log('submitFormPos');
		$('#form_pos input[name=customer_id]').val( $('#modal-done #modal-done-customer-id').val() );
		$('#form_pos input[name=customer_type]').val( $('#modal-done input[name=customer_type]:checked').val() );

		$.notify("Saving data...",{globalPosition:"bottom right",className:"success"});
		$.ajax({
			data:$('#form_pos').serialize(),
			type:'POST',
			url:"{{action('PosController@postSubmitPos')}}",
			success:function(data) {
				$.notify("Transaction has been success",{globalPosition:"bottom right",className:"success"});
				clearCart();
				$('#modal-done').modal('hide');
				$('#modal-print').modal('show');				
				last_trans_id = data.trans_id;
				printInvoice();
			},
			error:function() {
				alert('Oops something went wrong with the system');
			}
		})
	}

	var newwindow = null;
	function popitup(url,windowName) {
       newwindow=window.open(url,windowName,'height=500,width=600');
       if (window.focus) {newwindow.focus()}
       return false;
     }

	function showDone() {
		$('#modal-done-total-payable').text(totalPay.formatMoney(2));
		$('#modal-done-return-amount').text('0.00');
		$('#modal-done').modal('show');
		$('#customer_pay_amount').val(0).focus();
	}

	let timeChecking = null;	
	function scanBarcode() {
		clearTimeout(timeChecking);
		var barcode = $('#input_barcode_scanner').val().trim().toLowerCase();
		if(barcode=='') return false;
		timeChecking = setTimeout(function() {
			var found = false;
			$('.btn-product').each(function() {
				var code = $(this).data('code').toLowerCase();
				if(code==barcode) {
					addToCart(
						$(this).data('id'),
						$(this).data('code'),
						$(this).data('name'),
						$(this).data('price')
						);
					found = true;					
				}
			})

			$('#input_barcode_scanner').val('').focus();
			if(found) {
				$("#input_barcode_scanner").notify("The product has been added to table", { elementPosition:"bottom left",className:"success"});
			}else{
				$("#input_barcode_scanner").notify("The product is not found, try again", { elementPosition:"bottom left",className:"error"});
			}			
		},400);
	}

	function clearCart() {
		$('#table_pos tbody tr').remove();
		calculateTotal();
		calculateTotalItems();
		$.notify("The table has been cleared!", { globalPosition:"bottom right",className:"success"});
	}

	function calculateTotalItems() {
		totalItems = 0;
		$('#table_pos tbody tr .cart_qty').each(function() {
			totalItems += parseInt($(this).text());
		})
		$('#tr_total_items').text(totalItems);
	}

	function getTotal() {
		total = 0;
		$('.cart_total').each(function() {
			total += parseInt( $(this).text() ) * parseInt( $(this).parent().find('.cart_qty').text() );
		})		
		return total;
	}

	function calculateTotal() {
		discount = parseFloat( $('#btn-discount').text() );
		tax = parseFloat( $('#btn-tax').text() );
		total = getTotal();

		discountPrice = total * discount/100;
		taxPrice = tax/100 * (total-discountPrice);
		totalPay = total - discountPrice + taxPrice;

		console.log(totalPay);

		$('#tr_total').text(total);
		$('#tr_total_pay').text(totalPay.formatMoney(2));

		$('input[name=sub_total]').val(total);
		$('input[name=discount]').val(discountPrice);
		$('input[name=tax]').val(taxPrice);
		$('input[name=grand_total]').val(totalPay);
	}

	function removeCart(id) {
		$("#cart_tr_"+id).remove();
		calculateTotalItems();
		calculateTotal();
	}
	function addToCart(id,code,name,price) {
		if( $('#cart_tr_'+id).length > 0) {
			$('#cart_qty_'+id).text( parseInt($('#cart_qty_'+id).text())+1 );
			$('#cart_tr_'+id+' .input_qty').val( parseInt($('#cart_qty_'+id).text())+1 );
		}else{
			cartTr = "<tr id='cart_tr_"+id+"'>"+
					"<td width='50px'><a href='javascript:;' onclick='removeCart("+id+")'><i class='glyphicon glyphicon-remove'></i></a></td>"+
					"<td width='280px'>"+code+" - "+name+"</td>"+
					"<td width='100px' class='cart_qty' id='cart_qty_"+id+"'>1</td>"+
					"<td width='100px' class='cart_total' align=\"right\">"+price+" "+
					"<input type='hidden' class='input_id' name='id[]' value='"+id+"'/>"+
					"<input type='hidden' class='input_qty' name='quantity[]' value='1'/>"+
					"</td>"+
					"</tr>";
			$('#table_pos tbody').append(cartTr);
		}
		calculateTotalItems();
		calculateTotal();
	}

	function showProductsCategories(id) {
		if(id==null) {
			$('#products .col-lg-3').show();
		}else{
			$('#products .col-lg-3').hide();
			$('#products a.category'+id).each(function() {
				$(this).parent().show();
			})
		}		
	}
	function findProducts() {
		let keyword = $('#product_keyword').val();
		let findBoolean = -1;
		console.log(keyword);
		$('#products .col-lg-3').hide();

		$('#products a').each(function() {
			let title = $(this).attr('title');
			findBoolean = title.toLowerCase().indexOf(keyword);
			if(findBoolean>-1) {
				$(this).parent().show();
			}else{
				$(this).parent().hide();
			}
		})
	}
</script>
@endpush

@push('head')
	<style>
		::-webkit-scrollbar {
		    width: 5px;
		}
		::-webkit-scrollbar:horizontal {
		    width: 5px;
		}
		 
		::-webkit-scrollbar-track {
		    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
		}
		 
		::-webkit-scrollbar-thumb {
		  background-color: darkgrey;
		  outline: 1px solid slategrey;
		}
		.padding-0{
		padding-right:2px;
		padding-left:2px;
		}
		.btn-product {
			display: inline-block;
			background: #0099CC;
			padding: 10px;
			text-align: center;
			width: 100%;
			height: 80px;
			
			white-space: nowrap;
			  overflow: hidden;
			  text-overflow: ellipsis;
			color: #ffffff;
		}
		.btn-product:hover {
			text-decoration: none;
			color: #ffffff;
			background: #0085b2;
		}
	</style>
@endpush


@endsection