@extends('backend.admin_master')
@section('admin')

<div class="page-content">
  <div class="container-fluid">

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">

            <h4 class="card-title">Change Password Page</h4><br><br>

            <!-- burada parola değiştirme için error oluşturduk -->
              @if(count($errors))
                @foreach ($errors->all() as $error)
                <p class="alert alert-danger alert-dismissible fade show"> {{$error}} </p>
                @endforeach

              @endif
            <form method="post" action="{{ route('update.password') }}">
              @csrf

              <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Old Password:</label>
                <div class="col-sm-10">
                  <input class="form-control" name="oldpassword" type="password" id="oldpassword">
                </div>
              </div>
              <!-- end row -->

              <div class="row mb-3">
                <label class="col-sm-2 col-form-label">New Password:</label>
                <div class="col-sm-10">
                  <input class="form-control" name="newpassword" type="password" id="newpassword">
                </div>
              </div>
              <!-- end row -->

              <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Confirm Password:</label>
                <div class="col-sm-10">
                  <input class="form-control" name="confirm_password" type="password" id="confirm_password">
                </div>
              </div>
              <!-- end row -->

              <input type="submit" class="btn btn-info waves-effect waves-light" value="Change Password">
            </form>

          </div>
        </div>
      </div> <!-- end col -->
    </div>

  </div>
</div>
@endsection
