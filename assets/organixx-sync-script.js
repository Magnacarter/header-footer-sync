jQuery(function($)
{
	$('.toplevel_page_sync_header_footer .submit #submit').on('click', function(e)
	{
		e.preventDefault();
		$.ajax(
		{
			type: 'POST',
			dataType: 'json',
			url: ajaxurl,
			data: {
				action: 'process_ajax',
				buttonValue: $(this).val()
			},
			success: function(response)
			{
				if (response.success === true)
				{
					var json = response.data;
					$('.form-table tr th').html(json);
				}
				if ( response.success === false )
				{
					var json = response.data;
					$('.form-table tr th').html(json);
				}
			}
		});
	});
});
