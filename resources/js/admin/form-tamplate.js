import input from '../input';

window.form = () => {
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
    // *===========================================*
    // *========== Campos del formulario ==========*
    // *===========================================*
    //TODO
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
        id: 'biometryDate',
        name: 'date',
        label: 'Selecciona una fecha',
        required: true,
        max: dayjs(),
        min: dayjs(),
      });

      //HORA
      this.time = input({
        id: 'biometryTime',
        name: 'time',
        label: 'Hora',
        required: true,
        value: dayjs().format('HH:mm')
      });
    },
    // *=====================================================*
    // *================ METODOS DE UTILIDAD ================*
    // *=====================================================*
    reset() {
      this.visible = false;
      this.mode = "register";
      this.waiting = false;
    },
    /**
     * Este metodo establece los parametros basicos con lo que
     * se inicializa el formulario cuando es llamado por un evento global.
     * @param {*} detail Detalles del modo en el que se habilita el formulario
     */
    enableForm(detail) {
      this.visible = true;
      this.mode = detail.mode;

      if (detail.mode === 'updating') {
        //TODO
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
    // *=======================================*
    // *================ FETCH ================*
    // *=======================================*
    submit() {
      if (this.validateSubmit()) {
        if (this.mode === 'register') {
          this.__store();
        } else if (this.mode === 'updating') {
          this.__update();
        }
      }
    },
    __store() {
      this.waiting = true;
      let data = this.getSubmitData();
      //TODO
    },
    __update() {
      this.waiting = true;
      let data = this.getSubmitData();
      //TODO
    },
    getSubmitData() {
      //TODO
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
    /**
     * Realiza todas las validaciones del formulario
     * y returna true si todas fueron correctas.
     * @returns bool
     */
    validateSubmit() {
      let validations = [];
      validations.push(this.validateDate());
      validations.push(this.validateTime());

      //Retorna false si alguna de las validaciones es falsa, pero valida todos los campos
      return !validations.some(val => val === false);
    }
  }
}