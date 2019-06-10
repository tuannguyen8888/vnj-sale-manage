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

	class AdminGoldSaleOrdersController extends CBExtendController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "order_no";
			$this->limit = "20";
			$this->orderby = "id,desc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = false;
			$this->button_action_style = "button_icon_text";
			$this->button_add = true;
			$this->button_edit = false;
			$this->button_delete = true;
			$this->button_detail = false;
			$this->button_show = false;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = true;
			$this->table = "gold_sale_orders";
            $this->is_search_form = true;
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Số đơn hàng","name"=>"order_no","width"=>"100"];
            $this->col[] = ["label"=>"Loại","name"=>"order_type","callback_php"=>'get_order_type_name($row->order_type);'];
			$this->col[] = ["label"=>"Mã khách hàng","name"=>"(select  case when gold_customers.code is null then gold_customers.tmp_code else gold_customers.code end from gold_customers where gold_customers.id = gold_sale_orders.customer_id) as customer_code"];
			$this->col[] = ["label"=>"Tên khách hàng","name"=>"customer_id","join"=>"gold_customers,name"];
			$this->col[] = ["label"=>"Ngày đơn hàng","name"=>"order_date","callback_php"=>'date_time_format($row->order_date, \'Y-m-d H:i:s\', \'d/m/Y\');'];
			$this->col[] = ["label"=>"Nhân viên bán hàng","name"=>"saler_id","join"=>"cms_users,name"];
			# END COLUMNS DO NOT REMOVE THIS LINE

            // Nguen add new for search
            $this->search_form = [];
            $this->search_form[] = ["label"=>"Loại đơn hàng", "name"=>"order_type","type"=>"select","width"=>"col-md-3", 'dataenum'=>"3|<lable class='label label-danger'>ĐH vi phạm</lable>;2|<lable class='label label-info'>ĐH chuẩn</lable>;1|<lable class='label label-success'>ĐH nhanh</lable>"];
            if(CRUDBooster::myPrivilegeId() == 2) {
                $this->search_form[] = ["label" => "Khách hàng", "name" => "customer_id", "type" => "select2", "width" => "col-md-3", 'datatable' => 'gold_customers,name', 'datatable_where' => 'deleted_at is null and saler_id = ' . CRUDBooster::myId(), 'datatable_format' => "IFNULL(code,tmp_code),' - ',name,' - ',IFNULL(phone,'')"];
            }else{
                $this->search_form[] = ["label" => "Khách hàng", "name" => "customer_id", "type" => "select2", "width" => "col-md-3", 'datatable' => 'gold_customers,name', 'datatable_where' => 'deleted_at is null', 'datatable_format' => "IFNULL(code,tmp_code),' - ',name,' - ',IFNULL(phone,'')"];
            }
            $this->search_form[] = ["label"=>"NVBH", "name"=>"saler_id","type"=>"select2","width"=>"col-md-3", 'datatable'=>'cms_users,name', 'datatable_where'=>CRUDBooster::myPrivilegeId() == 2 ? 'id = '.CRUDBooster::myId() : 'id_cms_privileges = 2', 'datatable_format'=>"employee_code,' - ',name,' (',email,')'"];
            $this->search_form[] = ["label"=>"Xuống dòng", "name"=>"break_line", "type"=>"break_line"];
            $this->search_form[] = ["label"=>"Từ ngày &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", "name"=>"order_date_from_date", "data_column"=>"order_date", "search_type"=>"between_from","type"=>"date","width"=>"col-md-3"];
            $this->search_form[] = ["label"=>"Đến ngày &nbsp;&nbsp;&nbsp;&nbsp;", "name"=>"order_date_to_date", "data_column"=>"order_date", "search_type"=>"between_to","type"=>"date","width"=>"col-md-3"];

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Số đơn hàng','name'=>'order_no','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Khách hàng','name'=>'customer_id','type'=>'datamodal','validation'=>'required|integer|min:0','width'=>'col-sm-10','datamodal_table'=>'gold_customers','datamodal_columns'=>'code,tmp_code,name,address,phone','datamodal_size'=>'large','datamodal_where'=>'deleted_at is null','datamodal_module_path'=>'gold_customers/add','datamodal_columns_alias_name'=>'Mã khách hàng,Mã tạm,Tên khách hàng,Địa chỉ,Số điện thoại','help'=>'Chọn khách hàng'];
			$this->form[] = ['label'=>'Ngày công nợ','name'=>'debt_date','type'=>'datetime','validation'=>'required|date_format:Y-m-d H:i:s','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Số ngày','name'=>'days_diff','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Q10','name'=>'exchange_g10','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Công','name'=>'wage','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Ngày đơn hàng','name'=>'order_date','type'=>'datetime','validation'=>'required|date_format:Y-m-d H:i:s','width'=>'col-sm-10','help'=>'Số đơn hàng sẽ tự phát sinh khi bạn lưu','readonly'=>'true'];
			$this->form[] = ['label'=>'Tổng cân thực tế','name'=>'actual_weight','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Giảm trừ','name'=>'reduce','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Ngày thu','name'=>'pay_date','type'=>'datetime','validation'=>'required|date_format:Y-m-d H:i:s','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Lý do chưa thu hết công nợ cũ/tiền công','name'=>'reason_pay_not_enough','type'=>'textarea','validation'=>'max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Hàng khách đặt','name'=>'other_orders','type'=>'textarea','validation'=>'max:255','width'=>'col-sm-10'];
			# END FORM DO NOT REMOVE THIS LINE

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
            $this->addaction[] = ['label'=>'Hóa đơn BH','url'=>CRUDBooster::mainpath('print-invoice?id=[id]'),'icon'=>'fa fa-newspaper-o','color'=>'info'];
            $this->addaction[] = ['label'=>'Bảng kê chi tiết','url'=>CRUDBooster::mainpath('print-report-detail?id=[id]'),'icon'=>'glyphicon glyphicon-list-alt','color'=>'success'];
            $this->addaction[] = ['label'=>'Danh sách hàng xuất kho','url'=>CRUDBooster::mainpath('print-report-detail-xlsx?id=[id]'),'icon'=>'fa fa-file-text-o','color'=>'primary'];


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
	        // $this->script_js = NULL;
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
            $this->load_js[] = asset("plugins/jQuery-Scanner-Detection/jquery.scannerdetection.js");
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
            $para = Request::all();
            if($para && $para['fast']){
                $data = [];
                $data['page_title'] = 'Tạo mới Đơn Hàng Nhanh';
                $data += ['mode' => 'new'];
                $this->cbView('fast_sale_order_form',$data);
            }else {
                $data = [];
                $data['page_title'] = 'Tạo mới Đơn Hàng';
                $data += ['mode' => 'new'];
                $this->cbView('sale_order_form', $data);
            }
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
                $query->where('gold_sale_orders.saler_id', CRUDBooster::myId());
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
//	    public function hook_before_add(&$postdata) {
//	        //Your code here
//
//	    }

        public function postAddSave() {
            $this->cbLoader();
            if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE) {
                CRUDBooster::insertLog(trans('crudbooster.log_try_add_save',['name'=>Request::input($this->title_field),'module'=>CRUDBooster::getCurrentModule()->name ]));
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }
            DB::beginTransaction();
            try {
                $para = Request::all();
                Log::debug('$para = ' . Json::encode($para));
                $new_order = $para['order'];
                $order_details = $para['order_details'];
                $order_returns = $para['order_returns'];
                $order_pays = $para['order_pays'];

                if (!$new_order['customer_id']) {
                    $new_customer = $para['customer'];
                    $new_customer['created_at'] = date('Y-m-d H:i:s');
                    $new_customer['created_by'] = CRUDBooster::myId();
                    $customer_id = DB::table('gold_customers')->insertGetId($new_customer);
                    $new_order['customer_id'] = $customer_id;
                }

                $order_date_str = $new_order['order_date'];
                $order_date = DateTime::createFromFormat('Y-m-d H:i:s', $order_date_str);

                // get new order no
                $last_order = DB::table('gold_sale_orders as SO')
                    ->whereRaw('SO.deleted_at is null')
                    ->where('SO.order_date', '>=', $order_date->format('Y-m-d') . ' 00:00:00')
                    ->where('SO.order_date', '<=', $order_date->format('Y-m-d') . ' 23:59:59')
                    ->orderBy('SO.order_no', 'desc')
                    ->first();
                $new_order_no = '';
                if ($last_order) {
                    $old_no = intval(explode('-', $last_order->order_no)[1]);
                    $new_order_no = '000' . ($old_no + 1);
                    $new_order_no = substr($new_order_no, strlen($new_order_no) - 3, 3);
                    $new_order_no = 'DH' . $order_date->format('ymd') . '-' . $new_order_no;
                } else {
                    $new_order_no = 'DH' . $order_date->format('ymd') . '-001';
                }
                $new_order['order_no'] = $new_order_no;
                $created_at = date('Y-m-d H:i:s');
                $new_order['created_at'] = $created_at;
                $new_order['created_by'] = CRUDBooster::myId();
                unset($new_order['id']);
                $order_id = DB::table('gold_sale_orders')->insertGetId($new_order);
                Log::debug('$order_id = ' . $order_id);

                if ($order_details && count($order_details)) {
                    $new_sale_order_details = [];
                    foreach ($order_details as $detail) {
                        $new_detail = [
                            'order_id' => $order_id,
                            'sort_no' => $detail['no'],
                            'product_id' => $detail['id'],
                            'age' => $detail['age'] ? $detail['age'] : 0,
                            'discount_wage' => $detail['discount_machining_fee'] ? $detail['discount_machining_fee'] : 0
                        ];
                        array_push($new_sale_order_details, $new_detail);
                    }
                    DB::table('gold_sale_order_details')->insert($new_sale_order_details);
                    foreach ($order_details as $detail) {
                        DB::table('gold_products')->where('id', $detail['id'])->update(['qty' => 0, 'status' => 0, 'updated_at' => $created_at, 'updated_by' => CRUDBooster::myId(), 'notes' => 'Bán trong đơn hàng ' . $new_order_no]);
                    }
                }

                $new_order_returns = [];
                if ($order_returns && count($order_returns)) {
                    foreach ($order_returns as $return) {
                        $new_return = arrayCopy($return);
                        unset($new_return['id']);
                        $new_return['order_id'] = $order_id;
                        Log::debug('$return = ' . Json::encode($new_return));
                        array_push($new_order_returns, $new_return);
                    }
                    Log::debug('$new_order_returns = ' . Json::encode($new_order_returns));
                    DB::table('gold_sale_order_returns')->insert($new_order_returns);
                }
                $new_order_pays = [];
                if ($order_pays && count($order_pays)) {
                    foreach ($order_pays as $pay) {
                        $new_pay = arrayCopy($pay);
//                unset($pay['id']);
                        $new_pay['order_id'] = $order_id;
                        array_push($new_order_pays, $new_pay);
                    }
                    DB::table('gold_sale_order_pays')->insert($new_order_pays);
                }
                $this->sendNotificationViaEmail($new_order_no, $order_date->format('d/m/Y'), $new_order['customer_id'], $new_order['saler_id']);
            }
            catch( \Exception $e){
                DB::rollback();
                Log::debug('PostAdd error $e = ' . Json::encode($e));
                throw $e;
            }
            DB::commit();
            return response()->json(['id'=>$order_id, 'order_no'=>$new_order_no]);
        }
        private function sendNotificationViaEmail($order_no, $order_date, $customer_id, $saler_id){
            try {
                $email = CRUDBooster::getSetting('email_sender');
                $customer = DB::table('gold_customers')->where('id', $customer_id)->first();
                $saler = DB::table('cms_users')->where('id', $saler_id)->first();
                $data = [
                    'order_no' => $order_no,
                    'order_date' => $order_date,
                    'customer_name' => $customer->name,
                    'saler_name' => $saler->name
                ];
                CRUDBooster::sendEmail(['to' => $email, 'data' => $data, 'template' => 'sale_notification_accountant']);
            }
            catch (exception $e) {
                $e = (string) $e;
                Log::debug('sendNotificationViaEmail error $e = '.$e);
            }
        }
        public function getPrintInvoice() {
            $para = Request::all();
            Log::debug('$para = '.Json::encode($para));
            $jasper = new JasperPHP();
            $id = intval($para['id']);
            $filename = 'DH-'.time();
            $database = \Config::get('database.connections.mysql');
            // $database['host'] = '127.0.0.1';
            $input = base_path().'/app/Reports/gold_invoice.jasper';
            $output = public_path() . '/output_reports/' . $filename;
            $parameter = ['id'=>$id];
            Log::debug('$input = ' . $input);
            Log::debug('$output = ' . $output);
            $command = $jasper->process($input, $output, array('pdf'), $parameter, $database)->output();
            Log::debug('$database = ' , $database);
            Log::debug('$command = ' . $command);

            $jasper->process($input, $output, array('pdf'), $parameter, $database)->execute();

            while (!file_exists($output . '.pdf' )){
                sleep(1);
            }

            $file = File::get( $output . '.pdf' );
            //unlink($output . '.pdf');

            return Response::make($file, 200,
                array(
                    'Content-type' => 'application/pdf',
                    'Content-Disposition' => 'filename="hoa-don.pdf"'
                )
            );
        }
        public function getPrintReportDetail() {
            $para = Request::all();
            Log::debug('$para = '.Json::encode($para));
            $jasper = new JasperPHP();
            $id = intval($para['id']);
            $filename = 'DH-'.time();
            $database = \Config::get('database.connections.mysql');
            $input = base_path().'/app/Reports/report_detail_4_pdf.jasper';
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
                    'Content-Disposition' => 'filename="bang-ke-chi-tiet.pdf"'
                )
            );
        }
        public function getPrintReportDetailXlsx() {
            $para = Request::all();
            Log::debug('$para = '.Json::encode($para));
            $jasper = new JasperPHP();
            $id = intval($para['id']);
            $filename = 'DH-'.time();
            $database = \Config::get('database.connections.mysql');
            $input = base_path().'/app/Reports/report_detail_4_xlsx.jasper';
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
                    'Content-Disposition' => 'filename="ds-hang-xuat-kho.xlsx"'
                )
            );
        }
	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after add public static function called 
	    | ---------------------------------------------------------------------- 
	    | @id = last insert id
	    | 
	    */
//	    public function hook_after_add($id) {
//	        //Your code here
//
//	    }

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
            $order_details  = DB::table('gold_sale_order_details')->where('order_id',$id)->select('product_id')->get();
            $created_at = date('Y-m-d H:i:s');
            foreach ($order_details as $detail) {
                DB::table('gold_products')->where('id', $detail->product_id)->update(['qty' => 1, 'status' => 1, 'updated_at' => $created_at, 'updated_by' => CRUDBooster::myId(), 'notes' => '']);
            }
	    }


	    //By the way, you can still create your own method in here... :) 


	}