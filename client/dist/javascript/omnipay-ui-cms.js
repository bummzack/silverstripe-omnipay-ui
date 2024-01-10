/**
 */
(function ($) {
  $.entwine('ss', function ($) {
    $('.grid-field .ss-gridfield-item button.action.payment-dialog-button').entwine({
      onmatch: function () {
      },
      onclick: function (e) {
        if (this.is(':disabled')) {
          e.preventDefault();
          return false;
        }
        var config = this.data("dialog");
        var self = this;

        var dialog = $("#PaymentDialog").length === 0 ? $('<div id="PaymentDialog"></div>') : $("#PaymentDialog");
        dialog.empty().append('<strong>' + ss.i18n.inject(
          ss.i18n._t('PaymentDialog.' + config.infoTextKey),
          {Amount: config.maxAmount}
        ) + '</strong>');

        if (config.hasAmountField) {
          dialog.append('<div class="amount-input"><label for="PaymentDialog_AmountField">'
            + ss.i18n._t('PaymentDialog.Amount')
            + ':</label><span><input type="text" id="PaymentDialog_AmountField" name="amount" value=""/></span>'
            + '</div>');

          dialog.on("change, keyup", "input[type=text]", function () {
            var numVal = $(this).val();
            $("#PaymentDialog_ConfirmButton").button(
              numVal.match(/^\d+[\.\,]?\d*$/) && parseFloat(numVal) <= config.maxAmountNum ? "enable" : "disable"
            );
          });
        }

        dialog.dialog({
          dialogClass: "payment-dialog",
          modal: true,
          buttons: [
            {
              text: ss.i18n._t('PaymentDialog.Cancel'),
              "class": "btn button-cancel",
              "data-icon": "cross-circle",
              click: function () {
                $(this).dialog("destroy");
              }
            },
            {
              "id": "PaymentDialog_ConfirmButton",
              text: ss.i18n._t('PaymentDialog.' + config.buttonTextKey),
              "class" : "btn button-accept",
              "data-icon": "accept",
              disabled: config.hasAmountField,
              click: function () {
                var value = $("#PaymentDialog_AmountField").length > 0
                  ? ($("#PaymentDialog_AmountField").val() || "-1")
                  : '';

                //replace , with . in value if there is only one comma and no dot
                if (value.match(/,/g) && !value.match(/\./g) && value.match(/,/g).length == 1) {
                  value = value.replace(/,/g, '.');
                }

                self.getGridField().reload({
                  data: [
                    {name: self.attr('name'), value: self.val()},
                    {name: "amount", value: value}
                  ]
                });

                $(this).dialog("destroy");
              }
            }
          ]
        });

        e.preventDefault();
        return false;
      }
    });

    $('.ss-gridfield').entwine({
      /**
       * Check if there are pending payments and auto-refresh the gridfield when needed
       */
      onadd: function () {
        this._super();
        this.refreshPendingPayments(2000);
      },

      onremove: function () {
        this._clearTimeout();
        this._super();
      },

      reload: function (ajaxOpts, successCallback) {
        // clear any running timeout before reloading
        this._clearTimeout();
        this._super(ajaxOpts, successCallback);
      },

      onreload: function () {
        this._super();
        this.refreshPendingPayments(2000);
      },

      refreshPendingPayments: function (delay) {
        this._clearTimeout();
        // collect all pending payment IDs
        var pending = [];
        var url;
        this.find('.payment-pending-indicator').each(function () {
          url = $(this).data("statuslink") || url;
          pending.push($(this).data("paymentid"));
        });

        if (url && pending.length > 0) {
          var self = this;
          var to = window.setTimeout(function () {
            $.get(url, {"ids": pending.join(",")}, function (data) {
              if (data == 1) {
                // at least one payment has been resolved, reload the gridfield
                self.reload();
              } else {
                // increase the delay with every unsuccessful request
                self.refreshPendingPayments(delay * 2);
              }
            })
          }, delay);

          self.data("payment-timeout", to);
        }
      },

      _clearTimeout: function () {
        if (this.data("payment-timeout")) {
          window.clearTimeout(this.data("payment-timeout"));
          this.data("payment-timeout", null);
        }
      }
    });
  });

}(jQuery));
