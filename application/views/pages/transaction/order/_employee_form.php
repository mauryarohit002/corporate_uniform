<?php $this->load->view('templates/header');?>
<script>
    let link 		= "<?php echo $menu; ?>";
    let sub_link 	= "<?php echo $sub_menu; ?>";
</script>
<section class="sticky_top"> 
    <?php $this->load->view('pages/'.$menu.'/'.$sub_menu.'/employee_form/_navbar'); ?>
</section>
<section class="container-fluid my-3">
    <form class="form-horizontal" id="_employee_form">
        <?php $this->load->view('pages/'.$menu.'/'.$sub_menu.'/employee_form/_body'); ?>
    </form>
</section>
<?php $this->load->view('templates/footer'); ?>
<?php $this->load->view('pages/'.$menu.'/'.$sub_menu.'/employee_form/_footer'); ?>
</body>
</html>