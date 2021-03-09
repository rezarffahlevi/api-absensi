<?= $this->extend(env('theme.name').'/'.env('theme.private')) ?>

<?= $this->section('stylesheet') ?>
<?= $this->endSection('stylesheet') ?>

<?= $this->section('javascript') ?>
<?= $this->endSection('javascript') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-4">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?=$box_title?></h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <?= form_open('', ['id' => 'exampleForm']); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="name" name="name" id="name" class="form-control" value="<?=empty($user) ? old('name') : $user['name'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?=empty($user) ? old('email') : $user['email'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="address">Address:</label>
                        <textarea name="address" id="address" class="form-control"><?=empty($user) ? old('address') : $user['address'] ?></textarea>
                    </div>
                    <?php
                        if (! empty($errors)) {
                            $html_error = '<div class="alert alert-danger">';
                            foreach ($errors as $error) {
                                $html_error .= "<p>{$error}</p>";
                            }
                            $html_error .= '</div>';
                            echo $html_error;
                        }
                    ?>
                </div>
                <!-- /.box-body -->

                <div class="box-footer">
                    <button type="button" class="btn btn-danger" onclick="history.go(-1);">Back</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<?= $this->endSection('content') ?>
