<?php

namespace App\Http\Controllers;

use Request;
use CRUDBooster;
use Session;
use DB;
use PDF;

class PosController extends Controller
{
    public function getIndex() {
        return redirect('/admin');
//    	if(!Session::has('employees_id')) CRUDBooster::redirect('login','Please sign in to enter the POS Area','info');
//    	return view('pos');
    }

    public function getLogin() {
        return redirect('/admin');
//    	if(Session::has('employees_id')) CRUDBooster::redirect('/','You have login in successfully','success');
//    	return view('login');
    }

    public function getPrintOrder($id_trans) {
        $data['id_trans'] = $id_trans;
        $pdf = PDF::loadView('export_pos',$data);
        return $pdf->stream('invoice.pdf');
    }

    public function postLogin() {
    	CRUDBooster::valid([
    		'username'=>'required',
    		'password'=>'required'
    	],'view');


    	$data = DB::table('employees')->where('username',g('username'))->first();
    	if($data) {
    		if(\Hash::check(g('password'),$data->password)) {
    			Session::put('employees_id',$data->id);
    			CRUDBooster::redirect('/','You have login in successfully','success');
    		}else{
    			CRUDBooster::redirect($_SERVER['HTTP_REFERER'],'The password is wrong','warning');
    		}
    	}else{
    		CRUDBooster::redirect($_SERVER['HTTP_REFERER'],'The username is not found','warning');
    	}

    }

    public function getLogout() {
    	Session::forget('employees_id');
    	CRUDBooster::redirect('login','Please sign in to enter the POS Area','info');
    }

    public function postSubmitPos() {
        $trans_id = null;
    	DB::transaction(function() use (&$trans_id) {
    		$a = [];
	    	$a['created_at'] = date('Y-m-d H:i:s');
	    	$a['employees_id'] = employees()->id;
	    	$a['sub_total'] = g('sub_total');
	    	$a['discount'] = g('discount');
	    	$a['tax'] = g('tax');
	    	$a['trans_no'] = str_pad(DB::table('trans')->max('id')+1, 5,0, STR_PAD_LEFT);
	    	$a['grand_total'] = g('grand_total');
	    	if(g('customer_type')=='Walk In') {
	    		$a['customers_code'] = '000';
	    		$a['customers_name'] = 'Walk In';
	    	}else{
	    		$customer = CRUDBooster::first('customers',g('customer_id'));
	    		$a['customers_code'] = $customer->code;
	    		$a['customers_name'] = $customer->name;
	    	}
	    	$trans_id = DB::table('trans')->insertGetId($a);

	    	foreach(g('id') as $i=>$id) {
	    		$p = CRUDBooster::first('products',$id);
	    		DB::table('trans_detail')->insert([
	    			'trans_id'=>$trans_id,
	    			'products_id'=>$p->id,
	    			'products_code'=>$p->code,
	    			'products_name'=>$p->name,
	    			'products_main_price'=>$p->main_price,
	    			'products_sell_price'=>$p->sell_price,
	    			'quantity'=>g('quantity')[$i],
	    			'total'=>((g('quantity')[$i])*$p->sell_price)
	    		]);
	    	}
    	});
    	return response()->json(['status'=>1,'message'=>'success','trans_id'=>$trans_id]);
    }
}
