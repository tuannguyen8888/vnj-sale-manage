<?php namespace App\Http\Controllers;

use Psy\Util\Json;
use Session;
use Request;
use DB;
use CRUDBooster;
use Enums;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\PDF;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use Schema;
use CB;
use DateTime;
use Illuminate\Support\Facades\Log;

	class AdminGoldLiabilitiesController extends CBExtendController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "id";
			$this->limit = "20";
			$this->orderby = "import_liabilities_id,desc";
			$this->global_privilege = false;
			$this->button_table_action = false;
			$this->button_bulk_action = false;
			$this->button_action_style = "button_icon_text";
			$this->button_add = false;
			$this->button_edit = false;
			$this->button_delete = false;
			$this->button_detail = false;
			$this->button_show = false;
			$this->button_filter = true;
			$this->button_import = (CRUDBooster::myPrivilegeId() == 1 || CRUDBooster::myPrivilegeId() == 5)? true: false;
			$this->button_export = true;
			$this->table = "gold_liabilities";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Ngày import","name"=>"import_liabilities_id","join"=>"gold_import_liabilities,import_date"];//,"callback_php"=>'date_time_format($row->import_date, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
			$this->col[] = ["label"=>"Mã khách hàng","name"=>"customer_id","join"=>"gold_customers,code"];
			$this->col[] = ["label"=>"Tên khách hàng","name"=>"customer_id","join"=>"gold_customers,name"];
			$this->col[] = ["label"=>"Nhân viên bán hàng","name"=>"saler_name"];
			$this->col[] = ["label"=>"Ngày công nợ","name"=>"date","callback_php"=>'date_time_format($row->date, \'Y-m-d H:i:s\', \'d/m/Y\');'];
			$this->col[] = ["label"=>"Tuổi bán(%)","name"=>"age_sell", "callback_php"=>'number_format($row->age_sell, 3)'];
			$this->col[] = ["label"=>"Q10","name"=>"exchange_g10_credit", "callback_php"=>'number_format($row->exchange_g10_credit, 3)'];
			$this->col[] = ["label"=>"Tiền công","name"=>"wage_credit", "callback_php"=>'number_format($row->wage_credit)'];
			$this->col[] = ["label"=>"Q10","name"=>"exchange_g10_debit", "callback_php"=>'number_format($row->exchange_g10_debit, 3)'];
			$this->col[] = ["label"=>"Tiền công","name"=>"wage_debit", "callback_php"=>'number_format($row->wage_debit)'];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];

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
	        $this->script_js = NULL;


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
                $query->leftJoin('gold_import_liabilities as IL', 'IL.id', '=', 'gold_liabilities.import_liabilities_id')
                    ->whereRaw('IL.deleted_at is null')
                    ->whereRaw("trim(upper(gold_liabilities.saler_name)) like '".CRUDBooster::myName()."'");
            }else{
                $query->leftJoin('gold_import_liabilities as IL', 'IL.id', '=', 'gold_liabilities.import_liabilities_id')
                    ->whereRaw('IL.deleted_at is null');
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

        public function getImportData() {
            set_time_limit(0);
            ini_set('memory_limit', '4294967296');
            $this->cbLoader();
            $data['page_menu']       = Route::getCurrentRoute()->getActionName();
            $module     = CRUDBooster::getCurrentModule();
            $data['page_title']      = 'Import dữ liệu: '.$module->name;

            if(Request::get('file') && !Request::get('import')) {
                $file = base64_decode(Request::get('file'));
                $file = storage_path('app/'.$file);
                $rows = Excel::load($file,function($reader) {
//                    $reader->limitRows(10);
                })->get();
                Log::debug('count($rows) = ');
                Log::debug(count($rows));
                Session::put('total_data_import',count($rows));
                $data['table_rows'] = []; /// $data['table_rows'] = $rows; chuyển qua load bằng ajax
                Session::put('table_rows',$rows);
                unlink($file);
                $data_import_column = array();
                foreach($rows as $value) {
                    $a = array();
//                    Log::debug('$rows = ',[Json::encode($rows)]);
                    foreach($value as $k=>$v) {
                        $a[] = $k;
                    }
                    if(count($a)) {
                        $data_import_column = $a;
                    }
                    break;
                }
                Log::debug('$data_import_column = ',$data_import_column);
                if(in_array('ma_khach_hang', $data_import_column, true)
                    && in_array('ten_khach_hang', $data_import_column, true)
                    && in_array('ten_nguoi_ban', $data_import_column, true)
                    && in_array('ngay_cong_no', $data_import_column, true)
                    && in_array('tuoi_ban', $data_import_column, true)
                    && in_array('q10_ghi_co', $data_import_column, true)
                    && in_array('tien_cong_ghi_co', $data_import_column, true)
                    && in_array('q10_ghi_no', $data_import_column, true)
                    && in_array('tien_cong_ghi_no', $data_import_column, true))
                {
                    Log::debug('File dung dinh dang');
                    $table_columns = $data_import_column; // ['ma_vach', 'ma_hang', 'ten_hang', 'dvt', 'tong_tl', 'tl_da', 'tl_vang', 'sl', 'cong_le', 'cong_si', 'cong_von', 'ngay_nhap', 'ngay_lam_tem', 'kho_hang', 'loai_hang', 'nhom_hang', 'loai_vang', 'tinh_trang_hang'];
                    $data['table_columns'] = $table_columns;
                    $data_import_column = ['Mã khách hàng', 'Tên khách hàng', 'Tên người bán', 'Ngày công nợ', 'Tuổi bán', 'Q10 (ghi có)', 'Tiền công (ghi có)', 'Q10 (ghi nợ)', 'Tiền công (ghi nợ)'];
                    $data['data_import_column'] = $data_import_column;
                    Session::put('select_column',$table_columns);
                } else {
                    //File không đúng định dạng
                    Log::debug('File KHONG dung dinh dang');
                    $message_all = [sprintf('File không đúng định dạng, vui lòng kiểm tra lại.','File')];
                    $res = redirect()->back()->with(['message'=>trans('crudbooster.alert_validation_error',['error'=>implode(', ',$message_all)]),'message_type'=>'warning'])->withInput();
                    Session::driver()->save();
                    $res->send();
                    exit();
                }
            } else {
                if (!(strpos( Request::server('HTTP_REFERER'), 'done-import') !== false || strpos(Request::server('HTTP_REFERER'), 'import-data') !== false)) {
                    Session::put('return_url',Request::server('HTTP_REFERER'));
                }
            }
            return view('import_liabilities',$data);
        }
        public function postLoadDataImport(){
            set_time_limit(0);
            ini_set('memory_limit', '4294967296');
            $para = Request::json()->all();
            $data = Session::get('table_rows');
            $start = intval($para['dataTableParameters']['start']);
            $length = intval($para['dataTableParameters']['length']);
            //Log::debug('$start = '.$start.'; $length = '.$length);
            $dataTableData = array();
            $dataTableData += ['draw' => $para['dataTableParameters']['draw']];
            $dataTableData += ['recordsTotal' => count($data)];
            $dataTableData += ['recordsFiltered' => $dataTableData['recordsTotal']];
            $select_data = array();
            $i = $start;
            if($length == -1){
                $select_data = $data;
            }else{
                while ($i < $start + $length && $i < $dataTableData['recordsTotal']) {
//                    Log::debug('$data['.$i.'] = '.Json::encode($data[$i]));
                    $clone = clone $data[$i];
                    if($clone['ngay_cong_no']) {
                        $tmp_date = $data[$i]['ngay_cong_no'];
                        $clone['ngay_cong_no'] = $tmp_date->format('d/m/Y');
                    }
                    array_push($select_data, $clone);
                    $i += 1;
                }
            }
            $dataTableData += ['data'=> $select_data];
            return response()->json($dataTableData);
        }
        public function postDoneImport() {
            set_time_limit(0);
            ini_set('memory_limit', '4294967296');
            $this->cbLoader();
            $data['page_menu']       = Route::getCurrentRoute()->getActionName();
            $module     = CRUDBooster::getCurrentModule();
            $data['page_title']      = trans('crudbooster.import_page_title',['module'=>$module->name]);
            //Session::put('select_column',Request::get('select_column'));
            return view('crudbooster::import',$data);
        }
        public function postDoImportChunk() {
            set_time_limit(0);
            ini_set('memory_limit', '4294967296');
            $this->cbLoader();
            $file_md5 = md5(Request::get('file'));

            if(Request::get('file') && Request::get('resume')==1) {
                $total = Session::get('total_data_import');
                $prog = intval(Cache::get('success_'.$file_md5)) / $total * 100;
                $prog = round($prog,2);
                if($prog >= 100) {
                    Cache::forget('success_'.$file_md5);
                }
                return response()->json(['progress'=> $prog, 'last_error'=>Cache::get('error_'.$file_md5) ]);
            }
            $select_column = Session::get('select_column');
//            Log::debug('$select_column = '.Json::encode($select_column));
            $table_columns = [
                'import_liabilities_id',
                'customer_id',
                'saler_name',
                'date',
                'age_sell',
                'exchange_g10_credit',
                'wage_credit',
                'exchange_g10_debit',
                'wage_debit'
            ];
//            $table_columns  =  DB::getSchemaBuilder()->getColumnListing($this->table);

//            $file = base64_decode(Request::get('file'));
//            $file = storage_path('app/'.$file);
//
//            $rows = Excel::load($file,function($reader) {
//            })->get();
            $rows = Session::get('table_rows');

            $has_created_at = false;
            if(CRUDBooster::isColumnExists($this->table,'created_at')) {
                $has_created_at = true;
            }
            $has_created_by = false;
            if(CRUDBooster::isColumnExists($this->table,'created_by')) {
                $has_created_by = true;
            }
            $has_updated_at = false;
            if(CRUDBooster::isColumnExists($this->table,'updated_at')) {
                $has_updated_at = true;
            }
            $has_updated_by = false;
            if(CRUDBooster::isColumnExists($this->table,'updated_by')) {
                $has_updated_by = true;
            }
            Cache::put('success_'.$file_md5, 0, 60*30); // cache trong 30 phút
            // insert new `gold_import_liabilities`
            $new_import_row = ['import_date'=>date('Y-m-d H:i:s')];
            DB::table('gold_import_liabilities')
                ->where('id','>', 0)
                ->where('import_date','<', date('Y-m-d H:i:s'))
                ->update(['deleted_at' => date('Y-m-d H:i:s')]);
            $import_liabilities_id = DB::table('gold_import_liabilities')->insertGetId($new_import_row);
            //

//            Log::debug('$rows = '.Json::encode($rows));
            $data_import_column = array();
            foreach($rows as $value) {
//                Log::debug('$value = '.$value);
                if($value['ma_khach_hang'] == 'Tổng cộng'){
                    Cache::increment('success_'.$file_md5);
                    continue;
                }
                if(!$value['ma_khach_hang']){
                    Cache::increment('success_'.$file_md5);
                    continue;
                }
                $import_row = array();
                foreach($table_columns as $colname) {
                    //$colname = $table_columns[$sk];
//                    Log::debug('$sk = '.$sk);
//                    Log::debug('$colname = '.$colname);
//                    Log::debug('$this->isForeignKey($colname) = '.$this->isForeignKey($colname));
                    //Log::debug('$s = '.$s);
                    //Log::debug('$value[$s] = '.$value[$s]);
//                    Log::debug('$value[$s]k = '.$value[$s]k);
                    if($colname  == 'import_liabilities_id') {
                        $import_row[$colname] = $import_liabilities_id;
                    } elseif($colname  == 'customer_id') {
                        $s='ma_khach_hang';
                        $relation_table = $this->getTableForeignKey($colname);
                        $relation_moduls = DB::table('cms_moduls')->where('table_name',$relation_table)->first();

                        $relation_class = __NAMESPACE__ . '\\' . $relation_moduls->controller;
                        if(!class_exists($relation_class)) {
                            $relation_class = '\App\Http\Controllers\\'.$relation_moduls->controller;
                        }
                        $relation_class = new $relation_class;
                        $relation_class->cbLoader();

                        $title_field = $relation_class->title_field;

                        $relation_insert_data = array();
                        $relation_insert_data[$title_field] = $value[$s];

                        if(CRUDBooster::isColumnExists($relation_table,'name')) {
                            $relation_insert_data['name'] = $value['ten_khach_hang'];
                        }
                        if(CRUDBooster::isColumnExists($relation_table,'import')) {
                            $relation_insert_data['import'] = 1;
                        }
                        if(CRUDBooster::isColumnExists($relation_table,'created_at')) {
                            $relation_insert_data['created_at'] = date('Y-m-d H:i:s');
                        }
                        if(CRUDBooster::isColumnExists($relation_table,'created_by')) {
                            $relation_insert_data['created_by'] = CRUDBooster::myId();
                        }

                        try{
                            $relation_exists = DB::table($relation_table)->where($title_field,strval($value[$s]))->first();
                            if($relation_exists) {
                                $relation_primary_key = $relation_class->primary_key;
                                $relation_id = $relation_exists->$relation_primary_key;
                            }else{

//                                Log::debug('$relation_insert_data = ', $relation_insert_data);
                                $relation_id = DB::table($relation_table)->insertGetId($relation_insert_data);
                            }

                            $import_row[$colname] = $relation_id;
                        }catch(\Exception $e) {
                            Log::debug('$e = '.$e);
                            exit($e);
                        }
                    } elseif($colname == 'saler_name') {
                        $s='ten_nguoi_ban';
                        $import_row[$colname] = $value[$s];
                    } elseif($colname  == 'date') {
                        $s='ngay_cong_no';
                        if($value[$s] == '' || !$value[$s]){
                            $import_row[$colname] = null;
                        }else{
                            $tmp_date = $value[$s];
                            $import_row[$colname] = $tmp_date->format('Y-m-d H:i:s');
                        }
                    } elseif($colname == 'saler_name') {
                        $s='ten_nguoi_ban';
                        $import_row[$colname] = $value[$s];
                    } elseif($colname == 'age_sell') {
                        $s='tuoi_ban';
                        $import_row[$colname] = $value[$s];
                    } elseif($colname == 'exchange_g10_credit') {
                        $s='q10_ghi_co';
                        $import_row[$colname] = $value[$s];
                    } elseif($colname == 'wage_credit') {
                        $s='tien_cong_ghi_co';
                        $import_row[$colname] = $value[$s];
                    } elseif($colname == 'exchange_g10_debit') {
                        $s='q10_ghi_no';
                        $import_row[$colname] = $value[$s];
                    } elseif($colname == 'wage_debit') {
                        $s='tien_cong_ghi_no';
                        $import_row[$colname] = $value[$s];
                    }
                }

//                Log::debug('$import_row = '.Json::encode($import_row));
//
//                Log::debug('$this->title_field = '.$this->title_field);
//                $has_title_field = true;
//                foreach($import_row as $k=>$v) {
//                    Log::debug('$k = '.$k.', $v  = '.$v);
//                    if($k == $this->title_field && $v == '') {
//                        $has_title_field = false;
//                        break;
//                    }
//                }
//
//                Log::debug('$has_title_field = '.$has_title_field);
//                if($has_title_field==false) continue;

                try{
                    if($has_created_at) {
                        $import_row['created_at'] = date('Y-m-d H:i:s');
                    }
                    if($has_created_by){
                        $import_row['created_by'] = CRUDBooster::myId();
                    }

                    DB::table($this->table)->insert($import_row);
                    Cache::increment('success_'.$file_md5);
                }catch(\Exception $e) {
                    $e = (string) $e;
                    Log::debug('(string) $e = '.$e);
                    Cache::put('error_'.$file_md5,$e,500);
                }
            }
            Session::put('table_rows',null);//đặt lại cho đỡ nặng memory
            return response()->json(['status'=>true]);
        }
        public function getTableForeignKey($fieldName)
        {
            $table = null;
            switch ($fieldName) {
                case 'import_liabilities_id':
                    $table  = 'gold_import_liabilities';
                    break;
                case 'customer_id':
                    $table  = 'gold_customers';
                    break;
                default:
                    if (substr($fieldName, 0, 3) == 'id_') {
                        $table = substr($fieldName, 3);
                    } elseif
                    (substr($fieldName, -3) == '_id') {
                        $table = substr($fieldName, 0, (strlen($fieldName) - 3));
                    }
            }
            return $table;
        }
        public function isForeignKey($fieldName) {
//            if(substr($fieldName, 0,3) == 'id_') {
//                $table = substr($fieldName, 3);
//            }elseif(substr($fieldName, -3) == '_id') {
//                $table = substr($fieldName, 0, (strlen($fieldName)-3) );
//            }
            if(Cache::has('isForeignKey_'.$fieldName)) {
                return Cache::get('isForeignKey_'.$fieldName);
            }else{
                $table  = $this->getTableForeignKey($fieldName);
                if($table) {
                    $hasTable = Schema::hasTable($table);
                    if($hasTable) {
                        Cache::forever('isForeignKey_'.$fieldName,true);
                        return true;
                    }else{
                        Cache::forever('isForeignKey_'.$fieldName,false);
                        return false;
                    }
                }else{
                    return false;
                }
            }
        }
	}