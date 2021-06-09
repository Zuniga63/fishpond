require('../bootstrap');
require('alpinejs');
import Swal from 'sweetalert2';
window.toastr = require('toastr');
window.Swal = Swal;

//--------------------------------------
// UTILIDADES
//--------------------------------------
/**
 * Utilidades genericas para todo el sistema
 */
window.functions = function () {
  return {
    generalValidation: (id, rules, messages) => {
      const form = $(`#${id}`);
      form.validate({
        rules,
        messages,
        errorElement: 'span',
        errorPlacement: function (error, element) {
          error.insertAfter(element);
          error.addClass('invalid-feedback');
          // element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
          $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
          $(element).removeClass('is-invalid');
        },
        submitHandler: function (form) {
          return true;
        }
      })
    },
    notifications: function (message, title, type) {
      toastr.options = {
        closeButton: true,
        newestOnTop: true,
        positionClass: 'toast-top-right',
        // preventDuplicates: true,
        timeOut: '3000'
      };
      switch (type) {
        case 'error':
          toastr.error(message, title);
          break;
        case 'success':
          toastr.success(message, title);
          break;
        case 'info':
          toastr.info(message, title);
          break;
        case 'warning':
          toastr.warning(message, title);
          break;
      }
    },//End of notification
  }
}();

window.formatCurrency = (number, fractionDigits = 0, style = 'currency', currency = "COP") => {
  var formatted = new Intl.NumberFormat('es-CO', {
    style,
    currency,
    minimumFractionDigits: fractionDigits,
  }).format(number);
  return formatted;
}

window.deleteCurrencyFormat = text => {
  let value = text.replace("$", "");
  value = value.split(".");
  value = value.join("");

  value = parseFloat(value);

  return isNaN(value) ? 0 : value;
}

window.formatInput = (target) => {
  let value = target.value;
  value = deleteCurrencyFormat(value);

  target.value = formatCurrency(value, 0);
}

window.addEventListener('load', () => {
  /**
  *  Esto quita el preload del sistema
  */
  document.getElementById('preload').classList.remove('show');  

  /**
   * Agrego los tooltips a todos los elementos con title
   */
  $(document.body).tooltip({ selector: "[title]" });

  /**
   * El siguiente codigo lo que hace es 
   * abrir los menús principales de os links
   * activos
   */
  const sidebar = document.getElementById('mainSidebar');
  const linkActive = sidebar.querySelector('a.active');
  if(linkActive){
    let father = linkActive.parentElement;
    while(!father.getAttribute('id')){
      //El siguiente punto de corte es cuando encuentra
      //un elemento con la clase has-treeview
      if(father.classList.contains('has-treeview')){
        father.classList.add('menu-open');
        father.querySelector('a').classList.add('active');
      }
      father = father.parentElement;
    }//.end while
  }//.end if
});