<?php namespace App\Http\Controllers;

    use Psy\Util\Json;
    use Session;
    use Request;
    use DB;
    use CRUDBooster;
    use DateTime;
    use Illuminate\Support\Facades\Log;
    use JasperPHP\JasperPHP;
    use Illuminate\Support\Facades\File;
    use Response;

	class AdminGoldStocksTransferController extends CBExtendController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "id";
			$this->limit = "20";
			$this->orderby = "id,desc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = false;
			$this->button_action_style = "button_icon_text";
			$this->button_add = true;
			$this->button_edit = false;
			$this->button_delete = false;
			$this->button_detail = false;
			$this->button_show = false;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = false;
			$this->table = "gold_stocks_transfer";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Loại","name"=>"tranfer_type","callback_php"=>'get_tranfer_type_name($row->tranfer_type);'];
			$this->col[] = ["label"=>"Từ kho","name"=>"from_stock_id","join"=>"gold_stocks,name"];
			$this->col[] = ["label"=>"Đến kho","name"=>"to_stock_id","join"=>"gold_stocks,name"];
			$this->col[] = ["label"=>"Lý do","name"=>"reason_id","join"=>"gold_stocks_transfer_reason,reason"];
            $this->col[] = ["label"=>"Người tạo","name"=>"created_by","join"=>"cms_users,name"];
            $this->col[] = ["label"=>"Ngày tạo","name"=>"created_at","callback_php"=>'date_time_format($row->created_at, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];

			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"From Stock Id","name"=>"from_stock_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"from_stock,id"];
			//$this->form[] = ["label"=>"To Stock Id","name"=>"to_stock_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"to_stock,id"];
			//$this->form[] = ["label"=>"Tranfer Type","name"=>"tranfer_type","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Reason Id","name"=>"reason_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"reason,id"];
			//$this->form[] = ["label"=>"Notes","name"=>"notes","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Created By","name"=>"created_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Updated By","name"=>"updated_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Deleted By","name"=>"deleted_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			# OLD END FORM

			/* 
	        | ---------------------------------------------------------------------- 
	        | Sub Module
	        | ----------------------------------------------------------------------     
			| @label          = Label of action 
			| @path           = Path of sub module
			| @foreign_key 	  = foreign key of sub table/module
			| @button_color   = Bootstrap Class (primary,success,warning,danger)
			| @button_icon    = Font Awesome Class  
			| @parent_columns = Sparate with comma, e.g : name,created_at
	        | 
	        */
	        $this->sub_module = array();


	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add More Action Button / Menu
	        | ----------------------------------------------------------------------     
	        | @label       = Label of action 
	        | @url         = Target URL, you can use field alias. e.g : [id], [name], [title], etc
	        | @icon        = Font awesome class icon. e.g : fa fa-bars
	        | @color 	   = Default is primary. (primary, warning, succecss, info)     
	        | @showIf 	   = If condition when action show. Use field alias. e.g : [id] == 1
	        | 
	        */
	        $this->addaction = array();
            $this->addaction[] = ['label'=>'Bảng kê chi tiết','url'=>CRUDBooster::mainpath('print-transfer-detail?id=[id]'),'icon'=>'glyphicon glyphicon-list-alt','color'=>'success'];
            $this->addaction[] = ['label'=>'DS hàng chuyển kho','url'=>CRUDBooster::mainpath('print-transfer-detail-xlsx?id=[id]'),'icon'=>'fa fa-file-text-o','color'=>'info'];


            /*
            | ----------------------------------------------------------------------
            | Add More Button Selected
            | ----------------------------------------------------------------------
            | @label       = Label of action
            | @icon 	   = Icon from fontawesome
            | @name 	   = Name of button
            | Then about the action, you should code at actionButtonSelected method
            |
            */
	        $this->button_selected = array();

	                
	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add alert message to this module at overheader
	        | ----------------------------------------------------------------------     
	        | @message = Text of message 
	        | @type    = warning,success,danger,info        
	        | 
	        */
	        $this->alert        = array();
	                

	        
	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add more button to header button 
	        | ----------------------------------------------------------------------     
	        | @label = Name of button 
	        | @url   = URL Target
	        | @icon  = Icon from Awesome.
	        | 
	        */
	        $this->index_button = array();



	        /* 
	        | ---------------------------------------------------------------------- 
	        | Customize Table Row Color
	        | ----------------------------------------------------------------------     
	        | @condition = If condition. You may use field alias. E.g : [id] == 1
	        | @color = Default is none. You can use bootstrap success,info,warning,danger,primary.        
	        | 
	        */
	        $this->table_row_color = array();     	          

	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | You may use this bellow array to add statistic at dashboard 
	        | ---------------------------------------------------------------------- 
	        | @label, @count, @icon, @color 
	        |
	        */
	        $this->index_statistic = array();



	        /*
	        | ---------------------------------------------------------------------- 
	        | Add javascript at body 
	        | ---------------------------------------------------------------------- 
	        | javascript code in the variable 
	        | $this->script_js = "function() { ... }";
	        |
	        */
//	        $this->script_js = NULL;
            $this->script_js = "
              $(function() {
                 $('.table .button_action a').attr('target','_blank');
              });
            ";


            /*
	        | ---------------------------------------------------------------------- 
	        | Include HTML Code before index table 
	        | ---------------------------------------------------------------------- 
	        | html code to display it before index table
	        | $this->pre_index_html = "<p>test</p>";
	        |
	        */
	        $this->pre_index_html = null;
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include HTML Code after index table 
	        | ---------------------------------------------------------------------- 
	        | html code to display it after index table
	        | $this->post_index_html = "<p>test</p>";
	        |
	        */
	        $this->post_index_html = null;
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include Javascript File 
	        | ---------------------------------------------------------------------- 
	        | URL of your javascript each array 
	        | $this->load_js[] = asset("myfile.js");
	        |
	        */
	        $this->load_js = array();
            $this->load_js[] = asset("plugins/autoNumeric/autoNumeric.min.js");
//            $this->load_js[] = asset("plugins/jQuery-Scanner-Detection/jquery.scannerdetection.js");
            $this->load_js[] = asset("vendor/crudbooster/assets/datetimepicker-master/build/jquery.datetimepicker.full.min.js");
            $this->load_js[] = asset("vendor/crudbooster/assets/select2/dist/js/select2.min.js");
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Add css style at body 
	        | ---------------------------------------------------------------------- 
	        | css code in the variable 
	        | $this->style_css = ".style{....}";
	        |
	        */
	        $this->style_css = NULL;
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include css File 
	        | ---------------------------------------------------------------------- 
	        | URL of your css each array 
	        | $this->load_css[] = asset("myfile.css");
	        |
	        */
	        $this->load_css = array();
            $this->load_css[] = asset("css/loading.css");
            $this->load_css[] = asset("vendor/crudbooster/assets/datetimepicker-master/jquery.datetimepicker.css");
            $this->load_css[] = asset("vendor/crudbooster/assets/select2/dist/css/select2.min.css");
	        
	    }

        public function getAdd() {
//            $para = Request::all();
            $data = [];
            $data['page_title'] = 'Tạo mới';
            $data += ['mode' => 'new'];
            $data += ['FromStockId' => DB::table('cms_users')->where('id', CRUDBooster::myId())->first()->stock_id];
            $this->cbView('stocks_transfer_form', $data);
        }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for button selected
	    | ---------------------------------------------------------------------- 
	    | @id_selected = the id selected
	    | @button_name = the name of button
	    |
	    */
	    public function actionButtonSelected($id_selected,$button_name) {
	        //Your code here
	            
	    }


	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate query of index result 
	    | ---------------------------------------------------------------------- 
	    | @query = current sql query 
	    |
	    */
	    public function hook_query_index(&$query) {
	        //Your code here
            if(CRUDBooster::myPrivilegeId() == 2)// Nhân viên bán hàng
            {
                $query->where('gold_stocks_transfer.created_by', CRUDBooster::myId());
            }
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate row of index table html 
	    | ---------------------------------------------------------------------- 
	    |
	    */    
	    public function hook_row_index($column_index,&$column_value) {	        
	    	//Your code here
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate data input before add data is execute
	    | ---------------------------------------------------------------------- 
	    | @arr
	    |
	    */
	    public function hook_before_add(&$postdata) {        
	        //Your code here

	    }

        public function postAddSave() {
            $this->cbLoader();
            if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE) {
                CRUDBooster::insertLog(trans('crudbooster.log_try_add_save',['name'=>Request::input($this->title_field),'module'=>CRUDBooster::getCurrentModule()->name ]));
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }
            $transfer_detail_ids = [];
            DB::beginTransaction();
            try {
                $para = Request::all();
                Log::debug('$para = ' . Json::encode($para));
                $new_transfer = $para['tranfer_header'];
                $transfer_details = $para['tranfer_details'];

                if($new_transfer['reason_id']) {
                    unset($new_transfer['new_reason']);
                } else {
                    $new_reason = [
                        'tranfer_type' => $new_transfer['tranfer_type'],
                        'reason' =>  $new_transfer['new_reason']
                    ];
                    $reason_id = DB::table('gold_stocks_transfer_reason')->insertGetId($new_reason);
                    $new_transfer['reason_id'] = $reason_id;
                    unset($new_transfer['new_reason']);
                }
                $created_at = date('Y-m-d H:i:s');
                if( $new_transfer['id'] && intval($new_transfer['id']) > 0) // update transfer
                {
//                    $transfer_id = intval($new_transfer['id']);
//                    $updated_at = date('Y-m-d H:i:s');
//                    $this->updatetransferHeader($new_transfer, $updated_at);
                }
                else // new transfer
                {
                    $new_transfer['created_at'] = $created_at;
                    $new_transfer['created_by'] = CRUDBooster::myId();
                    unset($new_transfer['id']);
                    $transfer_id = DB::table('gold_stocks_transfer')->insertGetId($new_transfer);
                    Log::debug('$transfer_id = ' . $transfer_id);
                }
                if ($transfer_details && count($transfer_details)) {
                    $new_transfer_details = [];
                    foreach ($transfer_details as $detail) {
                        $new_detail = [
                            'stocks_transfer_id' => $transfer_id,
                            'product_id' => $detail['id']
                        ];
                        array_push($new_transfer_details, $new_detail);
                    }
                    foreach ($new_transfer_details as $new_detail_insert) {
                        $new_detail_id = DB::table('gold_stocks_transfer_detail')->insertGetId($new_detail_insert);
                        DB::table('gold_products')->where('id', $new_detail_insert['id'])
                            ->where('stock_id', $new_transfer['from_stock_id'])
                            ->update(['stock_id' => $new_transfer['to_stock_id'], 'updated_at' => $created_at, 'updated_by' => CRUDBooster::myId()]);
                        array_push($transfer_detail_ids, $new_detail_id);
                    }
                }
            }
            catch( \Exception $e){
                DB::rollback();
                Log::debug('PostAdd error $e = ' . Json::encode($e));
                throw $e;
            }
            DB::commit();
            return response()->json(['transfer_id'=>$transfer_id, 'transfer_detail_ids'=>$order_detail_ids]);
        }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after add public static function called 
	    | ---------------------------------------------------------------------- 
	    | @id = last insert id
	    | 
	    */
	    public function hook_after_add($id) {        
	        //Your code here

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate data input before update data is execute
	    | ---------------------------------------------------------------------- 
	    | @postdata = input post data 
	    | @id       = current id 
	    | 
	    */
	    public function hook_before_edit(&$postdata,$id) {        
	        //Your code here

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after edit public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hook_after_edit($id) {
	        //Your code here 

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command before delete public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hook_before_delete($id) {
	        //Your code here

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after delete public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hook_after_delete($id) {
	        //Your code here

	    }



	    //By the way, you can still create your own method in here... :) 

        public function getPrintTransferDetail() {
            $para = Request::all();
            Log::debug('$para = '.Json::encode($para));
            $jasper = new JasperPHP();
            $id = intval($para['id']);
            $filename = 'DH-'.time();
            $database = \Config::get('database.connections.mysql');
            $input = base_path().'/app/Reports/transfer_detail_pdf.jasper';
            $output = public_path() . '/output_reports/' . $filename;
            $parameter = ['id'=>$id];
            Log::debug('$input = ' . $input);
            Log::debug('$output = ' . $output);
            $command = $jasper->process($input, $output, array('pdf'), $parameter, $database)->output();
            Log::debug('$command = ' . $command);
            $jasper->process($input, $output, array('pdf'), $parameter, $database)->execute();

            while (!file_exists($output . '.pdf' )){
                sleep(1);
            }

            $file = File::get( $output . '.pdf' );
            unlink($output . '.pdf');

            return Response::make($file, 200,
                array(
                    'Content-type' => 'application/pdf',
                    'Content-Disposition' => 'filename="bang-ke-chi-tiet-chuyen-kho.pdf"'
                )
            );
        }
        public function getPrintTransferDetailXlsx() {
            $para = Request::all();
            Log::debug('$para = '.Json::encode($para));
            $jasper = new JasperPHP();
            $id = intval($para['id']);
            $filename = 'DH-'.time();
            $database = \Config::get('database.connections.mysql');
            $input = base_path().'/app/Reports/transfer_detail_xlsx.jasper';
            $output = public_path() . '/output_reports/' . $filename;
            $parameter = ['id'=>$id];
            Log::debug('$input = ' . $input);
            Log::debug('$output = ' . $output);
            $command = $jasper->process($input, $output, array('xlsx'), $parameter, $database)->output();
            Log::debug('$command = ' . $command);
            $jasper->process($input, $output, array('xlsx'), $parameter, $database)->execute();

            while (!file_exists($output . '.xlsx' )){
                sleep(1);
            }

            $file = File::get( $output . '.xlsx' );
            unlink($output . '.xlsx');

            return Response::make($file, 200,
                array(
                    'Content-type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => 'filename="ds-hang-chuyen-kho.xlsx"'
                )
            );
        }
        public function getInventoryReport() {
//            $para = Request::all();
            $data = [];
            $data['page_title'] = 'Báo cáo tồn kho';
            $user = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
            $stock_names = '';
            if($user && $user->stock_id) {
                $stocks = DB::table('gold_stocks')->whereRaw('FIND_IN_SET(id , \'' . $user->stock_id . '\')')->get();
                if($stocks && count($stocks) > 0){
                    foreach ($stocks as $stock) {
                        if($stock_names == ''){
                            $stock_names = $stock->name;
                        }else{
                            $stock_names = $stock_names.', '.$stock->name;
                        }
                    }
                }
            }
            $data['stock_names'] = $stock_names;
            $this->cbView('inventory_report_form', $data);
        }
        public function getPrintInventoryReport() {
            $para = Request::all();
            Log::debug('$para = '.Json::encode($para));
            $jasper = new JasperPHP();
            $from_date = intval($para['from_date']);
            $user = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
            $filename = 'DH-'.time();
            $database = \Config::get('database.connections.mysql');
            $input = base_path().'/app/Reports/report_inventory.jasper';
            $output = public_path() . '/output_reports/' . $filename;
            $parameter = ['from_date'=>$from_date, 'stock_ids'=>$user->stock_id, 'saler_name'=>$user->name];
            Log::debug('$input = ' . $input);
            Log::debug('$output = ' . $output);
            $command = $jasper->process($input, $output, array('pdf'), $parameter, $database)->output();
            Log::debug('$command = ' . $command);
            $jasper->process($input, $output, array('pdf'), $parameter, $database)->execute();

            while (!file_exists($output . '.pdf' )){
                sleep(1);
            }

            $file = File::get( $output . '.pdf' );
            unlink($output . '.pdf');

            return Response::make($file, 200,
                array(
                    'Content-type' => 'application/pdf',
                    'Content-Disposition' => 'filename="bao-cao-ton-kho.pdf"'
                )
            );
        }
	}