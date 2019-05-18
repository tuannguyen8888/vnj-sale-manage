<?php namespace App\Http\Controllers;

error_reporting(E_ALL ^ E_NOTICE);

use crocodicstudio\crudbooster\controllers\Controller;
use crocodicstudio\crudbooster\controllers\LogsController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\PDF;
use Maatwebsite\Excel\Facades\Excel;
use CRUDBooster;
use CB;
use Schema;

	class CBExtendController extends \crocodicstudio\crudbooster\controllers\CBController {
        public $is_search_form = false;
        public function getIndex() {
            $this->cbLoader();

            $module = CRUDBooster::getCurrentModule();

            if(!CRUDBooster::isView() && $this->global_privilege==FALSE) {
                CRUDBooster::insertLog(trans('crudbooster.log_try_view',['module'=>$module->name]));
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
            }

            if(Request::get('parent_table')) {
                $parentTablePK = CB::pk(g('parent_table'));
                $data['parent_table'] = DB::table(Request::get('parent_table'))->where($parentTablePK,Request::get('parent_id'))->first();
                if(Request::get('foreign_key')) {
                    $data['parent_field'] = Request::get('foreign_key');
                }else{
                    $data['parent_field'] = CB::getTableForeignKey(g('parent_table'),$this->table);
                }

                if($parent_field) {
                    foreach($this->columns_table as $i=>$col) {
                        if($col['name'] == $parent_field) {
                            unset($this->columns_table[$i]);
                        }
                    }
                }
            }

            $data['table'] 	  = $this->table;
            $data['table_pk'] = CB::pk($this->table);
            $data['page_title']       = $module->name;
            $data['page_description'] = trans('crudbooster.default_module_description');
            $data['date_candidate']   = $this->date_candidate;
            $data['limit'] = $limit   = (Request::get('limit'))?Request::get('limit'):$this->limit;

            $tablePK = $data['table_pk'];
            $table_columns = CB::getTableColumns($this->table);
            $result = DB::table($this->table)->select(DB::raw($this->table.".".$this->primary_key));

            if(Request::get('parent_id')) {
                $table_parent = $this->table;
                $table_parent = CRUDBooster::parseSqlTable($table_parent)['table'];
                $result->where($table_parent.'.'.Request::get('foreign_key'),Request::get('parent_id'));
            }


            $this->hook_query_index($result);

            if(in_array('deleted_at', $table_columns)) {
                $result->where($this->table.'.deleted_at',NULL);
            }

            $alias            = array();
            $join_alias_count = 0;
            $join_table_temp  = array();
            $table            = $this->table;
            $columns_table    = $this->columns_table;
            foreach($columns_table as $index => $coltab) {

                $join = @$coltab['join'];
                $join_where = @$coltab['join_where'];
                $join_id = @$coltab['join_id'];
                $field = @$coltab['name'];
                $join_table_temp[] = $table;

                if(!$field) die('Please make sure there is key `name` in each row of col');

                if(strpos($field, ' as ')!==FALSE) {
                    $field = substr($field, strpos($field, ' as ')+4);
                    $field_with = (array_key_exists('join', $coltab))?str_replace(",",".",$coltab['join']):$field;
                    $result->addselect(DB::raw($coltab['name']));
                    $columns_table[$index]['type_data']   = 'varchar';
                    $columns_table[$index]['field']       = $field;
                    $columns_table[$index]['field_raw']   = $field;
                    $columns_table[$index]['field_with']  = $field_with;
                    $columns_table[$index]['is_subquery'] = true;
                    continue;
                }

                if(strpos($field,'.')!==FALSE) {
                    $result->addselect($field);
                }else{
                    $result->addselect($table.'.'.$field);
                }

                $field_array = explode('.', $field);

                if(isset($field_array[1])) {
                    $field = $field_array[1];
                    $table = $field_array[0];
                }else{
                    $table = $this->table;
                }

                if($join) {

                    $join_exp     = explode(',', $join);

                    $join_table  = $join_exp[0];
                    $joinTablePK = CB::pk($join_table);
                    $join_column = $join_exp[1];
                    $join_alias  = str_replace(".", "_", $join_table);

                    if(in_array($join_table, $join_table_temp)) {
                        $join_alias_count += 1;
                        $join_alias = $join_table.$join_alias_count;
                    }
                    $join_table_temp[] = $join_table;

                    $result->leftjoin($join_table.' as '.$join_alias,$join_alias.(($join_id)? '.'.$join_id:'.'.$joinTablePK),'=',DB::raw($table.'.'.$field. (($join_where) ? ' AND '.$join_where.' ':'') ) );
                    $result->addselect($join_alias.'.'.$join_column.' as '.$join_alias.'_'.$join_column);

                    $join_table_columns = CRUDBooster::getTableColumns($join_table);
                    if($join_table_columns) {
                        foreach($join_table_columns as $jtc) {
                            $result->addselect($join_alias.'.'.$jtc.' as '.$join_alias.'_'.$jtc);
                        }
                    }

                    $alias[] = $join_alias;
                    $columns_table[$index]['type_data']	 = CRUDBooster::getFieldType($join_table,$join_column);
                    $columns_table[$index]['field']      = $join_alias.'_'.$join_column;
                    $columns_table[$index]['field_with'] = $join_alias.'.'.$join_column;
                    $columns_table[$index]['field_raw']  = $join_column;

                    @$join_table1  = $join_exp[2];
                    @$joinTable1PK = CB::pk($join_table1);
                    @$join_column1 = $join_exp[3];
                    @$join_alias1  = $join_table1;

                    if($join_table1 && $join_column1) {

                        if(in_array($join_table1, $join_table_temp)) {
                            $join_alias_count += 1;
                            $join_alias1 = $join_table1.$join_alias_count;
                        }

                        $join_table_temp[] = $join_table1;

                        $result->leftjoin($join_table1.' as '.$join_alias1,$join_alias1.'.'.$joinTable1PK,'=',$join_alias.'.'.$join_column);
                        $result->addselect($join_alias1.'.'.$join_column1.' as '.$join_column1.'_'.$join_alias1);
                        $alias[] = $join_alias1;
                        $columns_table[$index]['type_data']	 = CRUDBooster::getFieldType($join_table1,$join_column1);
                        $columns_table[$index]['field']      = $join_column1.'_'.$join_alias1;
                        $columns_table[$index]['field_with'] = $join_alias1.'.'.$join_column1;
                        $columns_table[$index]['field_raw']  = $join_column1;
                    }

                }else{

                    $result->addselect($table.'.'.$field);
                    $columns_table[$index]['type_data']	 = CRUDBooster::getFieldType($table,$field);
                    $columns_table[$index]['field']      = $field;
                    $columns_table[$index]['field_raw']  = $field;
                    $columns_table[$index]['field_with'] = $table.'.'.$field;
                }
            }

            // Nguyên add for search form
            if($this->search_form  && count($this->search_form )>0) {
                foreach ($this->search_form as $index => $search_form) {
                    Log::debug('$search_form = ',$search_form);
                    if (CRUDBooster::isColumnExists($this->table, $search_form['name'])) {
                        if (Request::get($search_form['name'])) {
                            if ($search_form['search_type'] != 'between_from' && $search_form['search_type'] != 'between_to') {
                                if (Request::get($search_form['name']) == 'NULL') {
                                    $result->whereRaw(DB::raw($search_form['name'] . ' is null'));
                                } else {
                                    $result->where($search_form['name'], Request::get($search_form['name']));
                                }
                            }elseif($search_form['search_type'] == 'between_from'){
                                if (Request::get($search_form['name'])) {
                                    $result->where($search_form['name'], '>=', Request::get($search_form['name']));
                                }
                            }elseif($search_form['search_type'] == 'between_to'){
                                if (Request::get($search_form['name'])) {
                                    if($search_form['type'] = 'date'){
                                        $result->where($search_form['name'], '<=', Request::get($search_form['name']).' 23:59:59');
                                    } else {
                                        $result->where($search_form['name'], '<=', Request::get($search_form['name']));
                                    }
                                }
                            }
                        }
                        // $this->search_form[$index]['value'] = Request::get($search_form['name']);
                    } elseif($search_form['data_column'] && CRUDBooster::isColumnExists($this->table, $search_form['data_column'])) {
                        if (Request::get($search_form['name'])) {
                            if($search_form['search_type'] == 'between_from'){
                                if (Request::get($search_form['name'])) {
                                    $result->where($search_form['data_column'], '>=', Request::get($search_form['name']));
                                }
                            }elseif($search_form['search_type'] == 'between_to'){
                                if (Request::get($search_form['name'])) {
                                    if($search_form['type'] = 'date'){
                                        $result->where($search_form['data_column'], '<=', Request::get($search_form['name']).' 23:59:59');
                                    } else {
                                        $result->where($search_form['data_column'], '<=', Request::get($search_form['name']));
                                    }
                                }
                            }
                        }
                    }
                    if (Request::get($search_form['name'])) {
                        $this->search_form[$index]['value'] = Request::get($search_form['name']);
                    }
                }
                Log::debug('SQL = '.$result->toSql());
            }
            /////

            if(Request::get('q')) {
                $result->where(function($w) use ($columns_table, $request) {
                    foreach($columns_table as $col) {
                        if(!$col['field_with']) continue;
                        if($col['is_subquery']) continue;
                        $w->orwhere($col['field_with'],"like","%".Request::get("q")."%");
                    }
                });
            }

            if(Request::get('where')) {
                foreach(Request::get('where') as $k=>$v) {
                    $result->where($table.'.'.$k,$v);
                }
            }

            $filter_is_orderby = false;
            if(Request::get('filter_column')) {

                $filter_column = Request::get('filter_column');
                $result->where(function($w) use ($filter_column,$fc) {
                    foreach($filter_column as $key=>$fc) {

                        $value = @$fc['value'];
                        $type  = @$fc['type'];

                        if($type == 'empty') {
                            $w->whereNull($key)->orWhere($key,'');
                            continue;
                        }

                        if($value=='' || $type=='') continue;

                        if($type == 'between') continue;

                        switch($type) {
                            default:
                                if($key && $type && $value) $w->where($key,$type,$value);
                                break;
                            case 'like':
                            case 'not like':
                                $value = '%'.$value.'%';
                                if($key && $type && $value) $w->where($key,$type,$value);
                                break;
                            case 'in':
                            case 'not in':
                                if($value) {
                                    $value = explode(',',$value);
                                    if($key && $value) $w->whereIn($key,$value);
                                }
                                break;
                        }


                    }
                });

                foreach($filter_column as $key=>$fc) {
                    $value = @$fc['value'];
                    $type  = @$fc['type'];
                    $sorting = @$fc['sorting'];

                    if($sorting!='') {
                        if($key) {
                            $result->orderby($key,$sorting);
                            $filter_is_orderby = true;
                        }
                    }

                    if ($type=='between') {
                        if($key && $value) $result->whereBetween($key,$value);
                    }else{
                        continue;
                    }
                }
            }

            if($filter_is_orderby == true) {
                $data['result']  = $result->paginate($limit);

            }else{
                if($this->orderby) {
                    if(is_array($this->orderby)) {
                        foreach($this->orderby as $k=>$v) {
                            if(strpos($k, '.')!==FALSE) {
                                $orderby_table = explode(".",$k)[0];
                                $k = explode(".",$k)[1];
                            }else{
                                $orderby_table = $this->table;
                            }
                            $result->orderby($orderby_table.'.'.$k,$v);
                        }
                    }else{
                        $this->orderby = explode(";",$this->orderby);
                        foreach($this->orderby as $o) {
                            $o = explode(",",$o);
                            $k = $o[0];
                            $v = $o[1];
                            if(strpos($k, '.')!==FALSE) {
                                $orderby_table = explode(".",$k)[0];
                            }else{
                                $orderby_table = $this->table;
                            }
                            $result->orderby($orderby_table.'.'.$k,$v);
                        }
                    }
                    $data['result'] = $result->paginate($limit);
                }else{
                    $data['result'] = $result->orderby($this->table.'.'.$this->primary_key,'desc')->paginate($limit);
                }
            }

            $data['columns'] = $columns_table;

            if($this->index_return) return $data;

            //LISTING INDEX HTML
            $addaction     = $this->data['addaction'];

            if($this->sub_module) {
                foreach($this->sub_module as $s) {
                    $table_parent = CRUDBooster::parseSqlTable($this->table)['table'];
                    $addaction[] = [
                        'label'=>$s['label'],
                        'icon'=>$s['button_icon'],
                        'url'=>CRUDBooster::adminPath($s['path']).'?parent_table='.$table_parent.'&parent_columns='.$s['parent_columns'].'&parent_columns_alias='.$s['parent_columns_alias'].'&parent_id=['.(!isset($s['custom_parent_id']) ? "id": $s['custom_parent_id']).']&return_url='.urlencode(Request::fullUrl()).'&foreign_key='.$s['foreign_key'].'&label='.urlencode($s['label']),
                        'color'=>$s['button_color'],
                        'showIf'=>$s['showIf']
                    ];
                }
            }

            $mainpath      = CRUDBooster::mainpath();
            $orig_mainpath = $this->data['mainpath'];
            $title_field   = $this->title_field;
            $html_contents = array();
            $page = (Request::get('page'))?Request::get('page'):1;
            $number = ($page-1)*$limit+1;
            foreach($data['result'] as $row) {
                $html_content = array();

                if($this->button_bulk_action) {

                    $html_content[] = "<input type='checkbox' class='checkbox' name='checkbox[]' value='".$row->{$tablePK}."'/>";
                }

                if($this->show_numbering) {
                    $html_content[] = $number.'. ';
                    $number++;
                }

                foreach($columns_table as $col) {
                    if($col['visible']===FALSE) continue;

                    $value = @$row->{$col['field']};
                    $title = @$row->{$this->title_field};
                    $label = $col['label'];

                    if(isset($col['image'])) {
                        if($value=='') {
                            $value = "<a  data-lightbox='roadtrip' rel='group_{{$table}}' title='$label: $title' href='".asset('vendor/crudbooster/avatar.jpg')."'><img width='40px' height='40px' src='".asset('vendor/crudbooster/avatar.jpg')."'/></a>";
                        }else{
                            $pic = (strpos($value,'http://')!==FALSE)?$value:asset($value);
                            $value = "<a data-lightbox='roadtrip'  rel='group_{{$table}}' title='$label: $title' href='".$pic."'><img width='40px' height='40px' src='".$pic."'/></a>";
                        }
                    }

                    if(@$col['download']) {
                        $url = (strpos($value,'http://')!==FALSE)?$value:asset($value).'?download=1';
                        if($value) {
                            $value = "<a class='btn btn-xs btn-primary' href='$url' target='_blank' title='Download File'><i class='fa fa-download'></i> Download</a>";
                        }else{
                            $value = " - ";
                        }
                    }

                    if($col['str_limit']) {
                        $value = trim(strip_tags($value));
                        $value = str_limit($value,$col['str_limit']);
                    }

                    if($col['nl2br']) {
                        $value = nl2br($value);
                    }

                    if($col['callback_php']) {
                        foreach($row as $k=>$v) {
                            $col['callback_php'] = str_replace("[".$k."]",$v,$col['callback_php']);
                        }
                        @eval("\$value = ".$col['callback_php'].";");
                    }

                    //New method for callback
                    if(isset($col['callback'])) {
                        $value = call_user_func($col['callback'],$row);
                    }


                    $datavalue = @unserialize($value);
                    if ($datavalue !== false) {
                        if($datavalue) {
                            $prevalue = [];
                            foreach($datavalue as $d) {
                                if($d['label']) {
                                    $prevalue[] = $d['label'];
                                }
                            }
                            if(count($prevalue)) {
                                $value = implode(", ",$prevalue);
                            }
                        }
                    }

                    $html_content[] = $value;
                } //end foreach columns_table


                if($this->button_table_action):

                    $button_action_style = $this->button_action_style;
                    $html_content[] = "<div class='button_action' style='text-align:right'>".view('crudbooster::components.action',compact('addaction','row','button_action_style','parent_field'))->render()."</div>";

                endif;//button_table_action


                foreach($html_content as $i=>$v) {
                    $this->hook_row_index($i,$v);
                    $html_content[$i] = $v;
                }

                $html_contents[] = $html_content;
            } //end foreach data[result]


            $html_contents = ['html'=>$html_contents,'data'=>$data['result']];

            $data['html_contents'] = $html_contents;
            $data['is_search_form'] = $this->is_search_form;
            $data['search_forms'] = $this->search_form;

            return view("index",$data);
        }

        public function postAddSave() {
            $this->cbLoader();
            if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE) {
                CRUDBooster::insertLog(trans('crudbooster.log_try_add_save',['name'=>Request::input($this->title_field),'module'=>CRUDBooster::getCurrentModule()->name ]));
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

            $this->validation();
            $this->input_assignment();

            if(Schema::hasColumn($this->table, 'created_at'))
            {
                $this->arr['created_at'] = date('Y-m-d H:i:s');
            }

            //add by NguyenVT
            if (Schema::hasColumn($this->table, 'created_by')) {
                $this->arr['created_by'] = CRUDBooster::myId();
            }

            $this->hook_before_add($this->arr);


            $this->arr[$this->primary_key] = $id = CRUDBooster::newId($this->table);
            DB::table($this->table)->insert($this->arr);


            //Looping Data Input Again After Insert
            foreach($this->data_inputan as $ro) {
                $name = $ro['name'];
                if(!$name) continue;

                $inputdata = Request::get($name);

                //Insert Data Checkbox if Type Datatable
                if($ro['type'] == 'checkbox') {
                    if($ro['relationship_table']) {
                        $datatable = explode(",",$ro['datatable'])[0];
                        $foreignKey2 = CRUDBooster::getForeignKey($datatable,$ro['relationship_table']);
                        $foreignKey = CRUDBooster::getForeignKey($this->table,$ro['relationship_table']);
                        DB::table($ro['relationship_table'])->where($foreignKey,$id)->delete();

                        if($inputdata) {
                            $relationship_table_pk = CB::pk($ro['relationship_table']);
                            foreach($inputdata as $input_id) {
                                DB::table($ro['relationship_table'])->insert([
                                    $relationship_table_pk=>CRUDBooster::newId($ro['relationship_table']),
                                    $foreignKey=>$id,
                                    $foreignKey2=>$input_id
                                ]);
                            }
                        }

                    }
                }


                if($ro['type'] == 'select2') {
                    if($ro['relationship_table']) {
                        $datatable = explode(",",$ro['datatable'])[0];
                        $foreignKey2 = CRUDBooster::getForeignKey($datatable,$ro['relationship_table']);
                        $foreignKey = CRUDBooster::getForeignKey($this->table,$ro['relationship_table']);
                        DB::table($ro['relationship_table'])->where($foreignKey,$id)->delete();

                        if($inputdata) {
                            foreach($inputdata as $input_id) {
                                $relationship_table_pk = CB::pk($ro['relationship_table']); //CB::pk($row['relationship_table']); edit by NguyenVT: $row -> $ro
                                DB::table($ro['relationship_table'])->insert([
                                    $relationship_table_pk=>CRUDBooster::newId($ro['relationship_table']),
                                    $foreignKey=>$id,
                                    $foreignKey2=>$input_id
                                ]);
                            }
                        }

                    }
                }

                if($ro['type']=='child') {
                    $name = str_slug($ro['label'],'');
                    $columns = $ro['columns'];
                    $count_input_data = count(Request::get($name.'-'.$columns[0]['name']))-1;
                    $child_array = [];

                    for($i=0;$i<=$count_input_data;$i++) {
                        $fk = $ro['foreign_key'];
                        $column_data = [];
                        $column_data[$fk] = $id;
                        foreach($columns as $col) {
                            $colname = $col['name'];
                            $column_data[$colname] = Request::get($name.'-'.$colname)[$i];
                        }
                        $child_array[] = $column_data;
                    }

                    $childtable = CRUDBooster::parseSqlTable($ro['table'])['table'];
                    DB::table($childtable)->insert($child_array);
                }



            }


            $this->hook_after_add($this->arr[$this->primary_key]);


            $this->return_url = ($this->return_url)?$this->return_url:Request::get('return_url');

            //insert log
            CRUDBooster::insertLog(trans("crudbooster.log_add",['name'=>$this->arr[$this->title_field],'module'=>CRUDBooster::getCurrentModule()->name]));

            if($this->return_url) {
                if(Request::get('submit') == trans('crudbooster.button_save_more')) {
                    CRUDBooster::redirect(Request::server('HTTP_REFERER'),trans("crudbooster.alert_add_data_success"),'success');
                }else{
                    CRUDBooster::redirect($this->return_url,trans("crudbooster.alert_add_data_success"),'success');
                }

            }else{
                if(Request::get('submit') == trans('crudbooster.button_save_more')) {
                    CRUDBooster::redirect(CRUDBooster::mainpath('add'),trans("crudbooster.alert_add_data_success"),'success');
                }else{
                    CRUDBooster::redirect(CRUDBooster::mainpath(),trans("crudbooster.alert_add_data_success"),'success');
                }
            }
        }
        public function postEditSave($id) {
            $this->cbLoader();
            $row = DB::table($this->table)->where($this->primary_key,$id)->first();

            if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {
                CRUDBooster::insertLog(trans("crudbooster.log_try_add",['name'=>$row->{$this->title_field},'module'=>CRUDBooster::getCurrentModule()->name]));
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
            }

            $this->validation($id);
            $this->input_assignment($id);

            if (Schema::hasColumn($this->table, 'updated_at'))
            {
                $this->arr['updated_at'] = date('Y-m-d H:i:s');
            }

            //add new by NguyenVT
            if (Schema::hasColumn($this->table, 'updated_by')) {
                $this->arr['updated_by'] = CRUDBooster::myId();
            }

            $this->hook_before_edit($this->arr,$id);
            DB::table($this->table)->where($this->primary_key,$id)->update($this->arr);

            //Looping Data Input Again After Insert
            foreach($this->data_inputan as $ro) {
                $name = $ro['name'];
                if(!$name) continue;

                $inputdata = Request::get($name);

                //Insert Data Checkbox if Type Datatable
                if($ro['type'] == 'checkbox') {
                    if($ro['relationship_table']) {
                        $datatable = explode(",",$ro['datatable'])[0];

                        $foreignKey2 = CRUDBooster::getForeignKey($datatable,$ro['relationship_table']);
                        $foreignKey = CRUDBooster::getForeignKey($this->table,$ro['relationship_table']);
                        DB::table($ro['relationship_table'])->where($foreignKey,$id)->delete();

                        if($inputdata) {
                            foreach($inputdata as $input_id) {
                                $relationship_table_pk = CB::pk($ro['relationship_table']);
                                DB::table($ro['relationship_table'])->insert([
                                    $relationship_table_pk=>CRUDBooster::newId($ro['relationship_table']),
                                    $foreignKey=>$id,
                                    $foreignKey2=>$input_id
                                ]);
                            }
                        }


                    }
                }


                if($ro['type'] == 'select2') {
                    if($ro['relationship_table']) {
                        $datatable = explode(",",$ro['datatable'])[0];

                        $foreignKey2 = CRUDBooster::getForeignKey($datatable,$ro['relationship_table']);
                        $foreignKey = CRUDBooster::getForeignKey($this->table,$ro['relationship_table']);
                        DB::table($ro['relationship_table'])->where($foreignKey,$id)->delete();

                        if($inputdata) {
                            foreach($inputdata as $input_id) {
                                $relationship_table_pk = CB::pk($ro['relationship_table']);
                                DB::table($ro['relationship_table'])->insert([
                                    $relationship_table_pk=>CRUDBooster::newId($ro['relationship_table']),
                                    $foreignKey=>$id,
                                    $foreignKey2=>$input_id
                                ]);
                            }
                        }


                    }
                }

                if($ro['type']=='child') {
                    $name = str_slug($ro['label'],'');
                    $columns = $ro['columns'];
                    $count_input_data = count(Request::get($name.'-'.$columns[0]['name']))-1;
                    $child_array = [];
                    $childtable = CRUDBooster::parseSqlTable($ro['table'])['table'];
                    $fk = $ro['foreign_key'];

                    DB::table($childtable)->where($fk,$id)->delete();
                    $lastId = CRUDBooster::newId($childtable);
                    $childtablePK = CB::pk($childtable);

                    for($i=0;$i<=$count_input_data;$i++) {

                        $column_data = [];
                        $column_data[$childtablePK] = $lastId;
                        $column_data[$fk] = $id;
                        foreach($columns as $col) {
                            $colname = $col['name'];
                            $column_data[$colname] = Request::get($name.'-'.$colname)[$i];
                        }
                        $child_array[] = $column_data;

                        $lastId++;
                    }

                    $child_array = array_reverse($child_array);

                    DB::table($childtable)->insert($child_array);
                }


            }

            $this->hook_after_edit($id);


            $this->return_url = ($this->return_url)?$this->return_url:Request::get('return_url');

            //insert log
            $old_values = json_decode(json_encode($row),true);
            CRUDBooster::insertLog(trans("crudbooster.log_update",['name'=>$this->arr[$this->title_field],'module'=>CRUDBooster::getCurrentModule()->name]), LogsController::displayDiff($old_values, $this->arr));

            if($this->return_url) {
                CRUDBooster::redirect($this->return_url,trans("crudbooster.alert_update_data_success"),'success');
            }else{
                if(Request::get('submit') == trans('crudbooster.button_save_more')) {
                    CRUDBooster::redirect(CRUDBooster::mainpath('add'),trans("crudbooster.alert_update_data_success"),'success');
                }else{
                    CRUDBooster::redirect(CRUDBooster::mainpath(),trans("crudbooster.alert_update_data_success"),'success');
                }
            }
        }
        public function getDelete($id) {
            $this->cbLoader();
            $row = DB::table($this->table)->where($this->primary_key,$id)->first();

            if(!CRUDBooster::isDelete() && $this->global_privilege==FALSE || $this->button_delete==FALSE) {
                CRUDBooster::insertLog(trans("crudbooster.log_try_delete",['name'=>$row->{$this->title_field},'module'=>CRUDBooster::getCurrentModule()->name]));
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
            }

            //insert log
            CRUDBooster::insertLog(trans("crudbooster.log_delete",['name'=>$row->{$this->title_field},'module'=>CRUDBooster::getCurrentModule()->name]));

            $this->hook_before_delete($id);

            // edit by NguyenVT
            if(CRUDBooster::isColumnExists($this->table,'deleted_at')) {
                $delete_query = ['deleted_at'=>date('Y-m-d H:i:s')];
                if(CRUDBooster::isColumnExists($this->table,'deleted_by')) {
                    $delete_query['deleted_by'] = CRUDBooster::myId();
                }
                DB::table($this->table)->where($this->primary_key,$id)->update($delete_query);
            }else{
                DB::table($this->table)->where($this->primary_key,$id)->delete();
            }


            $this->hook_after_delete($id);

            $url = g('return_url')?:CRUDBooster::referer();

            CRUDBooster::redirect($url,trans("crudbooster.alert_delete_data_success"),'success');
        }
	}