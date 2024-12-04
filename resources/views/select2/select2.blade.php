
<link rel="stylesheet" href="{{ asset_url('select2//select2.min.css') }}">
<style>

.select2-container .select2-selection--single{
	height: 35px;
}
.select2-container--default .select2-selection--single .select2-selection__rendered{
	line-height: 35px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow{
	height: 35px;
}
</style>
<script src="{{ asset_url('select2/select2.full.min.js') }}"></script>
<script>
	jQuery(document).ready(function(e) {
		seeker_skill_select2();
        players_select2();
	});

    function players_select2()
	{
		if($('.players_select2'))
		{
			$('.players_select2').select2({
				width: '100%',
				placeholder: "Select Players",
				allowClear: true
			});
		}
	}
	function seeker_skill_select2()
	{
		if($('.seeker_skill_select2'))
		{
			$('.seeker_skill_select2').select2({
				width: '100%',
				placeholder: "Select Skills",
				allowClear: true
			});
		}
	}
</script>
