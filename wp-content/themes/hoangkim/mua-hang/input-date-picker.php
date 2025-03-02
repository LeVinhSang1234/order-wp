<input class="datepicker" id="<?php echo (isset($id) ? $id : ''); ?>" placeholder="<?php echo (isset($placeholder) ? $placeholder : ''); ?>" />
<script>
    $(function() {
        var id = "<?php echo (isset($id) ? $id : ''); ?>";
        console.log(id)
        if (!id) return
        $(`#${id}`).datepicker();
    });
</script>