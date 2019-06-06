@extends('layouts.backoffice')

@section('title', 'Staff profile')

@section('content')
<div class="change-password">
        <h4>Change password</h4>
        <form>
            <div class="form-row">
              <div class="form-group required col-md-6">
                <label class="control-label" for="old_password">Password (old)</label>
                <input type="password" name="old_password" class="form-control" id="old_password" placeholder="Enter old password" required />
              </div>
              <div class="form-group required col-md-6">
                <label class="control-label" for="new_password">Password (new)</label>
                <input type="password" name="new_password" class="form-control" id="new_password" placeholder="Enter new password" required />
              </div>
            </div>
            <input type="hidden" name="user_id" value="{{$user->id}}" />
            <button type="submit" class="btn btn-lg btn-primary">
              <span class="glyphicon glyphicon-earphone pull-left"><i class="fas fa-save"></i></span>
              Save
            </button>
          </form>
      </div>
@endsection