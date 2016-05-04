/**
 */
(function($) {
	$.entwine('ss', function($){

		$('.cms-edit-form button.payment-dialog-button').entwine({
			onclick: function(e) {
                if(this.is(':disabled')) {
                    e.preventDefault();
                    return false;
                }

                var config = this.data("dialog");

                var self = this;
                var submitHandler = function(data){
                    var dataStr = (typeof data === 'object') ? JSON.stringify(data) : data;
                    self.parents('form').append(
                        '<input id="TMP_PaymentField" type="hidden" name="PaymentAdditionalData" value=\'' + dataStr + '\'/>'
                    ).trigger('submit', [self]);
                    $("#TMP_PaymentField").remove();
                };

                var dialog = $("#PaymentDialog").length === 0 ? $('<div id="PaymentDialog"></div>') : $("#PaymentDialog");

                dialog.empty().append('<strong>' + ss.i18n.inject(
                    ss.i18n._t('PaymentDialog.' + config.infoTextKey),
                    { Amount: config.maxAmount }
                ) + '</strong>');

                if(config.hasAmountField){
                    dialog.append('<div class="amount-input"><label for="PaymentDialog_AmountField">'
                        + ss.i18n._t('PaymentDialog.Amount')
                        +':</label><span><input type="text" id="PaymentDialog_AmountField" name="amount" value=""/></span>'
                        + '</div>');
                }

                dialog.dialog({
                    dialogClass: "payment-dialog",
                    modal: true,
                    buttons: [
                        {
                            text: ss.i18n._t('PaymentDialog.Cancel'),
                            "class" : "button-cancel",
                            "data-icon": "cross-circle",
                            click: function() {
                                $( this ).dialog( "destroy" );
                            }
                        },
                        {
                            text: ss.i18n._t('PaymentDialog.' + config.buttonTextKey),
                            "data-icon": "accept",
                            click: function() {

                                var value = $("#PaymentDialog_AmountField").length > 0
                                    ? ($("#PaymentDialog_AmountField").val() || "-1")
                                    : '';
                                submitHandler(value);
                                $( this ).dialog( "destroy" );
                            }
                        }
                    ]
                });

                e.preventDefault();
                return false;
			}
		});

        /*
        $('.cms a.payment-capture-link').entwine({
            onclick: function (e) {
                e.preventDefault();
                var dialog = $('<div class="payment-dialog"/>');
                dialog.ssdialog({iframeUrl: this.attr('href'), height: 200});

                dialog.find('iframe').on('load', function(e) {
                    var contents = $(this).contents();

                    contents.find('.button-cancel').one("click", function(e) {
                        e.preventDefault();
                        dialog.ssdialog('close');
                        return false;
                    });
                });

                dialog.ssdialog('open');
            }
        });
        */
    });

}(jQuery));
