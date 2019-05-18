
<?php
//Loading input components
if($search_forms && count($search_forms)>0){
    $asset_already = [];
    foreach($search_forms as $index=>$form) {

    $name = $form['name'];
    @$value = (isset($form['value'])) ? $form['value'] : '';

    $type = @$form['type']?:'text';
    $col_width = @$form['width'] ?: "col-sm-9";

    if(!in_array($type, $asset_already)){
    ?>
    @if(file_exists(base_path('/vendor/crocodicstudio/crudbooster/src/views/default/type_components/'.$type.'/asset.blade.php')))
        @include('crudbooster::default.type_components.'.$type.'.asset')
    @elseif(file_exists(resource_path('views/vendor/crudbooster/type_components/'.$type.'/asset.blade.php')))
        @include('vendor.crudbooster.type_components.'.$type.'.asset')
    @endif
    <?php
    $asset_already[] = $type;
    }
    ?>
    @if($type == 'break_line')
        <div class="input-group">
                <input type="text" disabled class="form-control notfocus invisible">
        </div><br/>
    @elseif(file_exists(resource_path('views/vendor/crudbooster/type_components/'.$type.'/component.blade.php')))
        @include('vendor.crudbooster.type_components.'.$type.'.component')
    @elseif(file_exists(base_path('/vendor/crocodicstudio/crudbooster/src/views/default/type_components/'.$type.'/component.blade.php')))
        @include('crudbooster::default.type_components.'.$type.'.component')
    @else
        <p class='text-danger'>{{$type}} is not found in type component system</p><br/>
    @endif
    <?php
    }
}