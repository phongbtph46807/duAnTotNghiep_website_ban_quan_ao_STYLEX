<?php $__env->startSection('title', 'Quản lý Role'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Danh sách Role</h4>
    <a href="<?php echo e(route('admin.rbac.roles.create')); ?>" class="btn btn-primary">Tạo Role</a>
  </div>

  <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?php echo e(session('success')); ?>

      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?php echo e(session('error')); ?>

      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <div class="card">
    <div class="card-body table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>Tên</th>
            <th>Mô tả</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php
            $isAdmin = strtolower($role->name) === 'admin';
            $userCount = $roleUserCounts[$role->id] ?? 0;
            $canDelete = !$isAdmin && $userCount == 0;
          ?>
          <tr>
            <td><?php echo e($role->id); ?></td>
            <td>
              <div>
                <strong><?php echo e($role->name); ?></strong>
                <?php if($isAdmin): ?>
                  <span class="badge bg-danger ms-2">Bắt buộc</span>
                <?php endif; ?>
              </div>
              <small class="text-muted">(<?php echo e($userCount); ?> tài khoản)</small>
            </td>
            <td><?php echo e($role->description); ?></td>
            <td>
              <div class="d-flex gap-2 align-items-center">
              <a class="btn btn-sm btn-warning" href="<?php echo e(route('admin.rbac.roles.edit', $role)); ?>">Sửa</a>
                <?php if($canDelete): ?>
              <form class="d-inline" method="POST" action="<?php echo e(route('admin.rbac.roles.destroy', $role)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa role này?')">Xóa</button>
              </form>
                <?php else: ?>
                  <button class="btn btn-sm btn-danger" disabled title="<?php echo e($isAdmin ? 'Không thể xóa role Admin' : 'Role đang được sử dụng bởi ' . $userCount . ' tài khoản'); ?>">
                    Xóa
                  </button>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
      <div>
        <?php echo e($roles->links()); ?>

      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\duAnTotNghiep_website_ban_quan_ao_STYLEX\resources\views\admin\rbac\roles\index.blade.php ENDPATH**/ ?>