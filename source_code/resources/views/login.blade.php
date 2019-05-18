@extends('layout')
@section('content')

@push('head')
	<style>
		body {			
			background: url('{{asset("images/pexels-photo-811108.jpeg")}}') no-repeat center center fixed; 
			  -webkit-background-size: cover;
			  -moz-background-size: cover;
			  -o-background-size: cover;
			  background-size: cover;
		}
	</style>
@endpush

<div class="row" style="margin-top: 100px">
	<div class="col-sm-4"></div>
	<!-- /.col-sm-4 -->
	<div class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="glyphicon glyphicon-user"></i> Login
			</div>
			<!-- /.panel-heading -->
			<form action="" method="post">
				{!! csrf_field() !!}
			<div class="panel-body">
				
				@if(Session::get('message'))
					<div class="alert alert-{{Session::get('message_type')}}">{!! Session::get('message') !!}</div>
					<!-- /.alert -->
				@endif


				<div class="form-group">
					<label for="">Username</label>
					<input type="text" name="username" required class="form-control" />
				</div>
				<!-- /.form-group -->
				<div class="form-group">
					<label for="">Password</label>
					<input type="password" required name="password" class="form-control" />
				</div>
				<!-- /.form-group -->
			</div>
			<!-- /.panel-body -->
			<div class="panel-footer">
				<input type="submit" value="Login" class="btn btn-primary" />
			</div>
			<!-- /.panel-footer -->
			</form>
		</div>
		<!-- /.panel panel-default -->
	</div>
	<!-- /.col-sm-4 -->

	<div class="col-sm-4"></div>
	<!-- /.col-sm-4 -->
</div>
<!-- /.row -->

@endsection