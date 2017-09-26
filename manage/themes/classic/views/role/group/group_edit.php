<link rel="stylesheet" href="/themes/classic/statics/app/role/css/style.css">
<br>
<table class="table table-striped">
  <thead>
    <tr>
      <td width="20%">角色</td>
      <td>管理</td>
    </tr>
  </thead>
  <tbody>
    <?php foreach( $roles as $role ):?>
    <tr>
      <td>
        <?php echo CHtml::checkBox($role->role->roleId); echo $role->role->roleName;?>
      </td>
      <td></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
