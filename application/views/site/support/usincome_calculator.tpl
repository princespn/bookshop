{% extends "global/base.tpl" %}
{% block title %}{{ parent() }} {{ 'Income Calculator' }}{% endblock %}
{% block meta_description %}{{ parent() }} {{ 'Income Calculator' }} {% endblock meta_description %}
{% block meta_keywords %}{{ parent() }} {{ 'Income Calculator' }} {% endblock meta_keywords %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">Layer to Level</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div class="product-list">
        {{ breadcrumb }}
        <div class="row">
            <div class="col-md-12">
            <div id="jroxMessage" class="jroxGeneralMessageBox" align="center">
    
    <div id="total">
        <span class="pull-left">Your Monthly Income:(USD)</span>
        <span class="pull-right" id="total-income"></span>
        <span class="clearfix"></span>
      </div>
      <div id="layers">
        <table>
          <thead>
            <th>Level</th>
            <th>Basic Referrals</th>
            <th>Level Total</th>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>2</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>3</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>4</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>5</td>
              <td></td>
              <td></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="clearfix">
        <span id="error-message">
          Please check your inputs and try again!
        </span>
        <form id="referrals">
          <label><input type="number" id="direct" placeholder="#" min="1" max="10000" /> &nbsp;Enter # of People You can refer.&nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;</label>
          <label><input type="number" id="indirect" placeholder="#" min="1" max="10" />     &nbsp;Enter # of People each member can refer on their own.&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
          <input type="submit" style="display: none;" />
        </form>
        <button id="calculate">=</button>
      </div>
    <div class="clearfix"></div>

    <div style="float: inherit;text-align: left;font-size: 14px;color: orange;margin-top:50px;">
    <p><strong>Important legal notice:</strong> This income calculator is intended for illustrative and educational purposes only, and in no way guarantees any specific network growth pattern or income earnings.</p>
      </div>
      </div>
    

      
          </div> 
       
                
            </div>
        </div>
    </div>
    <script>
    'use strict';
    (function() {
      // Pretty print money
      var moneyFormat = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      });

      var numberFormat = new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      });

      var errorHandler = {
        show: function() {
          var inputs = this._getInputs(),
              errorText = this._getErrorText();

          errorText.style.visibility = 'visible';
          for (var i = 0; i < inputs.length; i++) {
            inputs[i].style.outline = '3px solid red';
          }
        },

        hide: function() {
          var inputs = this._getInputs(),
              errorText = this._getErrorText();

          errorText.style.visibility = 'hidden';
          for (var i = 0; i < inputs.length; i++) {
            inputs[i].style.outline = 'none';
          }
        },

        _getInputs: function() {
          return document.getElementsByTagName('input');
        },

        _getErrorText: function() {
          return document.getElementById('error-message');
        }
      };

      // function state, prevents double submit, etc.
      var state = false;

      var calc = function(e) {
        e.preventDefault();

        if (state) {
          return;
        }

        errorHandler.hide();

        state = true;

        var directAmount = 1;
        var indirectAmount = 1;
        var maxLevels = 5;

        var direct = parseInt(document.getElementById('direct').value) || 0;
        var indirect = parseInt(document.getElementById('indirect').value) || 0;
        var totalIncome = document.getElementById('total-income');

        if (!direct || !indirect) {
          errorHandler.show();
          state = false;
          return;
        }

        var table = document.getElementsByTagName('tbody');
            table = table[0];

        // Clear table
        totalIncome.innerText = '';
        for (var i = 0; i < maxLevels; i++) {
          table.rows[i].cells[1].innerText = '';
          table.rows[i].cells[2].innerText = '';
        }

        var i = 0;
        var delay = 150;
        var total = 0;

        var main = function loop (maxLevels) {
          var count =  direct * Math.pow(indirect, i);
          var amount =  (direct * Math.pow(indirect, i) * indirectAmount);
          total += amount;

          setTimeout(function () {
            table.rows[i].cells[1].innerText = numberFormat.format(count);
          }, delay);

          setTimeout(function () {
            table.rows[i].cells[2].innerText = moneyFormat.format(amount);
          }, delay * 2);

          setTimeout(function() {
            if (++i < maxLevels) {
              loop(maxLevels);
            } else {
              setTimeout(function () {
                totalIncome.innerText = moneyFormat.format(total);
              }, delay * 3);
              state = false;
            }
          }, delay * 3);
        }(maxLevels);

      };

      document.getElementById('calculate').addEventListener('click', calc);
      document.getElementById('referrals').onsubmit = calc;

    })();
  </script>

{% endblock content %}