@extends('layouts.backoffice')

@section('content')
<div class="change-password">
        <h4>Change password</h4>
        <form>
          <form>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="oldPassword">Password (old)</label>
                <input type="password" name="oldPassword" class="form-control" id="oldPassword" placeholder="Enter old password" required />
              </div>
              <div class="form-group col-md-6">
                <label for="newPassword">Password (new)</label>
                <input type="password" name="newPassword" class="form-control" id="newPassword" placeholder="Enter new password" required />
              </div>
            </div>
            <button class="btn btn-lg btn-primary">
              <span class="glyphicon glyphicon-earphone pull-left"><i class="fas fa-save"></i></span>
              Save
            </button>
          </form>
        </form>
      </div>
@endsection