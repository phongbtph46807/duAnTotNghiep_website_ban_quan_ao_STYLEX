<?php $__env->startSection('content'); ?>
    <form action="<?php echo e(route('admin.post.update', $post->id)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="title">Title</label>
                        <input type="text" class="form-control" name="title" id="title" value="<?php echo e(old('title', $post->title)); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="slug">Slug</label>
                        <input type="text" class="form-control" name="slug" id="slug" value="<?php echo e(old('slug', $post->slug)); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Nội dung bài viết</label>
                        <textarea id="content" name="content" class="form-control" rows="6"><?php echo e(old('content', $post->content)); ?></textarea>
                    </div>
                </div>
            </div>
            <div class="text-end mb-4">
                <a href="<?php echo e(route('admin.post.index')); ?>" class="btn btn-secondary w-sm">Back</a>
                <button type="submit" class="btn btn-success w-sm">Update</button>
                <form action="<?php echo e(route('admin.post.destroy', $post->id)); ?>" method="POST" style="display:inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger w-sm" onclick="return confirm('Bạn có chắc muốn xóa bài viết này?');">Delete</button>
                </form>
            </div>
        </div>
    </form>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            ClassicEditor
                .create(document.querySelector('#content'), {
                    toolbar: {
                        items: [
                            'heading', '|',
                            'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|',
                            'blockQuote', 'undo', 'redo'
                        ]
                    },
                    language: 'vi'
                })
                .then(editor => {})
                .catch(error => {
                    console.error(error);
                });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\LARAGON\laragon\www\DATN\duAnTotNghiep_website_ban_quan_ao_STYLEX\resources\views\admin\post\edit.blade.php ENDPATH**/ ?>