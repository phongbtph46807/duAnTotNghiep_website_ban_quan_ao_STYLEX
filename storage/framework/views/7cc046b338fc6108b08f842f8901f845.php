<?php $__env->startSection('content'); ?>
    <form action="<?php echo e(route('admin.post.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="title">Title</label>
                        <input type="text" class="form-control" name="title" id="title" placeholder="Enter project title" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="slug">Slug</label>
                        <input type="text" class="form-control" name="slug" id="slug" placeholder="Enter slug">
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Nội dung bài viết</label>
                        <textarea id="content" name="content" class="form-control" rows="6"></textarea>
                    </div>
                </div>
            </div>
            <div class="text-end mb-4">
                <button type="submit" class="btn btn-success w-sm">Create</button>
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

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\duAnTotNghiep_website_ban_quan_ao_STYLEX\resources\views/admin/post/create.blade.php ENDPATH**/ ?>