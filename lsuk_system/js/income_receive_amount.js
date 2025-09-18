	
	$(function () {
		// Initial check on page load
		getBankInfos();

		// Re-check when payment type changes
		$('#payment_type').on('change', getBankInfos);
	});
	
	function getBankInfos() {
		var paymentType = $('#payment_type').val();
		var $paymentThrough = $('#payment_through');
		var $wrapper = $('.payment_through_wrap');

		if (paymentType) {
			// Build query string using jQuery
			var queryString = window.location.search;
			if (queryString.indexOf('?') === -1) {
				queryString = '?';
			} else if (!queryString.endsWith('&') && queryString !== '?') {
				queryString += '&';
			}

			queryString += 'type=' + encodeURIComponent(paymentType) + '&action=get_payment_types';

			$.ajax({
				type: "GET",
				url: "ajax_functions.php" + queryString,
				success: function (response) {
					$paymentThrough.html(response).attr('required', true);
					$wrapper.removeClass('hide');
				},
				error: function () {
					alert("Unable to fetch payment methods. Please try again.");
				}
			});
		} else {
			$paymentThrough.attr('required', false);
			$wrapper.addClass('hide');
		}
	}

	
	function addNewPaymentMode() {
		const $modal = $('#myModal2');
		const paymentType = $('#payment_type').val();

		// Prepare and show modal
		$modal.find('.modal-content').addClass('modal-lg');
		$modal.modal('show');

		// Load content via AJAX
		$.ajax({
			type: "GET",
			url: "ajax_functions.php",
			data: {
				action: "add_payment_method",
				type: paymentType
			},
			success: function (response) {
				$modal.find('.modal_details').html(response);
			},
			error: function () {
				alert("Failed to load payment method form. Please try again.");
			}
		});
	}

	
	function savePaymentMethod() {
		const $bankTitle = $('#bank_title');
		const $accountNo = $('#account_no');
		const isCash = $('#MM_is_cash').val() == 1;

		let isValid = true;

		// Validate bank title
		if ($bankTitle.val().trim() === '') {
			$bankTitle.focus().css('border', '1px solid red');
			isValid = false;
		} else {
			$bankTitle.css('border', '');
		}

		// Validate account number if not cash
		if (!isCash && $accountNo.val().trim() === '') {
			$accountNo.focus().css('border', '1px solid red');
			isValid = false;
		} else {
			$accountNo.css('border', '');
		}

		if (!isValid) return false;

		$('#MM_is_validated').val(1);

		// Proceed with AJAX
		$.ajax({
			type: "POST",
			url: "ajax_functions.php",
			data: $('#frmSavePaymentMethod').serialize(),
			success: function (response) {
				if (response == 1001) {
					alert("Record already exists with same Name/Title.");
				} else {
					$('#myModal2').modal('hide');
					$('#payment_through').html(response);
				}
			},
			error: function () {
				alert("Oops! Something went wrong, please try again.");
			}
		});
	}

	function generateVoucherNo(is_payable, payment_type) {
      
      var vch = '';

      if (is_payable == 1) {
        vch = 'JV';
      } else {
        vch = payment_type === 'cash' ? 'CPV' : 'BPV';
      }

      // AJAX call to fetch count from PHP
      $.ajax({
        url: 'ajax_functions.php',
        type: 'GET',
        data: {
          voucher_type: vch,
          action: 'getNextVoucherCount',
        },
        success: function(vch_count) {
          var new_voucher = vch + '-' + vch_count;
          $('#voucher').val(new_voucher); // Fill full voucher number
        },
        error: function() {
          alert('Error fetching voucher count.');
        }
      });
    }