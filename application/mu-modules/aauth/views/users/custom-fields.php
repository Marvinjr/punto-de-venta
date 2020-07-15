<?php
$skin = riake('user_id', $config) ? $this->options->get('theme-skin', riake('user_id', $config)) : '';
?>
<h3><?php _e('Select a theme', 'aauth');
        ?></h3>
<ul class="list-unstyled clearfix theme-selector">
    <li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0);" data-skin="skin-blue"
            style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover <?php echo $skin == 'skin-blue' ? 'active' : '';
        ?>">
            <div><span style="display:block; width: 20%; float: left; height: 7px; background: #367fa9;"></span><span
                    class="bg-light-blue" style="display:block; width: 80%; float: left; height: 7px;"></span></div>
            <div><span style="display:block; width: 20%; float: left; height: 50px; background: #222d32;"></span><span
                    style="display:block; width: 80%; float: left; height: 50px; background: #f4f5f7;"></span></div>
        </a>
        <p class="text-center no-margin"><?php _e('Blue', 'aauth');
        ?></p>
    </li>
    <li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0);" data-skin="skin-black"
            style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover <?php echo $skin == 'skin-black' ? 'active' : '';
        ?>">
            <div style="box-shadow: 0 0 2px rgba(0,0,0,0.1)" class="clearfix"><span
                    style="display:block; width: 20%; float: left; height: 7px; background: #fefefe;"></span><span
                    style="display:block; width: 80%; float: left; height: 7px; background: #fefefe;"></span></div>
            <div><span style="display:block; width: 20%; float: left; height: 50px; background: #222;"></span><span
                    style="display:block; width: 80%; float: left; height: 50px; background: #f4f5f7;"></span></div>
        </a>
        <p class="text-center no-margin"><?php _e('Black', 'aauth');
        ?></p>
    </li>
    <li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0);" data-skin="skin-purple"
            style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover <?php echo $skin == 'skin-purple' ? 'active' : '';
        ?>">
            <div><span style="display:block; width: 20%; float: left; height: 7px;"
                    class="bg-purple-active"></span><span class="bg-purple"
                    style="display:block; width: 80%; float: left; height: 7px;"></span></div>
            <div><span style="display:block; width: 20%; float: left; height: 50px; background: #222d32;"></span><span
                    style="display:block; width: 80%; float: left; height: 50px; background: #f4f5f7;"></span></div>
        </a>
        <p class="text-center no-margin"><?php _e('Purple', 'aauth');
        ?></p>
    </li>
    <li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0);" data-skin="skin-green"
            style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover <?php echo $skin == 'skin-green' ? 'active' : '';
        ?>">
            <div><span style="display:block; width: 20%; float: left; height: 7px;" class="bg-green-active"></span><span
                    class="bg-green" style="display:block; width: 80%; float: left; height: 7px;"></span></div>
            <div><span style="display:block; width: 20%; float: left; height: 50px; background: #222d32;"></span><span
                    style="display:block; width: 80%; float: left; height: 50px; background: #f4f5f7;"></span></div>
        </a>
        <p class="text-center no-margin"><?php _e('Green', 'aauth');
        ?></p>
    </li>
    <li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0);" data-skin="skin-red"
            style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover <?php echo $skin == 'skin-red' ? 'active' : '';
        ?>">
            <div><span style="display:block; width: 20%; float: left; height: 7px;" class="bg-red-active"></span><span
                    class="bg-red" style="display:block; width: 80%; float: left; height: 7px;"></span></div>
            <div><span style="display:block; width: 20%; float: left; height: 50px; background: #222d32;"></span><span
                    style="display:block; width: 80%; float: left; height: 50px; background: #f4f5f7;"></span></div>
        </a>
        <p class="text-center no-margin"><?php _e('Red', 'aauth');
        ?></p>
    </li>
    <li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0);" data-skin="skin-yellow"
            style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover <?php echo $skin == 'skin-yellow' ? 'active' : '';
        ?>">
            <div><span style="display:block; width: 20%; float: left; height: 7px;"
                    class="bg-yellow-active"></span><span class="bg-yellow"
                    style="display:block; width: 80%; float: left; height: 7px;"></span></div>
            <div><span style="display:block; width: 20%; float: left; height: 50px; background: #222d32;"></span><span
                    style="display:block; width: 80%; float: left; height: 50px; background: #f4f5f7;"></span></div>
        </a>
        <p class="text-center no-margin"><?php _e('Yellow', 'aauth');
        ?></p>
    </li>
    <li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0);" data-skin="skin-blue-light"
            style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover <?php echo $skin == 'skin-blue-light' ? 'active' : '';
        ?>">
            <div><span style="display:block; width: 20%; float: left; height: 7px; background: #367fa9;"></span><span
                    class="bg-light-blue" style="display:block; width: 80%; float: left; height: 7px;"></span></div>
            <div><span style="display:block; width: 20%; float: left; height: 50px; background: #f9fafc;"></span><span
                    style="display:block; width: 80%; float: left; height: 50px; background: #f4f5f7;"></span></div>
        </a>
        <p class="text-center no-margin" style="font-size: 12px"><?php _e('Blue Light', 'aauth');
        ?></p>
    </li>
    <li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0);" data-skin="skin-black-light"
            style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover <?php echo $skin == 'skin-black-light' ? 'active' : '';
        ?>">
            <div style="box-shadow: 0 0 2px rgba(0,0,0,0.1)" class="clearfix"><span
                    style="display:block; width: 20%; float: left; height: 7px; background: #fefefe;"></span><span
                    style="display:block; width: 80%; float: left; height: 7px; background: #fefefe;"></span></div>
            <div><span style="display:block; width: 20%; float: left; height: 50px; background: #f9fafc;"></span><span
                    style="display:block; width: 80%; float: left; height: 50px; background: #f4f5f7;"></span></div>
        </a>
        <p class="text-center no-margin" style="font-size: 12px"><?php _e('Black Light', 'aauth');
        ?></p>
    </li>
    <li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0);" data-skin="skin-purple-light"
            style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover <?php echo $skin == 'skin-purple-light' ? 'active' : '';
        ?>">
            <div><span style="display:block; width: 20%; float: left; height: 7px;"
                    class="bg-purple-active"></span><span class="bg-purple"
                    style="display:block; width: 80%; float: left; height: 7px;"></span></div>
            <div><span style="display:block; width: 20%; float: left; height: 50px; background: #f9fafc;"></span><span
                    style="display:block; width: 80%; float: left; height: 50px; background: #f4f5f7;"></span></div>
        </a>
        <p class="text-center no-margin" style="font-size: 12px"><?php _e('Purple Light', 'aauth');
        ?></p>
    </li>
    <li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0);" data-skin="skin-green-light"
            style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover <?php echo $skin == 'skin-green-light' ? 'active' : '';
        ?>">
            <div><span style="display:block; width: 20%; float: left; height: 7px;" class="bg-green-active"></span><span
                    class="bg-green" style="display:block; width: 80%; float: left; height: 7px;"></span></div>
            <div><span style="display:block; width: 20%; float: left; height: 50px; background: #f9fafc;"></span><span
                    style="display:block; width: 80%; float: left; height: 50px; background: #f4f5f7;"></span></div>
        </a>
        <p class="text-center no-margin" style="font-size: 12px"><?php _e('Green Light', 'aauth');
        ?></p>
    </li>
    <li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0);" data-skin="skin-red-light"
            style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover <?php echo $skin == 'skin-red-light' ? 'active' : '';
        ?>">
            <div><span style="display:block; width: 20%; float: left; height: 7px;" class="bg-red-active"></span><span
                    class="bg-red" style="display:block; width: 80%; float: left; height: 7px;"></span></div>
            <div><span style="display:block; width: 20%; float: left; height: 50px; background: #f9fafc;"></span><span
                    style="display:block; width: 80%; float: left; height: 50px; background: #f4f5f7;"></span></div>
        </a>
        <p class="text-center no-margin" style="font-size: 12px"><?php _e('Red Light', 'aauth');
        ?></p>
    </li>
    <li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0);" data-skin="skin-yellow-light"
            style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover <?php echo $skin == 'skin-yellow-light' ? 'active' : '';
        ?>">
            <div><span style="display:block; width: 20%; float: left; height: 7px;"
                    class="bg-yellow-active"></span><span class="bg-yellow"
                    style="display:block; width: 80%; float: left; height: 7px;"></span></div>
            <div><span style="display:block; width: 20%; float: left; height: 50px; background: #f9fafc;"></span><span
                    style="display:block; width: 80%; float: left; height: 50px; background: #f4f5f7;"></span></div>
        </a>
        <p class="text-center no-margin" style="font-size: 12px;"><?php _e('Yellow Light', 'aauth');
        ?></p>
    </li>
</ul>
<input type="hidden" name="theme-skin" value="<?php echo $skin;?>" />
<style>
    .theme-selector li a.active {
        opacity: 1 !important;
        box-shadow: 0px 0px 0px 5px #3c8dbc !important;
    }
</style>
<script>
    $('.theme-selector li a').each(function () {
        $(this).bind('click', function () {
            // remove active status
            $('.theme-selector li a').each(function () {
                $(this).removeClass('active');
            });

            $(this).toggleClass('active');
            $('input[name="theme-skin"]').val($(this).data('skin'));
            // console.log( $(this).data( 'skin' ) );
        });
    })
</script>