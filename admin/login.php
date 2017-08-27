<div class="row">
  <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1">

    <br /><br />    

    <div class="panel panel-primary">
      <div class="panel-heading">
        Admin Login
      </div>
      <div class="panel-body">
        <form class="form" method="post" action="index.php">
          <input type="hidden" name="action" value="login" />

          <div class="form-body">
            <div class="row">
              <div class="col-xs-12">
                <label class="control-label">Username</label>
                <input type="text" name="username" class="form-control" />
              </div>
            </div>
            <div class="row">
              <div class="col-xs-12">
                <label class="control-label">Password</label>
                <input type="password" name="password" class="form-control" />
              </div>
            </div>
          </div>

          <div class="form-actions">
            <div class="row">
              <div class="col-xs-12">
                <button type="submit" class="btn btn-primary btn-block">Log In</button>
                <a href="../" class="btn btn-default btn-block">Cancel</a>
              </div>
            </div>
          </div>

        </form>
      </div>
    </div>

  </div>
</div>
