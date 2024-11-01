var AdtoniqGAPlugin = (function () {
  'use strict';

  var module = {
    // elements (set in this.init)
    $accountNumber: null,
    $trafficSplit: null,
    $button: null,

    state: {
      accountNumber: '',
      trafficSplit: ''
    },

    isNotEmpty: function (str) {
      if (typeof str !== 'string') return false;
      return str.length > 0;
    },

    isValidAcctNumber: function (str) {
      if (typeof str !== 'string') return false;
      return (/^ua-\d{4,9}-\d{1,4}$/i).test(str);
    },

    isValidForm: function (formValues) {
      return (
        this.isNotEmpty(formValues.accountNumber) &&
        this.isValidAcctNumber(formValues.accountNumber)
      );
    },

    setState: function () {
      this.state = {
        accountNumber: this.$accountNumber.value || '',
        trafficSplit: this.$trafficSplit.value || ''
      };
    },

    setButtonState: function () {
      var isValid = this.isValidForm(this.state);
      isValid ?
        this.$button.classList.remove('btn-disabled') : this.$button.classList.add('btn-disabled'); // jshint ignore:line
    },

    setValidationState: function (el, failureMessage, isWarning) {
      var helpBlockId = el.getAttribute('aria-describedby');
      var helpBlock = document.getElementById(helpBlockId);
      var inputContainer = el.parentElement.parentElement;
      var icon = el.nextElementSibling;

      if (failureMessage) {
        inputContainer.className = isWarning ?
          'form-group has-warning has-feedback' : 'form-group has-error has-feedback';
        icon.className = isWarning ?
          'form-control-feedback icon-warning' : 'form-control-feedback icon-error';
        helpBlock.innerHTML = failureMessage;
        helpBlock.style.display = 'block';
      } else {
        inputContainer.className = 'form-group has-success has-feedback';
        icon.className = 'form-control-feedback icon-ok';
        helpBlock.style.display = 'none';
      }
    },

    validateAcctNumber: function () {
      this.render();
      this.setValidationState(
        this.$accountNumber,
        this.isValidAcctNumber(this.state.accountNumber) ? '' : 'Invalid account number'
      );
    },

    requestAccount: function (server, apiKey) {
    	var email = prompt('Please enter the Google/gmail email address you wish to use to access Google Analytics, and we\'ll send an invitation your way.');
    	if (email) {
    		this.ajax(server + 'api/v1', function (response) {
          alert(response);
        }, 'operation=requestGAProperty&email=' + email + '&apiKey=' + apiKey);
    	}
    },

    ajax: function (url, onDone, params) {
      this.ajax2(url, onDone, true, null, params);
    },

    ajax2: function (url, onDone, synch, onFail, params) {
      var xmlhttp = new XMLHttpRequest();
      if (onDone) {
        xmlhttp.onreadystatechange = function() {
          if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200)
              onDone(xmlhttp.responseText.trim());
            else if (onFail)
              onFail();
          }
        };
      }
      xmlhttp.open('POST', url, synch);
      xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      xmlhttp.send(params);
    },

    onSave: function (success) {
      var $alert = success ? document.getElementById('save-success') : document.getElementById('save-error');
      $alert.classList.remove('hide');
      setTimeout(function () {
        this.showAlert($alert);
      }, 100);
    },

    showAlert: function ($alert) {
      $alert.classList.remove('fade');
      $alert.classList.add('fade-in');
      $alert.addEventListener('transitionend', this.hideAlert($alert), false); // IE
      $alert.addEventListener('webkitTransitionEnd', this.hideAlert($alert), false); // EVERYTHING ELSE
      setTimeout (function () {
        $alert.classList.remove('fade-in');
        $alert.classList.add('fade-out');
      }, 3000);
    },

    hideAlert: function ($alert) {
      $alert.removeEventListener('transitionend', this.hideAlert($alert), false); // IE
      $alert.removeEventListener('webkitTransitionEnd', this.hideAlert($alert), false); // EVERYTHING ELSE
      $alert.classList.add('hide');
      $alert.classList.add('fade');
      $alert.classList.remove('fade-out');
    },

    render: function () {
      this.setState();
      this.setButtonState();
    },

    init: function () {
      // assign elements
      this.$accountNumber = document.getElementById('gaProperty');
      this.$trafficSplit = document.getElementById('traffic');
      this.$button = document.getElementById('submit');
      // kick off the module
      this.render();
    }
  };

  return module;

}());

if (typeof module !== 'undefined') {
  module.exports = AdtoniqGAPlugin;
}
