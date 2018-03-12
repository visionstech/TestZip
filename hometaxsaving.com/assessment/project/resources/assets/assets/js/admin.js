jQuery(document).ready(function($) {
    /* now you can use $ */
        $('#stateId').on('change', function() {
			this.form.submit();                          
        });
		
		$('#lookupTypeId').on('change', function() {
			this.form.submit();                          
        });
    });