import { isEmpty } from 'lodash';
import input from '../input';
const dayjs = require('dayjs');
var isSameOrBefore = require('dayjs/plugin/isSameOrBefore');
dayjs.extend(isSameOrBefore);

var isSameOrAfter = require('dayjs/plugin/isSameOrAfter');
dayjs.extend(isSameOrAfter);


window.fishBatchExpenseForm = () => {
  return {
    /** Define si el formulario se muestra o no */
    visible: false,
    /** 
     * Establece el tipo de formulario, 
     * si register para nuevo costo o 
     * updating para actualizar uno existente 
     * */
    mode: 'register',
    /**
     * Instnacia del lote de peces en el que se desea 
     * crear o actualizar un costo.
     */
    fishBatch: null,
    /** Instancia original del gasto que se desea modificar */
    originalExpense: null,
    // *===========================================*
    // *========== Campos del formulario ==========*
    // *===========================================*
    /** Establece el momento en el que se registra el gasto */
    inThisMoment: true,
    /** Fecha del gasto en formato YYYY-MM-DD */
    date: null,
    /** Habilita el ingreso manual de la hora */
    setTime: false,
    /** Hora en la que se registra el gasto */
    time: null,
    /** Información adicional del gasto a registrar */
    description: null,
    /** Importe del gasto a registrar en pesos colombianos */
    amount: null,
    // *====================================================*
    // *========== Relacionadas con el componente ==========*
    // *====================================================*
    /** Define si el componente está esperando una respuesta del servidor */
    waiting: false,
    /** Se encarga de las peticones al servidor */
    wire: null,
    /** Se encarga de administrar los eventos personalizados */
    dispatch: null,
    /** Permite acceder a las referencias del componente en el DOM */
    refs: null,
    // *=====================================================*
    // *============= METODOS DE INICIALIZACIÓN =============*
    // *=====================================================*
    init(wire = null, dispatch = null, refs = null) {
      this.wire = wire;
      this.dispatch = dispatch;
      this.refs = refs;
      this.__createInputs();
    },
    __createInputs() {
      //FECHA
      this.date = input({
        id: 'expenseDate',
        name: 'date',
        label: 'Selecciona una fecha',
        required: true,
        max: dayjs(),
        min: dayjs(),
      });

      //HORA
      this.time = input({
        id: 'expenseTime',
        name: 'time',
        label: 'Hora',
        required: true,
        value: dayjs().format('HH:mm')
      });

      //DESCRIPCIÓN
      this.description = input({
        id: 'expenseDescription',
        name: 'description',
        label: 'Descripción',
        placeholder: 'Escribe los detalles aquí.',
        min: 3,
        max: 255,
      });

      this.amount = input({
        id: 'expenseAmount',
        name: 'amount',
        label: 'Importe',
        placeholder: '$0 [COP]',
        require: true,
        min: 100,
        max: 1000000000
      });
    },
    // *=====================================================*
    // *================ METODOS DE UTILIDAD ================*
    // *=====================================================*
    reset() {
      this.visible = false;
      this.mode = "register";
      this.fishBatch = null;
      this.waiting = false;
      //Se rresetean los formulario
      this.inThisMoment = true;
      this.setTime = false;
      this.description.reset();
      this.amount.reset();
      this.refs.amount.value = '';
    },
    /**
     * Este metodo establece los parametros basicos con lo que
     * se inicializa el formulario cuando es llamado por un evento global.
     * @param {*} detail Detalles del modo en el que se habilita el formulario
     */
    enableForm(detail) {
      this.visible = true;
      this.mode = detail.mode;
      this.fishBatch = detail.fishBatch;
      this.date.min = dayjs(this.fishBatch.seedtime);

      if (detail.mode === 'updating') {
        this.originalExpense = detail.data;
        this.inThisMoment = false;
        this.date.value = detail.data.date.format('YYYY-MM-DD');
        this.setTime = true;
        this.time.value = detail.data.date.format('HH:mm');
        this.description.value = detail.data.description;
        this.amount.value = detail.data.amount;
        this.refs.amount.value = window.formatCurrency(this.amount.value, 0);
      }
    },
    cancel() {
      this.reset();
      this.dispatch('cancel-form-operation');
    },
    notifyErrors(errors) {
      //Esto se encarga de actualizar los campos del formulario
      for (const key in errors) {
        if (Object.hasOwnProperty.call(errors, key)) {
          const error = errors[key];
          if (Object.hasOwnProperty.call(this, key)) {
            this[key].setError(error);
          }
        }
      }
    },
    formatAmount() {
      //Recupero el valor
      let value = window.deleteCurrencyFormat(this.refs.amount.value);
      this.amount.value = value;
      //Se vuelve a formatear
      this.refs.amount.value = window.formatCurrency(value, 0);
    },
    __printSubmitData(data) {
      let bodyLength = 60;
      let separator = '-';
      let header = '';
      let left = `+${separator}`;
      let right = `${separator}+`;
      let text = '';

      header = left + separator.repeat(bodyLength) + right + '\n';
      text = header;

      for (const key in data) {
        if (Object.hasOwnProperty.call(data, key)) {
          let value = data[key] ? data[key] : 'null';
          let keyLength = key.length;
          let valueLength = value.length;
          let line = `${key}: ${value}`;
          if (line.length <= bodyLength) {
            line += ' '.repeat(bodyLength - line.length);
            text += `| ${line} |\n`;
          } else {
            let first = line.slice(0, bodyLength - 1);
            let last = line.slice(bodyLength, 259);

            text += `| ${first} |\n`;
            text += '| ' + ' '.repeat(keyLength + 2);
            text += '| ' + " ".repeat(bodyLength - last.length) + ' |' + '\n'
          }
        }//end if
      }//end for
      text += header;
      console.log(text, data);
    },
    // *======================================*
    // *================ CRUD ================*
    // *======================================*
    submit() {
      if (this.validateSubmit()) {
        if (this.mode === 'register') {
          this.__store();
        }else if(this.mode === 'updating'){
          this.__update();
        }
      }
    },
    __store() {
      this.waiting = true;
      let data = this.getSubmitData();
      this.wire.storeExpense(data)
        .then(res => {
          if (res.ok) {
            let info = {
              fishBatch: this.fishBatch,
              expense: res.expense,
            }
            // Se emite el evento con los datos del nuevo lote de peces
            this.dispatch('expense-was-stored', info);
            //Se resetea el formulario
            this.reset();
          } else {
            console.log(res.errors);
            this.notifyErrors(res.errors);
          }
        }).catch(error => {
          console.log(error);
        }).finally(() => {
          this.waiting = false;
        })
    },
    __update() {
      this.waiting = true;
      let data = this.getSubmitData();
      this.wire.updateExpense(data)
        .then(res => {
          if (res.ok) {
            let info = {
              fishBatch: this.fishBatch,
              expense: res.expense,
            }
            // Se emite el evento con los datos del nuevo lote de peces
            this.dispatch('expense-was-updated', info);
            //Se resetea el formulario
            this.reset();
          } else {
            console.log(res.errors);
            this.notifyErrors(res.errors);
          }
        }).catch(error => {
          console.log(error);
        }).finally(() => {
          this.waiting = false;
        })
    },
    getSubmitData() {
      let date = this.date.value;
      let time = this.time.value;
      let data = {
        fishBatchId: this.fishBatch.id,
        description: this.description.value,
        amount: this.amount.value,
        inThisMoment: this.inThisMoment,
        setTime: this.setTime
      };

      if (!this.inThisMoment) {
        data.date = date;
        if (this.setTime) {
          data.time = time;
          data.fullDate = `${date} ${time}`;
        }
      }

      if (this.mode === 'updating') {
        data.expenseId = this.originalExpense.id;
      }

      return data;
    },
    // *===============================================*
    // *================ VALIDACIONES =================*
    // *===============================================*
    validateDate() {
      let value = this.date.value;
      let min = dayjs(this.date.min).startOf('day');
      let max = dayjs();
      let errorMessage = null;

      if (!this.inThisMoment) {
        if (!isEmpty(value)) {
          let date = dayjs(value, 'YYYY-MM-DD').startOf('day');
          if (date.isSameOrAfter(min)) {
            if (date.isSameOrBefore(max)) {
              this.date.isOk();
              //Se valida la hora
              if (this.setTime) {
                this.validateTime();
              }

              return true;
            } else {
              errorMessage = "No se pueden agregar gastos en el futuro.";
            }
          } else {
            errorMessage = "La fecha debe ser mayor a la fecha del lote";
          }
        } else {
          errorMessage = "Se debe elegir una fecha valida";
        }
      } else {
        return true;
      }

      this.date.setError(errorMessage);
      return false;
    },
    validateTime() {
      let time = this.time.value;
      let date = this.date.value;
      let min = dayjs(this.fishBatch.seedtime);
      let max = dayjs();
      let errorMessage = null;

      if (!this.inThisMoment && this.setTime && !this.date.hasError) {
        if (!isEmpty(time)) {
          let fullDate = dayjs(`${date} ${time}`, 'YYYY-MM-DD HH:mm');
          if (fullDate.isSameOrAfter(min)) {
            if (fullDate.isSameOrBefore(max)) {
              this.time.isOk();
              return true;
            } else {
              errorMessage = "Los gastos no se pueden realizar en el futuro";
            }
          } else {
            errorMessage = "Se intenta registrar un gasto anterior al lote.";
          }
        } else {
          errorMessage = "Debe escribir o seleccionar una hora válida";
        }
      } else {
        return true;
      }

      this.time.setError(errorMessage);
      return false;
    },
    validateDescription() {
      let value = this.description.value;
      let min = this.description.min;
      let max = this.description.max;
      let errorMessage = null;

      if (value && !isEmpty(value.trim())) {
        if (value.length >= min) {
          if (value.length <= max) {
            this.description.isOk();
            return true;
          } else {
            errorMessage = `La descripción no debe superar los ${max} caracteres.`
          }
        } else {
          errorMessage = `Debe tener ${min} o mas caracteres`;
        }
      } else {
        errorMessage = "Este campo es obligatorio.";
      }

      this.description.setError(errorMessage);
      return false;
    },
    validateAmount() {
      let value = this.amount.value;
      let min = this.amount.min;
      let max = this.amount.max;
      let errorMessage = null;

      if (value) {
        if (value >= min) {
          if (value < max) {
            this.amount.isOk();
            return true;
          } else {
            errorMessage = `El importe debe ser menor que ` + window.formatCurrency(max, 0);
          }
        } else {
          errorMessage = "El importe minimo debe ser de " + window.formatCurrency(min, 0);
        }
      } else {
        errorMessage = "Este campo es obligatorio."
      }

      this.amount.setError(errorMessage);
      return false;
    },
    validateSubmit() {
      let validations = [];
      validations.push(this.validateDate());
      validations.push(this.validateTime());
      validations.push(this.validateDescription());
      validations.push(this.validateAmount());

      //Retorna false si alguna de las validaciones es falsa, pero valida todos los campos
      return !validations.some(val => val === false);
    }
  }//.end return
}//.end method