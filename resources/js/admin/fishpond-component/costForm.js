import input from './Input';

const costForm = () => {
  return {
    title: 'Registrar Costo',
    /** Tipo de formulario [register|update] */
    mode: 'register',
    /** Instancia origial de costos */
    originalData: undefined,
    /** Instancia del estanque que es modificada */
    fishpond: undefined,
    /** Tipo de costo [materials, workforce, maintenance] */
    costType: null,
    /** Determina el momento del costo */
    inThisMoment: true,
    /** Fecha del costo */
    date: null,
    /** Define si se va a intrducir la hora */
    setTime: false,
    /** Hora en la que se realiza el costo */
    time: null,
    /** Descripiíon del costo que se desea registrar */
    description: null,
    /** Importe o valor de costo del estanque */
    amount: null,
    /** Habilita y deshabilita los campos del formulario */
    disabled: false,
    /** Encargado de observar cuando el formulario esté esperando la respuesta del servidor */
    waiting: false,
    /** Para acceder a las funciones de livewire */
    wire: undefined,
    /** Para acceder al sistema de eventos de alpine */
    dispatch: undefined,
    /** Para acceder a las referencias del componente */
    refs: undefined,
    // *============================================================================================*
    // *================================= METODOS DE INICIALIZACIÓN ================================*
    // *============================================================================================*
    /**
     * Se encarga de inicizalizar los campos del componente
     */
    init(wire, dispatch, refs) {
      this.wire = wire;
      this.dispatch = dispatch;
      this.refs = refs;
      this.__buildInputs();
    },
    /**
     * Construye los objetos que se encargan de controlar los
     * campos que el usuario debe rellenar
     */
    __buildInputs() {
      this.costType = input({
        id: 'costType',
        name: 'type',
        label: 'Tipo de Costo',
        required: true,
        value: ''
      });

      this.date = input({
        id: 'costDate',
        name: 'date',
        label: 'Selecciona una fecha',
        required: true,
        max: dayjs().format('YYYY-MM-DD'),
      })

      this.time = input({
        id: 'costTime',
        name: 'time',
        label: 'Hora',
        required: true,
      })

      this.description = input({
        id: 'costDescription',
        name: 'description',
        label: 'Descripicíon',
        required: true,
        placeholder: 'Escribe una descripción del costo.'
      })

      this.amount = input({
        id: 'costAmount',
        name: 'amount',
        label: 'Importe',
        required: true,
        placeholder: '$ 0.00',
        max: 100000000,
      })
    },
    // *============================================================================================*
    // *=========================================== CRUD ===========================================*
    // *============================================================================================*
    /**
     * Dependiendo del modo del componente el envió del
     * formulario ejecuta store o update
     */
    submit() {
      if (this.validateData()) {
        if (this.mode === 'register') {
          this.store();
        } else if (this.mode === 'update') {
          this.update();
        }
      }
    },
    /**
     * Envia unq petición al servidor con los datos
     * del costo que se desea guardar
     */
    store() {
      let data = this.__buildData();
      this.waiting = true;

      this.wire.storeFishpondCost(data)
        .then(res => {
          if (res.isOk) {
            this.hidden();
            this.dispatch('new-fishpond-cost-registered', res.cost);
          } else {
            this.notifyErrors(res.errors);
          }

          this.waiting = false;
        }).catch(error => {
          console.log(error);
        });
    },
    update() {
      let data = this.__buildData();
      //Se agrega el id del costo
      data.costId = this.originalData.id;
      this.waiting = true;
      this.wire.updateFishpondCost(data)
        .then(res => {
          if(res.isOk){
            this.hidden();
            this.dispatch('fishpond-cost-updated', res.cost);
          }else{
            this.notifyErrors(res.errors);
          }
          this.waiting = false;
        }).catch(error => {
          console.log(error);
        })
    },
    // *============================================================================================*
    // *======================================== UTILIDADES ========================================*
    // *============================================================================================*
    /**
     * Se encarga demontar en el formulario los datos del costo que
     * se desea actualizar.
     * @param {*} data Instancia de un costo
     */
    mountCost(data) {
      this.reset();
      this.title = 'Actualizar Costo'

      //Se cargan los datos
      this.costType.value = data.type;
      this.inThisMoment = false;
      this.date.value = data.date;
      this.setTime = true;
      this.time.value = data.time;
      this.description.value = data.description;
      this.amount.value = data.amount;
      this.originalData = data;

      //Se actualiza el valor del importe en el DOM
      this.refs.costAmount.value = window.formatCurrency(data.amount, 0);
      //Se cambia el estado del formulario
      this.mode = 'update';

    },
    /**
     * Restaura a sus valores por defecto cada uno
     * de los campos del formulario y de resetear los 
     * campos que no son controlados por el model.
     */
    reset() {
      this.costType.reset();
      this.description.reset();
      this.amount.reset();
      this.refs.costAmount.value = '';

      //Se resetean las fechas si tienen errores
      this.inThisMoment = true;
      if (this.date.hasError) {
        this.date.reset();
      }

      if (this.time.hasError) {
        this.setTime = false;
        this.time.reset;
      }

      this.title = "Registrar Costo";
    },
    /**
     * Se encarga de resetear el formulario 
     * y de enviar un evento para que el componente principal se encargue
     * de ocultar la ventana modal.
     */
    hidden() {
      this.reset();
      this.dispatch('hidden-modal');
    },
    /**
     * Este metodo interno es el encargado de crea los datos que serán enviados al
     * servidor utilizando la estructura de datos del mismo.
     */
    __buildData() {
      let fishpondId = this.fishpond.id;
      let type = this.costType.value;
      let description = this.description.value;
      let amount = this.amount.value;
      let inThisMoment = this.inThisMoment;
      let setTime = this.setTime;
      let date = inThisMoment ? null : this.date.value;
      let time = !inThisMoment && setTime ? this.time.value : null;

      return { fishpondId, type, description, amount, inThisMoment, setTime, date, time };
    },
    /**
     * Meto requerido para validar y guardar de forma correcta el valor del 
     * importe de la transacción ya que este en la vista requiere ser formateado.
     * utiliza el objeto refs para acceder el elemento del DOM
     */
    formatAmount(target) {
      let value = window.deleteCurrencyFormat(target.value);
      this.refs.costAmount.value = window.formatCurrency(value, 0);
      this.amount.value = value;
      this.validateAmount();
    },
    // *============================================================================================*
    // *======================================= VALIDACIONES =======================================*
    // *============================================================================================*
    /**
     * Se encarga de hacer todas las validaciones del formulario y retorna
     * true si todas son correctas.
     * @returns {boolean}
     */
    validateData() {
      let validations = [];
      validations.push(this.validateCostType(),
        this.validateDate(),
        this.validateTime(),
        this.validateDescription(),
        this.validateAmount());

      return !validations.some(val => val === false);
    },
    /**
     * Se encarga de validar que el tipo de costo seleccionado tenga 
     * un valor adecuado
     * @returns {boolean} Si la validacion fue correcta
     */
    validateCostType() {
      let value = this.costType.value;
      let isOk = false;

      if (value === 'materials' || value === 'workforce' || value === 'maintenance') {
        isOk = true;
        this.costType.isOk();
      } else if (value === '') {
        this.costType.setError('Se debe seleccionar uno');
      } else {
        this.costType.setError('El tipo de costo seleccionado es incorrecto');
      }

      return isOk;
    },
    /**
     * Se encarga de verificar que la fecha seleccionada por el usuario sea valida
     * y que no supere el momento presente
     * @returns {boolean}
     */
    validateDate() {
      let value = this.date.value;
      let isOk = false;

      //Solo se verifica la fecha si se habilitó para ingresarla
      if (!this.inThisMoment) {
        if (value && value.length > 0) {
          //Se crea una instancia de tiempo para comparar
          let dateSelected = window.dayjs(value);
          //Se verifica que la fecha sea correcta
          if (dateSelected.isValid()) {
            //Se compara con el momento actual
            let now = window.dayjs();
            if (dateSelected.isSameOrBefore(now)) {
              this.date.isOk();
              //Se procede a validar la hora
              this.validateTime();
              isOk = true;
            } else {
              this.date.setError('La fecha superior a hoy');
            }
          } else {
            this.date.setError('La fecha es inválida');
          }

        } else {
          this.date.setError('Este campo es requerido');
        }
      } else {
        isOk = true;
      }

      return isOk;
    },
    /**
     * Se encarga de verificar que la hora ingresada por el usuario 
     * sea anterior al momento actual del registro.
     * @returns {boolean}
     */
    validateTime() {
      let value = this.time.value;
      let isOk = false;

      //Solo se verifica si se habilitó para ingresarla
      if (this.setTime) {
        if (value && value.length > 0) {
          /**
         * Solo se valida si la fecha ha sido seleccionada y tiene valor
         * ya que es necesaria para verificar el momento del registro
         */
          if (this.date.value && this.date.value.length > 0 && !this.date.hasError) {
            let dateValue = this.date.value;
            let fullDate = window.dayjs(`${dateValue} ${value}`);
            let now = window.dayjs();

            if (fullDate.isValid()) {
              console.log(fullDate.isSameOrBefore(now))
              if (fullDate.isSameOrBefore(now)) {
                this.time.isOk();
                isOk = true;
              } else {
                this.time.setError('La combinacion fecha y hora superan al ahora');
              }
            } else {
              this.time.setError('Formato de fecha inválido');
            }
          }
        } else {
          this.time.setError('El campo hora es requerido');
        }
      } else {
        isOk = true;
      }
      return isOk;
    },
    /**
     * Se encarga de verificar que el campo descripción sea correcto
     * @returns {boolean}
     */
    validateDescription() {
      let value = this.description.value;
      let isOk = false;

      if (value && value.length > 0) {
        if (value.length >= 3) {
          this.description.isOk();
          isOk = true;
        } else {
          this.description.setError('La descripción es muy pequeña');
        }
      } else {
        this.description.setError('El campo descrioción es requerido');
      }

      return isOk;
    },
    /**
     * Verifica que el cmapo del importe sea valido
     */
    validateAmount() {
      let value = this.amount.value;
      let isOk = false;

      if (value) {
        if (value > 0) {
          if (value < this.amount.max) {
            this.amount.isOk();
            isOk = true;
          } else {
            let max = window.formatCurrency(this.amount.max, 0);
            this.amount.setError(`EL campo importe debe ser menor que ${max}`);
          }
        } else {
          this.amount.setError('El importe debe ser mayor que cero (0)');
        }
      } else {
        this.amount.setError('El campo importe es requerido.')
      }

      return isOk;
    },
    /**
     * Se encarga de notificar a la vista los errores provenientes del
     * servidor
     */
    notifyErrors(errors) {
      for (const key in errors) {
        if (Object.hasOwnProperty.call(errors, key)) {
          const error = errors[key];
          if (Object.hasOwnProperty.call(this, key)) {
            this[key].setError(error);
          }
        }
      }
    },
  }//.end object
}

export default costForm;