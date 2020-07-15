<div class="error-page">
    <h2 class="headline text-yellow"><?php echo riake('error-type', $_item);
                    ?></h2>
    <div class="error-content">
        <h3><i class="fa fa-warning text-yellow"></i> <?php echo riake('title', $_item);
                    ?></h3>
        <p>
            <?php echo riake('content', $_item);
                    ?>
        </p>
        <!--
                    <form class="search-form">
                    <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search">
                    <div class="input-group-btn">
                    <button type="submit" name="submit" class="btn btn-warning btn-flat"><i class="fa fa-search"></i></button>
                    </div>
                    </div>
                    </form>
                    -->
    </div><!-- /.error-content -->
</div>